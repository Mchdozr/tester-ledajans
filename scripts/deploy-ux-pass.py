#!/usr/bin/env python3
"""Tester görünüm geçişi: medya yedeği, sıra, teklif ve blog filtresi.

Hedef sabit: https://tester.ledajans.com
"""
from __future__ import annotations

import importlib.util
import json
import re
import sys
from pathlib import Path

import requests

ROOT = Path(__file__).resolve().parents[1]
spec = importlib.util.spec_from_file_location("eng", ROOT / "scripts/deploy-engineering-home.py")
eng = importlib.util.module_from_spec(spec)
spec.loader.exec_module(eng)

GUARD = "ldj-media-guard"
GUARD_CSS = """
.ledajans-project-image-wrap{position:relative;aspect-ratio:16/10;background:#171d2a;overflow:hidden}
.ledajans-project-image-wrap>video[hidden]{display:none!important}
.ledajans-project-image-wrap>img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}
.ledajans-project-actions{display:flex;gap:.75rem;align-items:center;flex-wrap:wrap}
.ledajans-project-actions .ldj-ux-quote{color:#c2410c;font-weight:800;text-decoration:none;min-height:44px;display:inline-flex;align-items:center}
"""
GUARD_JS = """
<script id="ldj-media-guard">
(function(){
  function label(wrap){
    if(wrap.querySelector(".ldj-ux-poster-label")) return;
    var cat=wrap.querySelector(".ledajans-project-category");
    var s=document.createElement("span");
    s.className="ldj-ux-poster-label";
    s.textContent=cat&&cat.textContent?cat.textContent:"Proje görseli";
    wrap.appendChild(s);
  }
  document.querySelectorAll(".ledajans-project-image-wrap").forEach(function(wrap){
    var video=wrap.querySelector("video");
    var img=wrap.querySelector("img");
    if(video){
      video.hidden=true;
      video.addEventListener("error", function(){ video.hidden=true; label(wrap); });
    }
    if(img){
      img.addEventListener("error", function(){ img.style.display="none"; label(wrap); });
      if(img.complete && img.naturalWidth===0){ img.style.display="none"; label(wrap); }
    }
  });
  document.querySelectorAll(".ledajans-project-body").forEach(function(body){
    if(body.querySelector(".ldj-ux-quote")) return;
    var btn=body.querySelector(".ledajans-project-link");
    if(!btn) return;
    var row=document.createElement("div");
    row.className="ledajans-project-actions";
    btn.parentNode.insertBefore(row, btn);
    row.appendChild(btn);
    var a=document.createElement("a");
    a.className="ldj-ux-quote";
    a.href="/iletisim/";
    a.textContent="Teklif al";
    row.appendChild(a);
  });
})();
</script>
"""


def patch_projects(html: str) -> str:
    if GUARD in html:
        return html
    if "</style>" in html:
        html = html.replace("</style>", GUARD_CSS + "\n</style>", 1)
    else:
        html = "<style>" + GUARD_CSS + "</style>\n" + html
    return html + "\n" + GUARD_JS


def kind(html: str) -> str:
    if 'id="ldj-cozum"' in html:
        return "cozum"
    if 'id="ldj-imza"' in html:
        return "imza"
    if 'id="ldj-metrikler"' in html:
        return "metrik"
    if 'id="ldj-mimari"' in html:
        return "mimari"
    if 'id="ldj-ihracat"' in html:
        return "ihracat"
    if "ledajans-hero" in html and "ledajans-projects-page" not in html:
        return "hero"
    if "ledajans-projects-page" in html:
        return "projects"
    if "ledajans-home-certs" in html:
        return "certs"
    if "ledajans-indoor" in html:
        return "indoor"
    if "ledajans-outdoor" in html or "ledajans-outdoor" in html:
        return "outdoor"
    if "products-slider" in html:
        return "products"
    if "ledajans-about" in html:
        return "about"
    if "ledajans-reviews" in html:
        return "reviews"
    if "ledajans-blog" in html and "ledajans-blog-track" in html:
        return "blog"
    return "other"


RANK = {
    "hero": 10,
    "cozum": 20,
    "imza": 30,
    "projects": 40,
    "indoor": 50,
    "outdoor": 60,
    "products": 70,
    "mimari": 80,
    "metrik": 90,
    "certs": 100,
    "ihracat": 110,
    "about": 120,
    "reviews": 130,
    "blog": 140,
    "other": 200,
}


def reorder(data: list) -> None:
    indexed = list(enumerate(data))
    indexed.sort(key=lambda pair: (RANK[kind(eng.section_html(pair[1]))], pair[0]))
    data[:] = [section for _, section in indexed]


def load_page(session: requests.Session, site: str, page_id: int) -> tuple[dict, list]:
    res = session.get(f"{site}/wp-json/wp/v2/pages/{page_id}", params={"context": "edit"}, timeout=90)
    res.raise_for_status()
    row = res.json()
    raw = (row.get("meta") or {}).get("_elementor_data")
    data = json.loads(raw) if isinstance(raw, str) else raw
    if not isinstance(data, list):
        raise SystemExit(f"elementor liste degil: {page_id}")
    return row, data


def save_page(session: requests.Session, site: str, page_id: int, data: list) -> None:
    res = session.post(
        f"{site}/wp-json/wp/v2/pages/{page_id}",
        json={"meta": {"_elementor_data": json.dumps(data, ensure_ascii=False, separators=(",", ":"))}},
        timeout=180,
    )
    print("kayit", page_id, res.status_code)
    if res.status_code not in (200, 201):
        raise SystemExit(res.text[:240])


def page_id_by_slug(session: requests.Session, site: str, slug: str) -> int:
    res = session.get(
        f"{site}/wp-json/wp/v2/pages",
        params={"slug": slug, "status": "any", "context": "edit"},
        timeout=60,
    )
    rows = res.json()
    if not rows:
        raise SystemExit("sayfa yok " + slug)
    return int(rows[0]["id"])


def patch_widget(data: list, marker: str, html: str) -> int:
    return eng.replace_marker(data, marker, html)


def main() -> int:
    site, user, pw = eng.load_env()
    print("host", site)
    if "tester.ledajans.com" not in site:
        return 1
    session = requests.Session()
    session.auth = (user, pw)
    session.headers.update({"User-Agent": "LEDAJANS-UX/1.0", "Content-Type": "application/json"})

    home_id = 1248
    _, home = load_page(session, site, home_id)
    changed = 0

    def on_home(node) -> int:
        nonlocal changed
        if node.get("widgetType") != "html":
            return 0
        settings = node.get("settings") or {}
        html = settings.get("html")
        if not isinstance(html, str) or "ledajans-projects-page" not in html:
            return 0
        new = patch_projects(html)
        if new != html:
            settings["html"] = new
            node["settings"] = settings
            changed += 1
        return 0

    eng.walk(home, on_home)
    print("proje_yama", changed)

    if eng.find_root(home, 'id="ldj-cozum"') < 0:
        home.insert(1, eng.make_section((ROOT / "Anasayfa/widget-cozum.html").read_text(encoding="utf-8")))
        print("cozum eklendi")
    imza = (ROOT / "Anasayfa/widget-imza-projeler.html").read_text(encoding="utf-8")
    print("imza", patch_widget(home, 'id="ldj-imza"', imza))
    reorder(home)
    print("sira", [kind(eng.section_html(sec)) for sec in home])
    save_page(session, site, home_id, home)

    projeler_id = page_id_by_slug(session, site, "projeler")
    _, proj = load_page(session, site, projeler_id)
    pchanged = 0

    def on_proj(node) -> int:
        nonlocal pchanged
        if node.get("widgetType") != "html":
            return 0
        settings = node.get("settings") or {}
        html = settings.get("html")
        if not isinstance(html, str) or "ledajans-project-card" not in html:
            return 0
        new = patch_projects(html)
        if new != html:
            settings["html"] = new
            node["settings"] = settings
            pchanged += 1
        return 0

    eng.walk(proj, on_proj)
    print("projeler_yama", pchanged)
    if pchanged:
        save_page(session, site, projeler_id, proj)

    product_id = page_id_by_slug(session, site, "led-ekran")
    _, product = load_page(session, site, product_id)
    product_html = (ROOT / "LED Ekran/led-ekran.html").read_text(encoding="utf-8")
    print("urun", patch_widget(product, "la-gradient-title", product_html))
    save_page(session, site, product_id, product)

    contact_id = page_id_by_slug(session, site, "iletisim")
    _, contact = load_page(session, site, contact_id)
    print("iletisim", patch_widget(contact, "ledajans-iletisim-form", (ROOT / "İletisim/body.html").read_text(encoding="utf-8")))
    save_page(session, site, contact_id, contact)

    tool_id = page_id_by_slug(session, site, "proje-mimari")
    tool_html = eng.inline_js((ROOT / "Muhendislik/proje-mimari.html").read_text(encoding="utf-8"))
    _, tool = load_page(session, site, tool_id)
    n = patch_widget(tool, "data-ldj-mimari", tool_html)
    print("mimari", n)
    if n == 0:
        tool[:] = [eng.make_section(tool_html)]
    save_page(session, site, tool_id, tool)

    purged = session.delete(f"{site}/wp-json/elementor/v1/cache", timeout=60)
    print("cache", purged.status_code)
    return 0


if __name__ == "__main__":
    sys.exit(main())
