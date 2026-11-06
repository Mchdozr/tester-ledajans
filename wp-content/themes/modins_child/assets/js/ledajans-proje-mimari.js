/* LEDAJANS proje mimarı + metrik panosu
 * Değerler yayınlanmış ürün tablolarındandır; bağımsız laboratuvar raporu değildir.
 * Kaynaklar: Cob-Smart-Screen, GOB hub, Tayor, OFC, dış mekan RGB, Grand Air, 2026 fiyat rehberleri.
 */
(function (root) {
  "use strict";

  var INDOOR = [0.93, 1.25, 1.53, 1.56, 1.86, 1.87, 2.5];
  var OUTDOOR = [2.5, 3.076, 4, 4.4, 5, 5.71, 6.6, 6.67, 8, 10];
  var RENTAL_IN = [1.953, 2.604, 2.976, 3.91];
  var RENTAL_OUT = [2.604, 2.976, 3.91];

  var FAMILIES = {
    cob: {
      label: "İç mekan COB",
      pitchLabel: "1,25 – 1,87 mm",
      refreshLabel: "≥3840 Hz",
      refreshMin: 3840,
      brightLabel: "0–1000 cd/m²",
      brightTop: 1000,
      ip: "Ön bakım kabin",
      powerLabel: "140 / 300 W/m²",
      powerAvg: [140, 140],
      powerMax: [300, 300],
      powerNote: "P1.56 CFC156 tablosu. Diğer COB pitch satırlarında aynı watt değeri yok.",
      lifeLabel: "Şemada ≥100.000 saat",
      lifePct: 100,
      barNote: "Ömür iç mekan LED ekran ürün şemasından.",
      source: "COB model kartları ve P1.56 CFC156 tablosu. Ömür: iç mekan LED ekran ürün şeması."
    },
    gob: {
      label: "İç mekan GOB",
      pitchLabel: "0,93 – 2,5 mm",
      refreshLabel: "≥3840 / ≥6000 Hz",
      refreshMin: 6000,
      brightLabel: "Nit satırı yok",
      brightTop: 0,
      ip: "IP65",
      powerLabel: "W/m² satırı yok",
      powerAvg: null,
      powerMax: null,
      powerNote: "GOB hub kartlarında güç tüketimi yayınlanmıyor.",
      lifeLabel: "Ömür satırı yok",
      lifePct: 0,
      barNote: "Hub kartında ömür ve nit alanı yok.",
      source: "İç mekan RGB GOB panel kartları: P0.93, P1.25, P1.53, P1.86, P2.5."
    },
    outdoor: {
      label: "Dış mekan",
      pitchLabel: "2,5 – 10 mm",
      refreshLabel: "≥3840 Hz · H serisi ≥7680 Hz",
      refreshMin: 7680,
      brightLabel: "4500–8000 cd/m²",
      brightTop: 8000,
      ip: "IP65",
      powerLabel: "280–308 / 560–598 W/m²",
      powerAvg: [280, 308],
      powerMax: [560, 598],
      powerNote: "Tayor serisi, modele göre. OFC ve H serisi güç satırı bu aralık değildir.",
      lifeLabel: "≥100.000 saat",
      lifePct: 100,
      barNote: "Ömür dış mekan LED ekran ürün şemasından. Parlaklık dış mekan SSS ve Tayor.",
      source: "Tayor güç ve parlaklık, OFC ≥3840 Hz, H2.5–H5 ≥7680 Hz ve ≥4500 cd/m², IP65."
    },
    rental: {
      label: "Rental",
      pitchLabel: "1,95 – 3,91 mm",
      refreshLabel: "≥7680 Hz",
      refreshMin: 7680,
      brightLabel: "İç ≥600 · dış ≥2500–4000 cd/m²",
      brightTop: 4000,
      ip: "Dış Grand Air IP65",
      powerLabel: "İç 173–213 · dış 187–220 W/m² ort.",
      powerAvg: [173, 220],
      powerMax: [520, 660],
      powerNote: "Grand Air iç ve dış seri zarfı. Pitch’e göre ara değerler sayfada tek tek yazılı.",
      lifeLabel: "≥100.000 saat",
      lifePct: 100,
      barNote: "Ömür rental ürün şemasından. Parlaklık Grand Air tablosu.",
      source: "Grand Air iç/dış: 500×500 mm, ≥7680 Hz. Dış parlaklık ≥2500 / ≥3000 / ≥4000 cd/m²."
    }
  };

  function round3(n) {
    return Math.round(n * 1000) / 1000;
  }

  function formatPitch(n) {
    var s = String(round3(n)).replace(".", ",");
    return "P" + s;
  }

  function recommend(distance, pitches) {
    var sorted = pitches.slice().sort(function (a, b) { return a - b; });
    var ok = sorted.filter(function (p) { return p <= distance + 0.001; });
    var best = ok.length ? ok[ok.length - 1] : sorted[0];
    var i = sorted.indexOf(best);
    return {
      best: best,
      finer: i > 0 ? sorted[i - 1] : null,
      coarser: i < sorted.length - 1 ? sorted[i + 1] : null,
      list: sorted
    };
  }

  function bandFor(pitch) {
    if (pitch <= 1.8) {
      return { name: "En üst segment", note: "2026 iç mekan rehberi: P1,25–P1,8 göreli bandı." };
    }
    if (pitch <= 3.2) {
      return { name: "Orta–üst", note: "2026 iç mekan rehberi: P2–P3 göreli bandı." };
    }
    if (pitch <= 5) {
      return { name: "Orta", note: "2026 iç mekan rehberi: P4–P5 göreli bandı." };
    }
    return { name: "Geniş alan", note: "Bu pitch için 2026 rehberinde ayrı TL bandı yok." };
  }

  function profileKey(ortam, kullanim) {
    if (kullanim === "rental") return ortam === "ic" ? "rentalIn" : "rentalOut";
    if (ortam === "ic") return "indoor";
    return "outdoor";
  }

  function pitchesFor(key) {
    if (key === "rentalIn") return RENTAL_IN;
    if (key === "rentalOut") return RENTAL_OUT;
    if (key === "indoor") return INDOOR;
    return OUTDOOR;
  }

  function num(value, fallback) {
    var n = parseFloat(String(value).replace(",", "."));
    return isFinite(n) ? n : fallback;
  }

  function clamp(n, a, b) {
    return Math.min(b, Math.max(a, n));
  }

  function trNum(n, digits) {
    return n.toLocaleString("tr-TR", { maximumFractionDigits: digits, minimumFractionDigits: digits });
  }

  function moduleSpec(key, pitch) {
    if (key === "rentalIn" || key === "rentalOut") {
      return { w: 500, h: 500, note: "Grand Air kabin örneği 500×500 mm. 500×1000 seçeneği de yayında." };
    }
    if (key === "outdoor") {
      return { w: 960, h: 960, note: "Tayor kabin örneği 960×960 mm. RGB panel ebatı modele göre değişir." };
    }
    if (pitch === 1.25 || pitch === 1.56 || pitch === 1.87) {
      return { w: 600, h: 337.5, note: "COB kabin örneği 600×337,5 mm (P1.56 boş kabin ölçüsü)." };
    }
    return null;
  }

  function powerSpec(key, pitch) {
    if (key === "rentalIn") {
      return { avg: [173, 213], max: [520, 640], note: "Grand Air iç mekan, pitch’e göre ara değerler." };
    }
    if (key === "rentalOut") {
      return { avg: [187, 220], max: [560, 660], note: "Grand Air dış mekan, pitch’e göre ara değerler." };
    }
    if (key === "outdoor") {
      return { avg: [280, 308], max: [560, 598], note: "Tayor serisi aralığı. Diğer dış mekan serilerine taşınmaz." };
    }
    if (pitch === 1.56) {
      return { avg: [140, 140], max: [300, 300], note: "Yalnız P1.56 CFC156 tablosu." };
    }
    return null;
  }

  function watts(range, area) {
    return [range[0] * area, range[1] * area];
  }

  function cellSize(pitch, distance) {
    var ratio = pitch / Math.max(distance, 0.35);
    return Math.max(4, Math.min(28, ratio * 16));
  }

  function paint(canvas, pitch, distance, light) {
    if (!canvas || !canvas.getContext) return;
    var dpr = Math.min((root.devicePixelRatio || 1), 2);
    var w = canvas.clientWidth || 320;
    var h = canvas.clientHeight || 180;
    canvas.width = Math.max(1, Math.round(w * dpr));
    canvas.height = Math.max(1, Math.round(h * dpr));
    var ctx = canvas.getContext("2d");
    var off = root.document.createElement("canvas");
    off.width = canvas.width;
    off.height = canvas.height;
    var o = off.getContext("2d");
    o.scale(dpr, dpr);
    o.fillStyle = "#ffffff";
    o.font = "800 " + Math.round(Math.min(h * 0.46, w / 3.2)) + "px system-ui,sans-serif";
    o.textAlign = "center";
    o.textBaseline = "middle";
    o.fillText("LED", w / 2, h / 2);
    var img = o.getImageData(0, 0, canvas.width, canvas.height);
    var cell = cellSize(pitch, distance);
    var contrast = light === "karsi" ? 0.42 : light === "orta" ? 0.72 : 1;
    var wash = light === "karsi" ? 96 : light === "orta" ? 42 : 0;
    ctx.setTransform(1, 0, 0, 1, 0, 0);
    ctx.fillStyle = "#0f172a";
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    var y, x, sx, sy, i, on, r, g, b;
    for (y = 0; y < h; y += cell) {
      for (x = 0; x < w; x += cell) {
        sx = Math.min(canvas.width - 1, Math.floor((x + cell / 2) * dpr));
        sy = Math.min(canvas.height - 1, Math.floor((y + cell / 2) * dpr));
        i = (sy * canvas.width + sx) * 4;
        on = img.data[i] > 140;
        if (on) {
          r = Math.round(244 * contrast + wash);
          g = Math.round(111 * contrast + wash);
          b = Math.round(44 * contrast + wash * 0.6);
        } else {
          r = Math.round(30 * contrast + wash * 0.35);
          g = Math.round(41 * contrast + wash * 0.35);
          b = Math.round(59 * contrast + wash * 0.45);
        }
        ctx.fillStyle = "rgb(" + r + "," + g + "," + b + ")";
        ctx.fillRect(x + 0.5, y + 0.5, Math.max(1, cell - 1.2), Math.max(1, cell - 1.2));
      }
    }
  }

  function readState(rootEl) {
    var ortam = (rootEl.querySelector("[data-in='ortam']") || {}).value || "dis";
    var kullanim = (rootEl.querySelector("[data-in='kullanim']") || {}).value || "cephe";
    var light = (rootEl.querySelector("[data-in='isik']") || {}).value || "orta";
    if (ortam === "gunes") kullanim = kullanim === "rental" ? "rental" : kullanim;
    return {
      gen: clamp(num((rootEl.querySelector("[data-in='gen']") || {}).value, 4), 0.4, 40),
      yuk: clamp(num((rootEl.querySelector("[data-in='yuk']") || {}).value, 2), 0.3, 20),
      mesafe: clamp(num((rootEl.querySelector("[data-in='mesafe']") || {}).value, 3), 0.5, 80),
      ortam: ortam,
      kullanim: kullanim,
      isik: light
    };
  }

  function compute(state) {
    var key = profileKey(state.ortam, state.kullanim);
    var rec = recommend(state.mesafe, pitchesFor(key));
    var pair = rec.finer != null ? rec.finer : rec.coarser;
    var area = state.gen * state.yuk;
    var mod = moduleSpec(key, rec.best);
    var cols = mod ? Math.ceil((state.gen * 1000) / mod.w) : null;
    var rows = mod ? Math.ceil((state.yuk * 1000) / mod.h) : null;
    var power = powerSpec(key, rec.best);
    var band = bandFor(rec.best);
    var rental = null;
    if (state.kullanim === "rental") {
      var tl = state.ortam === "ic" ? [1500, 3000] : [1600, 4000];
      rental = {
        low: area * tl[0],
        high: area * tl[1],
        unit: tl,
        note: "2026 kiralama rehberindeki konuşulan m²/gün bandı. Teklif değildir."
      };
    }
    var bright = state.ortam === "ic"
      ? "İç mekan: COB 0–1000 cd/m² veya iç mekan şemasında ≥800 cd/m²."
      : "Dış mekan ve güneş: 4500–8000 cd/m² sınıfı, Tayor’da 5000–8000 cd/m².";
    return {
      key: key,
      rec: rec,
      pair: pair,
      area: area,
      cols: cols,
      rows: rows,
      mod: mod,
      power: power,
      band: band,
      rental: rental,
      bright: bright,
      state: state
    };
  }

  function setText(el, text) {
    if (el) el.textContent = text;
  }

  function render(rootEl) {
    var state = readState(rootEl);
    var out = compute(state);
    var pairLabel = out.pair == null ? "tek seçenek" : formatPitch(out.pair);
    var pairRole = out.rec.finer != null ? "bir kademe ince" : "bir kademe kaba";
    setText(rootEl.querySelector("[data-out='pitch']"), formatPitch(out.rec.best));
    setText(rootEl.querySelector("[data-out='alt']"), pairLabel + " · " + pairRole);
    setText(rootEl.querySelector("[data-out='rule']"), "Konfor mesafesi yaklaşık pitch (mm) kadar metredir. " + trNum(state.mesafe, 1) + " m için ekonomisi yeten en kaba pitch seçildi.");
    setText(rootEl.querySelector("[data-out='area']"), trNum(out.area, 2) + " m²");
    if (out.mod && out.cols) {
      setText(rootEl.querySelector("[data-out='modules']"), String(out.cols * out.rows) + " kabin (" + out.cols + "×" + out.rows + ")");
      setText(rootEl.querySelector("[data-out='moduleNote']"), out.mod.note + " Fire ve özel kesim teklifte netleşir.");
    } else {
      setText(rootEl.querySelector("[data-out='modules']"), "Ebat model sayfasında");
      setText(rootEl.querySelector("[data-out='moduleNote']"), "Bu pitch için hub kartında kabin ölçüsü yok. Alan hesabı duruyor.");
    }
    if (out.power) {
      var avg = watts(out.power.avg, out.area);
      var max = watts(out.power.max, out.area);
      setText(rootEl.querySelector("[data-out='power']"), "Ort. " + trNum(avg[0], 0) + "–" + trNum(avg[1], 0) + " W · azami " + trNum(max[0], 0) + "–" + trNum(max[1], 0) + " W");
      setText(rootEl.querySelector("[data-out='powerNote']"), out.power.note);
    } else {
      setText(rootEl.querySelector("[data-out='power']"), "Bu pitch için W/m² satırı yok");
      setText(rootEl.querySelector("[data-out='powerNote']"), "Uydurma watt yazılmadı. P1.56 COB ve Tayor / Grand Air tablolarında sayı var.");
    }
    setText(rootEl.querySelector("[data-out='band']"), out.band.name);
    setText(rootEl.querySelector("[data-out='bandNote']"), out.band.note);
    var money = rootEl.querySelector("[data-out='money']");
    if (money) {
      if (out.rental) {
        money.hidden = false;
        setText(rootEl.querySelector("[data-out='moneyValue']"), trNum(out.rental.low, 0) + "–" + trNum(out.rental.high, 0) + " TL / gün");
        setText(rootEl.querySelector("[data-out='moneyNote']"), out.rental.note + " Birim: " + trNum(out.rental.unit[0], 0) + "–" + trNum(out.rental.unit[1], 0) + " TL/m²/gün.");
      } else {
        money.hidden = true;
      }
    }
    setText(rootEl.querySelector("[data-out='bright']"), out.bright);
    var canvases = rootEl.querySelectorAll("[data-grid]");
    var leftPitch = out.rec.best;
    var rightPitch = out.pair == null ? out.rec.best : out.pair;
    if (canvases[0]) {
      paint(canvases[0], leftPitch, state.mesafe, state.isik);
      canvases[0].setAttribute("aria-label", formatPitch(leftPitch) + " ızgara, " + trNum(state.mesafe, 1) + " metre");
    }
    if (canvases[1]) {
      paint(canvases[1], rightPitch, state.mesafe, state.isik);
      canvases[1].setAttribute("aria-label", formatPitch(rightPitch) + " ızgara, " + trNum(state.mesafe, 1) + " metre");
    }
    setText(rootEl.querySelector("[data-grid-label='0']"), formatPitch(leftPitch) + " · önerilen");
    setText(rootEl.querySelector("[data-grid-label='1']"), formatPitch(rightPitch) + " · " + pairRole);
    var quote = rootEl.querySelector("[data-out='quote']");
    var pitchValue = String(round3(out.rec.best));
    var hiddenPitch = rootEl.querySelector("[data-in='pitch']");
    if (hiddenPitch) hiddenPitch.value = pitchValue;
    if (quote) {
      var q = new URLSearchParams();
      q.set("gen", String(round3(state.gen)));
      q.set("yuk", String(round3(state.yuk)));
      q.set("mesafe", String(round3(state.mesafe)));
      q.set("ortam", state.ortam);
      q.set("pitch", pitchValue);
      q.set("kullanim", state.kullanim);
      q.set("isik", state.isik);
      quote.setAttribute("href", "/iletisim/?" + q.toString());
    }
  }

  function mount(rootEl) {
    if (!rootEl || rootEl.getAttribute("data-ldj-ready") === "1") return;
    rootEl.setAttribute("data-ldj-ready", "1");
    function schedule() { render(rootEl); }
    rootEl.addEventListener("input", schedule);
    rootEl.addEventListener("change", schedule);
    var form = rootEl.querySelector("form");
    if (form) form.addEventListener("submit", schedule);
    render(rootEl);
  }

  function fillFamily(box, id) {
    var data = FAMILIES[id];
    if (!data) return;
    box.querySelectorAll("[data-family-btn]").forEach(function (btn) {
      var on = btn.getAttribute("data-family-btn") === id;
      btn.setAttribute("aria-selected", on ? "true" : "false");
    });
    setText(box.querySelector("[data-field='pitch']"), data.pitchLabel);
    setText(box.querySelector("[data-field='refresh']"), data.refreshLabel);
    setText(box.querySelector("[data-field='bright']"), data.brightLabel);
    setText(box.querySelector("[data-field='ip']"), data.ip);
    setText(box.querySelector("[data-field='power']"), data.powerLabel);
    setText(box.querySelector("[data-field='life']"), data.lifeLabel);
    setText(box.querySelector("[data-field='source']"), data.source);
    setText(box.querySelector("[data-field='barNote']"), data.barNote);
    setText(box.querySelector("[data-field='powerNote']"), data.powerNote);
    var refreshPct = Math.max(8, Math.min(100, (data.refreshMin / 7680) * 100));
    var brightPct = data.brightTop ? Math.max(8, Math.min(100, (data.brightTop / 8000) * 100)) : 6;
    var powerPct = data.powerAvg ? Math.max(8, Math.min(100, ((data.powerAvg[0] + data.powerAvg[1]) / 2 / 320) * 100)) : 6;
    function bar(name, pct) {
      var node = box.querySelector("[data-bar='" + name + "']");
      if (node) node.style.width = pct + "%";
    }
    bar("refresh", refreshPct);
    bar("bright", brightPct);
    bar("power", powerPct);
    bar("life", data.lifePct || 6);
    box.querySelectorAll("[data-energy-row]").forEach(function (row) {
      var fam = FAMILIES[row.getAttribute("data-energy-row")];
      var meter = row.querySelector("[data-energy-bar]");
      var label = row.querySelector("[data-energy-label]");
      if (!fam || !meter) return;
      if (!fam.powerAvg) {
        meter.style.width = "0%";
        if (label) label.textContent = "yok";
        return;
      }
      var mid = (fam.powerAvg[0] + fam.powerAvg[1]) / 2;
      meter.style.width = Math.max(8, Math.min(100, (mid / 320) * 100)) + "%";
      if (label) label.textContent = fam.powerLabel;
    });
  }

  function mountMetrics(box) {
    if (!box || box.getAttribute("data-ldj-metrics-ready") === "1") return;
    box.setAttribute("data-ldj-metrics-ready", "1");
    var current = "cob";
    box.querySelectorAll("[data-family-btn]").forEach(function (btn) {
      btn.addEventListener("click", function () {
        current = btn.getAttribute("data-family-btn");
        fillFamily(box, current);
      });
    });
    var reduce = root.matchMedia && root.matchMedia("(prefers-reduced-motion: reduce)").matches;
    function start() {
      fillFamily(box, current);
    }
    if (reduce || !root.IntersectionObserver) {
      start();
      return;
    }
    var seen = false;
    var io = new root.IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting || seen) return;
        seen = true;
        start();
        io.disconnect();
      });
    }, { threshold: 0.2 });
    io.observe(box);
  }

  var CALIBRATION = {
    kamera: "Kamera ve stüdyo çekiminde COB tablosu ≥3840 Hz sınıfını, çekimde daha temiz kare için anıyor. Bu bir kalibrasyon sertifikası değil.",
    video: "Hızlı video ve rental sahnede Grand Air ile H2.5–H5 kartları ≥7680 Hz yazıyor. Aynı içerik daha düşük yenilemede bantlaşabilir.",
    tabela: "Statik tabela ve duyuruda pitch ile parlaklık öne çıkar. Yenileme sınıfı kamera işindeki kadar belirleyici olmayabilir."
  };

  function mountCalibration(box) {
    if (!box || box.getAttribute("data-ldj-cal-ready") === "1") return;
    box.setAttribute("data-ldj-cal-ready", "1");
    var out = box.querySelector("[data-cal-out]");
    box.querySelectorAll("[data-cal]").forEach(function (btn) {
      btn.addEventListener("click", function () {
        box.querySelectorAll("[data-cal]").forEach(function (b) { b.setAttribute("aria-pressed", "false"); });
        btn.setAttribute("aria-pressed", "true");
        if (out) out.textContent = CALIBRATION[btn.getAttribute("data-cal")] || "";
      });
    });
  }

  function mountAll() {
    var doc = root.document;
    if (!doc) return;
    doc.querySelectorAll("[data-ldj-mimari]").forEach(mount);
    doc.querySelectorAll("[data-ldj-metrics]").forEach(mountMetrics);
    doc.querySelectorAll("[data-ldj-cal]").forEach(mountCalibration);
  }

  var api = {
    recommend: recommend,
    bandFor: bandFor,
    profileKey: profileKey,
    pitchesFor: pitchesFor,
    compute: compute,
    families: FAMILIES,
    mountAll: mountAll
  };

  if (typeof module !== "undefined" && module.exports) {
    module.exports = api;
  }

  if (!root.document) return;
  if (root.LedajansMimari && root.LedajansMimari.mountAll) {
    root.LedajansMimari.mountAll();
    return;
  }
  root.LedajansMimari = api;
  if (root.document.readyState === "loading") {
    root.document.addEventListener("DOMContentLoaded", mountAll);
  } else {
    mountAll();
  }
})(typeof window !== "undefined" ? window : global);
