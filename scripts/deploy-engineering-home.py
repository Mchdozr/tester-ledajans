#!/usr/bin/env python3
"""Mühendislik vitrinini yalnız tester.ledajans.com'a ekler.

Anasayfa 1248: imza, metrik, proje mimarı, ihracat section'ları.
Üç yeni sayfa yayınlanır. İletişim ve sertifika widget'ı varsa güncellenir.
Menü / footer HTML widget'ında Firma Bilgilerimiz geçiyorsa link eklenir.
"""
from __future__ import annotations

import json
import os
import re
import secrets
import sys
from pathlib import Path
from urllib.parse import urlparse

import requests

ROOT = Path(__file__).resolve().parents[1]
PAGE_ID = 1248
UA = "LEDAJANS-Deploy-Engineering/1.0"
SCRIPT_SRC = '<script src="/wp-content/themes/modins_child/assets/js/ledajans-proje-mimari.js" defer></script>'
JS_FILE = ROOT / "wp-content/themes/modins_child/assets/js/ledajans-proje-mimari.js"

HOME_SECTIONS = [
    ("id=\"ldj-imza\"", ROOT / "Anasayfa/widget-imza-projeler.html", ["ledajans-hero"]),
    ("id=\"ldj-metrikler\"", ROOT / "Anasayfa/widget-metrikler.html", ["ledajans-projects-page"]),
    ("id=\"ldj-mimari\"", ROOT / "Anasayfa/widget-proje-mimari.html", ["ldj-metrikler", "ledajans-projects-page"]),
    ("id=\"ldj-ihracat\"", ROOT / "Anasayfa/widget-ihracat.html", ["ledajans-home-certs"]),
]

PAGES = [
    (ROOT / "Kurumsal/Imza-Projeler/imza-projeler.html", "imza-projeler", "İmza LED Ekran Projeleri", "LED ekran referans projeleri"),
    (ROOT / "Muhendislik/muhendislik-metrikleri.html", "muhendislik-metrikleri", "Mühendislik Metrikleri", "LED ekran teknik değerleri"),
    (ROOT / "Muhendislik/proje-mimari.html", "proje-mimari", "LED Ekran Proje Mimarı", "LED ekran pitch hesaplama"),
]

MENU_RE = re.compile(
    r'<li><a href="[^"]*firma-bilgilerimiz/?">(?:<span class="menu-title">)?Firma Bilgilerimiz(?:</span>)?</a></li>'
)
FOOTER_RE = re.compile(
    r'<li><a href="[^"]*hakkimizda/" class="ledajans-footer-link">Hakkımızda</a></li>'
)


def load_env() -> tuple[str, str, str]:
    data: dict[str, str] = {}
    env_path = ROOT / ".env"
    if env_path.is_file():
        for line in env_path.read_text(encoding="utf-8-sig").splitlines():
            line = line.strip()
            if not line or line.startswith("#") or "=" not in line:
                continue
            key, _, value = line.partition("=")
            data[key.strip()] = value.strip().strip("\"'")
    for key in ("WP_SITE_URL", "WP_USERNAME", "WP_APP_PASSWORD"):
        if os.environ.get(key):
            data[key] = os.environ[key]
    # Bu script üretim adresini yok sayar. Hedef sabittir.
    return (
        "https://tester.ledajans.com",
        data.get("WP_USERNAME", ""),
        data.get("WP_APP_PASSWORD", "").replace(" ", ""),
    )


def inline_js(html: str) -> str:
    if SCRIPT_SRC not in html:
        return html
    js = JS_FILE.read_text(encoding="utf-8")
    if "</script>" in js.lower():
        raise SystemExit("HATA: JS içinde script kapanışı var")
    return html.replace(SCRIPT_SRC, "<script>\n" + js + "\n</script>")


def eid() -> str:
    return secrets.token_hex(4)[:7]


def make_section(html: str) -> dict:
    return {
        "id": eid(),
        "elType": "section",
        "settings": {"layout": "full_width", "gap": "no", "content_width": "full"},
        "elements": [
            {
                "id": eid(),
                "elType": "column",
                "settings": {"_column_size": 100},
                "elements": [
                    {
                        "id": eid(),
                        "elType": "widget",
                        "widgetType": "html",
                        "settings": {"html": html},
                        "elements": [],
                    }
                ],
            }
        ],
    }


def walk(nodes, fn) -> int:
    changed = 0
    if isinstance(nodes, list):
        for node in nodes:
            changed += walk(node, fn)
        return changed
    if not isinstance(nodes, dict):
        return 0
    changed += fn(nodes)
    for key in ("elements", "content"):
        if key in nodes:
            changed += walk(nodes[key], fn)
    return changed


def replace_marker(nodes, marker: str, html: str) -> int:
    def fn(node) -> int:
        if node.get("widgetType") != "html":
            return 0
        settings = node.get("settings") or {}
        current = settings.get("html")
        if isinstance(current, str) and marker in current:
            settings["html"] = html
            node["settings"] = settings
            return 1
        return 0

    return walk(nodes, fn)


def patch_nav_html(html: str) -> tuple[str, int]:
    count = 0

    def menu_repl(match: re.Match) -> str:
        nonlocal count
        if "imza-projeler" in html:
            return match.group(0)
        count += 1
        if "menu-title" in match.group(0):
            extra = (
                '<li><a href="/imza-projeler/"><span class="menu-title">İmza Projeler</span></a></li>'
                '<li><a href="/muhendislik-metrikleri/"><span class="menu-title">Mühendislik Metrikleri</span></a></li>'
                '<li><a href="/proje-mimari/"><span class="menu-title">Proje Mimarı</span></a></li>'
            )
        else:
            extra = (
                '<li><a href="/imza-projeler/">İmza Projeler</a></li>'
                '<li><a href="/muhendislik-metrikleri/">Mühendislik Metrikleri</a></li>'
                '<li><a href="/proje-mimari/">Proje Mimarı</a></li>'
            )
        return match.group(0) + extra

    html = MENU_RE.sub(menu_repl, html)
    if "ledajans-footer-links" in html and "imza-projeler" not in html:

        def foot_repl(match: re.Match) -> str:
            nonlocal count
            count += 1
            return match.group(0) + (
                '<li><a href="/imza-projeler/" class="ledajans-footer-link">İmza Projeler</a></li>'
                '<li><a href="/muhendislik-metrikleri/" class="ledajans-footer-link">Mühendislik Metrikleri</a></li>'
                '<li><a href="/proje-mimari/" class="ledajans-footer-link">Proje Mimarı</a></li>'
            )

        html = FOOTER_RE.sub(foot_repl, html, count=1)
    return html, count


def section_html(section) -> str:
    parts: list[str] = []

    def fn(node) -> int:
        if node.get("widgetType") == "html":
            current = (node.get("settings") or {}).get("html")
            if isinstance(current, str):
                parts.append(current)
        return 0

    walk(section, fn)
    return "\n".join(parts)


def find_root(data: list, needle: str) -> int:
    for i, section in enumerate(data):
        if needle in section_html(section):
            return i
    return -1


def dedupe_sections(data: list) -> int:
    """İkinci çalıştırmada aynı imza bölümünü tekrar eklemeyi geri alır."""
    seen: set[str] = set()
    removed = 0
    keep = []
    markers = ("id=\"ldj-imza\"", "id=\"ldj-ihracat\"", "id=\"ldj-metrikler\"", "id=\"ldj-mimari\"")
    for section in data:
        html = section_html(section)
        kind = next((marker for marker in markers if marker in html), "")
        if kind and kind in seen:
            removed += 1
            continue
        if kind:
            seen.add(kind)
        keep.append(section)
    data[:] = keep
    return removed


def ensure_section(data: list, marker: str, html: str, anchors: list[str]) -> bool:
    if find_root(data, marker) >= 0:
        n = replace_marker(data, marker, html)
        print("guncellendi", marker, n)
        return n > 0
    idx = -1
    for anchor in anchors:
        idx = find_root(data, anchor)
        if idx >= 0:
            break
    if idx < 0:
        print("HATA: çapa yok", marker, anchors)
        return False
    data.insert(idx + 1, make_section(html))
    print("eklendi", marker, "sonra", idx, anchors[0])
    return True


def meta_description(html: str) -> str:
    match = re.search(r"<!-- SEO Meta Description:\s*(.+?)\s*-->", html)
    return match.group(1).strip() if match else ""


def main() -> int:
    dry = "--dry-run" in sys.argv
    site, user, pw = load_env()
    host = (urlparse(site).hostname or "").lower()
    print("host", host or "(bos)")
    if host != "tester.ledajans.com":
        print("HATA: yalniz tester.ledajans.com")
        return 1
    if not user or not pw:
        print("HATA: .env kimlik yok")
        return 1
    if not JS_FILE.is_file():
        print("HATA: mimari js yok")
        return 1

    auth = (user, pw)
    headers = {"User-Agent": UA, "Content-Type": "application/json"}
    session = requests.Session()
    session.auth = auth
    session.headers.update(headers)

    home = session.get(
        f"{site}/wp-json/wp/v2/pages/{PAGE_ID}",
        params={"context": "edit"},
        timeout=90,
    )
    print("anasayfa", home.status_code)
    if home.status_code != 200:
        print(home.text[:300])
        return 1
    raw = (home.json().get("meta") or {}).get("_elementor_data")
    data = json.loads(raw) if isinstance(raw, str) else raw
    if not isinstance(data, list):
        print("HATA: elementor kok liste degil")
        return 1

    print("mukerrer_silindi", dedupe_sections(data), "kalan", len(data))

    ok = True
    for marker, path, anchors in HOME_SECTIONS:
        html = inline_js(path.read_text(encoding="utf-8"))
        if marker not in html:
            print("HATA: marker dosyada yok", path.name)
            ok = False
            continue
        ok = ensure_section(data, marker, html, anchors) and ok
    if not ok:
        return 1

    if dry:
        print("DRY_RUN anasayfa yazilmadi")
    else:
        saved = session.post(
            f"{site}/wp-json/wp/v2/pages/{PAGE_ID}",
            json={"meta": {"_elementor_data": json.dumps(data, ensure_ascii=False, separators=(",", ":"))}},
            timeout=180,
        )
        print("anasayfa_kayit", saved.status_code, saved.text[:220])
        if saved.status_code not in (200, 201):
            return 1

    for path, slug, title, focus in PAGES:
        html = inline_js(path.read_text(encoding="utf-8"))
        found = session.get(
            f"{site}/wp-json/wp/v2/pages",
            params={"slug": slug, "status": "any", "context": "edit"},
            timeout=60,
        )
        rows = found.json() if found.status_code == 200 else []
        existing = rows[0] if isinstance(rows, list) and rows else None
        # post_content wpautop betiği böler. Sayfa Elementor HTML widget olarak durur.
        payload = {
            "title": title,
            "slug": slug,
            "status": "publish",
            "meta": {
                "_elementor_edit_mode": "builder",
                "_elementor_template_type": "wp-page",
                "_elementor_data": json.dumps([make_section(html)], ensure_ascii=False, separators=(",", ":")),
                "rank_math_title": title,
                "rank_math_description": meta_description(html),
                "rank_math_focus_keyword": focus,
            },
        }
        if dry:
            print("DRY_RUN sayfa", slug, "var" if existing else "yeni")
            continue
        if existing:
            res = session.post(f"{site}/wp-json/wp/v2/pages/{existing['id']}", json=payload, timeout=120)
        else:
            res = session.post(f"{site}/wp-json/wp/v2/pages", json=payload, timeout=120)
        if res.status_code not in (200, 201):
            for key in ("rank_math_title", "rank_math_description", "rank_math_focus_keyword"):
                payload.get("meta", {}).pop(key, None)
            if existing:
                res = session.post(f"{site}/wp-json/wp/v2/pages/{existing['id']}", json=payload, timeout=120)
            else:
                res = session.post(f"{site}/wp-json/wp/v2/pages", json=payload, timeout=120)
        print("sayfa", slug, res.status_code, (res.json() or {}).get("link", res.text[:180]) if res.headers.get("content-type", "").startswith("application/json") else res.text[:180])
        if res.status_code not in (200, 201):
            return 1

    patch_known_pages(session, site, dry)
    patch_chrome(session, site, dry)

    if not dry:
        purged = session.delete(f"{site}/wp-json/elementor/v1/cache", timeout=60)
        print("elementor_cache", purged.status_code, purged.text[:160])
    return 0


def patch_known_pages(session: requests.Session, site: str, dry: bool) -> None:
    jobs = [
        ("iletisim", "ledajans-iletisim-form", ROOT / "İletisim/body.html"),
        ("sertifikalarimiz", "ledajans-cert-page", ROOT / "Kurumsal/Sertifikalarimiz/sertifikalarimiz.html"),
    ]
    for slug, marker, path in jobs:
        html = path.read_text(encoding="utf-8")
        found = session.get(
            f"{site}/wp-json/wp/v2/pages",
            params={"slug": slug, "status": "any", "context": "edit"},
            timeout=90,
        )
        rows = found.json() if found.status_code == 200 else []
        if not rows:
            print("ELLE", slug, "sayfa yok")
            continue
        row = rows[0]
        raw = (row.get("meta") or {}).get("_elementor_data")
        elementor = json.loads(raw) if isinstance(raw, str) and raw else raw
        payload = {}
        if isinstance(elementor, (list, dict)):
            n = replace_marker(elementor, marker, html)
            print(slug, "widget", n)
            if n:
                payload["meta"] = {"_elementor_data": json.dumps(elementor, ensure_ascii=False, separators=(",", ":"))}
        content = row.get("content") or {}
        raw_content = content.get("raw") if isinstance(content, dict) else ""
        if isinstance(raw_content, str) and marker in raw_content:
            payload["content"] = html
        if not payload:
            print("ELLE", slug, "marker canli icerikte yok")
            continue
        if dry:
            print("DRY_RUN", slug)
            continue
        res = session.post(f"{site}/wp-json/wp/v2/pages/{row['id']}", json=payload, timeout=180)
        print(slug, "kayit", res.status_code)


def patch_chrome(session: requests.Session, site: str, dry: bool) -> None:
    patched = 0
    for rest_base in ("elementor_library", "pages"):
        page = 1
        while page <= 6:
            res = session.get(
                f"{site}/wp-json/wp/v2/{rest_base}",
                params={"context": "edit", "per_page": 20, "page": page, "status": "any"},
                timeout=90,
            )
            if res.status_code != 200:
                print("liste", rest_base, res.status_code)
                break
            rows = res.json()
            if not isinstance(rows, list) or not rows:
                break
            for row in rows:
                raw = (row.get("meta") or {}).get("_elementor_data")
                if not raw:
                    continue
                try:
                    elementor = json.loads(raw) if isinstance(raw, str) else raw
                except json.JSONDecodeError:
                    continue
                changed = 0

                def fn(node, elementor=elementor) -> int:
                    nonlocal changed
                    if node.get("widgetType") != "html":
                        return 0
                    settings = node.get("settings") or {}
                    current = settings.get("html")
                    if not isinstance(current, str):
                        return 0
                    if "firma-bilgilerimiz" not in current and "ledajans-footer-links" not in current:
                        return 0
                    if "imza-projeler" in current:
                        return 0
                    updated, count = patch_nav_html(current)
                    if count:
                        settings["html"] = updated
                        node["settings"] = settings
                        changed += count
                    return 0

                walk(elementor, fn)
                if not changed:
                    continue
                patched += changed
                print("menu_footer", rest_base, row.get("id"), row.get("slug"), changed)
                if dry:
                    continue
                saved = session.post(
                    f"{site}/wp-json/wp/v2/{rest_base}/{row['id']}",
                    json={"meta": {"_elementor_data": json.dumps(elementor, ensure_ascii=False, separators=(",", ":"))}},
                    timeout=180,
                )
                print("menu_kayit", saved.status_code)
            if len(rows) < 20:
                break
            page += 1
    if patched == 0:
        print("ELLE: header/footer HTML widget bulunamadi. Ust-Menu/ust-menu.html ve Footer/footer.html repoda guncel.")
    try_menu_items(session, site, dry)


def try_menu_items(session: requests.Session, site: str, dry: bool) -> None:
    res = session.get(
        f"{site}/wp-json/wp/v2/menu-items",
        params={"per_page": 100, "context": "edit"},
        timeout=60,
    )
    print("menu_items", res.status_code)
    if res.status_code != 200 or not isinstance(res.json(), list):
        return
    rows = res.json()
    if any("imza-projeler" in json.dumps(row, ensure_ascii=False) for row in rows):
        print("menu_items zaten var")
        return
    parent = next((row for row in rows if row.get("title", {}).get("rendered") == "Kurumsal"), None)
    if not parent:
        print("menu_items Kurumsal yok")
        return
    menus = parent.get("menus")
    parent_id = parent.get("id")
    for title, url in (
        ("İmza Projeler", "/imza-projeler/"),
        ("Mühendislik Metrikleri", "/muhendislik-metrikleri/"),
        ("Proje Mimarı", "/proje-mimari/"),
    ):
        payload = {
            "title": title,
            "url": site + url,
            "status": "publish",
            "parent": parent_id,
            "menus": menus,
            "type": "custom",
        }
        if dry:
            print("DRY_RUN menu", title)
            continue
        made = session.post(f"{site}/wp-json/wp/v2/menu-items", json=payload, timeout=60)
        print("menu_ek", title, made.status_code)


if __name__ == "__main__":
    sys.exit(main())
