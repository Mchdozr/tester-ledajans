#!/usr/bin/env python3
"""Anasayfa vitrin çerçevelerini yalnız tester.ledajans.com'a yazar.

Sayfa 1248 içindeki imza, metrik, proje mimarı, ihracat ve çift çözüm
widget'ını günceller. Başka sayfaya dokunmaz.
"""
from __future__ import annotations

import importlib.util
import json
import sys
from pathlib import Path
from urllib.parse import urlparse

import requests

ROOT = Path(__file__).resolve().parents[1]
PAGE_ID = 1248
SITE = "https://tester.ledajans.com"

spec = importlib.util.spec_from_file_location("eng", ROOT / "scripts/deploy-engineering-home.py")
eng = importlib.util.module_from_spec(spec)
spec.loader.exec_module(eng)

WIDGETS = [
    ("id=\"ldj-cozum\"", ROOT / "Anasayfa/widget-cozum.html"),
    ("id=\"ldj-imza\"", ROOT / "Anasayfa/widget-imza-projeler.html"),
    ("id=\"ldj-metrikler\"", ROOT / "Anasayfa/widget-metrikler.html"),
    ("id=\"ldj-mimari\"", ROOT / "Anasayfa/widget-proje-mimari.html"),
    ("id=\"ldj-ihracat\"", ROOT / "Anasayfa/widget-ihracat.html"),
]


def main() -> int:
    dry = "--dry-run" in sys.argv
    site, user, password = eng.load_env()
    host = (urlparse(site).hostname or "").lower()
    if site != SITE or host != "tester.ledajans.com":
        print("HATA: yalniz tester")
        return 1
    if not user or not password:
        print("HATA: kimlik yok")
        return 1

    session = requests.Session()
    session.auth = (user, password)
    session.headers.update({"User-Agent": "LEDAJANS-Vitrin-Panel/1.0", "Content-Type": "application/json"})

    home = session.get(f"{SITE}/wp-json/wp/v2/pages/{PAGE_ID}", params={"context": "edit"}, timeout=90)
    print("anasayfa", home.status_code)
    if home.status_code != 200:
        print(home.text[:240])
        return 1
    raw = (home.json().get("meta") or {}).get("_elementor_data")
    data = json.loads(raw) if isinstance(raw, str) else raw
    if not isinstance(data, list):
        print("HATA: elementor kok liste degil")
        return 1

    for marker, path in WIDGETS:
        html = eng.inline_js(path.read_text(encoding="utf-8"))
        if marker not in html:
            print("HATA: marker yok", path.name)
            return 1
        count = eng.replace_marker(data, marker, html)
        print("widget", path.name, count, "bayt", len(html))
        if count != 1:
            print("HATA: beklenen tek widget", marker)
            return 1

    if dry:
        print("DRY_RUN yazilmadi")
        return 0

    saved = session.post(
        f"{SITE}/wp-json/wp/v2/pages/{PAGE_ID}",
        json={"meta": {"_elementor_data": json.dumps(data, ensure_ascii=False, separators=(",", ":"))}},
        timeout=180,
    )
    print("kayit", saved.status_code)
    if saved.status_code not in (200, 201):
        print(saved.text[:300])
        return 1
    purged = session.delete(f"{SITE}/wp-json/elementor/v1/cache", timeout=60)
    print("elementor_cache", purged.status_code, purged.text[:160])
    return 0 if purged.status_code in (200, 201, 204) else 1


if __name__ == "__main__":
    sys.exit(main())
