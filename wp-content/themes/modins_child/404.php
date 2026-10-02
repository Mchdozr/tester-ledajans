<?php
/**
 * LEDAJANS R6-8 — dile duyarlı markalı 404 (modins_child/404.php TASLAĞI; Tur 5 sürümünün yerine).
 * Dil: pll_current_language() (Polylang 404'te de URL önekinden algılar: /en/… → en, /de/… → de), yoksa 'tr'.
 * Linkler: TR sayfa ID'sinden pll_get_post() çevirisi → get_permalink(); çeviri yoksa tablodaki sabit yol (home_url).
 * Nötr palet, #f46f2c yalnızca vurgu. Geri alma: Tur 5 404.php'yi geri yüklemek.
 */
if (!defined('ABSPATH')) { exit; }

$ldj_lang = function_exists('pll_current_language') ? (string) pll_current_language('slug') : '';
if (!in_array($ldj_lang, array('tr', 'en', 'de'), true)) { $ldj_lang = 'tr'; }

$ldj_t = array(
    'tr' => array('h1' => 'Aradığınız sayfa bulunamadı',
                  'desc' => 'Sayfa taşınmış ya da kaldırılmış olabilir. Arama yapabilir veya aşağıdaki sayfalardan devam edebilirsiniz.',
                  'nav' => 'Öne çıkan sayfalar', 'btn' => 'Bize Ulaşın'),
    'en' => array('h1' => 'The page you are looking for could not be found',
                  'desc' => 'The page may have been moved or removed. You can search the site or continue with one of the pages below.',
                  'nav' => 'Featured pages', 'btn' => 'Contact Us'),
    'de' => array('h1' => 'Die gesuchte Seite wurde nicht gefunden',
                  'desc' => 'Die Seite wurde möglicherweise verschoben oder entfernt. Sie können die Suche nutzen oder mit einer der folgenden Seiten fortfahren.',
                  'nav' => 'Empfohlene Seiten', 'btn' => 'Kontakt aufnehmen'),
);
// TR sayfa ID => [dil => [yedek yol, etiket]]  (ID'ler: tester REST /wp/v2/pages, 01.10.2026)
$ldj_links = array(
    5001 => array('tr' => array('/led-ekran/', 'LED Ekran'), 'en' => array('/en/led-screen/', 'LED Screens'), 'de' => array('/de/led-display/', 'LED-Displays')),
    5002 => array('tr' => array('/ic-mekan-led-ekran/', 'İç Mekan LED Ekran'), 'en' => array('/en/indoor-led-screen/', 'Indoor LED Screens'), 'de' => array('/de/indoor-led-display/', 'Indoor-LED-Displays')),
    5003 => array('tr' => array('/dis-mekan-led-ekran/', 'Dış Mekan LED Ekran'), 'en' => array('/en/outdoor-led-screen/', 'Outdoor LED Screens'), 'de' => array('/de/outdoor-led-display/', 'Outdoor-LED-Displays')),
    5004 => array('tr' => array('/rental-ekran/', 'Rental Ekran'), 'en' => array('/en/rental-led-screen/', 'LED Screen Rental'), 'de' => array('/de/rental-led-display/', 'LED-Wand mieten')),
    4956 => array('tr' => array('/projeler/', 'Projeler'), 'en' => array('/en/projects/', 'Projects'), 'de' => array('/de/projekte/', 'Projekte')),
);
$ldj_contact = array('id' => 1262, 'tr' => '/iletisim/', 'en' => '/en/contact/', 'de' => '/de/kontakt/');

if (!function_exists('ldj_404_url')) {
    function ldj_404_url($tr_id, $lang, $fallback_path) {
        if (function_exists('pll_get_post')) {
            $pid = (int) pll_get_post((int) $tr_id, $lang);
            if ($pid > 0 && get_post_status($pid) === 'publish') {
                $u = get_permalink($pid);
                if ($u) { return $u; }
            }
        }
        return home_url($fallback_path);
    }
}
$t = $ldj_t[$ldj_lang];
get_header(); ?>
<main id="ldj-404" lang="<?php echo esc_attr($ldj_lang); ?>" style="max-width:880px;margin:0 auto;padding:clamp(48px,8vw,96px) 20px;color:#1f2937;text-align:center">
  <p style="margin:0 0 8px;font-weight:700;letter-spacing:.08em;color:#f46f2c">404</p>
  <h1 style="margin:0 0 12px;font-size:clamp(28px,5vw,44px);line-height:1.15;color:#111827"><?php echo esc_html($t['h1']); ?></h1>
  <p style="margin:0 auto 28px;max-width:560px;color:#4b5563"><?php echo esc_html($t['desc']); ?></p>
  <div style="max-width:480px;margin:0 auto 32px"><?php get_search_form(); ?></div>
  <nav aria-label="<?php echo esc_attr($t['nav']); ?>" style="display:flex;flex-wrap:wrap;gap:10px;justify-content:center;margin-bottom:32px">
    <?php foreach ($ldj_links as $tr_id => $row) :
        list($path, $label) = $row[$ldj_lang]; ?>
      <a href="<?php echo esc_url(ldj_404_url($tr_id, $ldj_lang, $path)); ?>" style="padding:10px 16px;border:1px solid #d1d5db;border-radius:999px;color:#1f2937;text-decoration:none"><?php echo esc_html($label); ?></a>
    <?php endforeach; ?>
  </nav>
  <a href="<?php echo esc_url(ldj_404_url($ldj_contact['id'], $ldj_lang, $ldj_contact[$ldj_lang])); ?>" style="display:inline-block;padding:12px 24px;border-radius:8px;background:#c9501a;color:#fff;font-weight:700;text-decoration:none"><?php echo esc_html($t['btn']); ?></a>
</main>
<?php get_footer();
