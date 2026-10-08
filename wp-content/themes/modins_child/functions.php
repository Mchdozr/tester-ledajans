<?php
/**
 *
 * @package [Parent Theme]
 * @author  gaviasthemes <gaviasthemes@gmail.com>
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 *
 */

function modins_child_scripts() {
   wp_enqueue_style( 'modins-parent-style', get_template_directory_uri(). '/style.css');
   wp_enqueue_style( 'modins-child-style', get_stylesheet_uri());
}
add_action( 'wp_enqueue_scripts', 'modins_child_scripts', 9999 );

function custom_override_header($name) {
    if (!$name) {
        get_template_part('header', 'front');
    } else {
        get_template_part('header', $name);
    }
}

function override_get_header($name = null) {
    remove_action('get_header', 'wp_head');
    add_action('get_header', 'custom_override_header');
}

add_action('get_header', 'override_get_header');

add_action('template_redirect', 'custom_buffer_start');

function custom_buffer_start() {
    if (is_admin() || is_feed()) {
        return;
    }
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    $needs_translation = (strpos($uri, '/en/') !== false) || (strpos($uri, '/de/') !== false);
    if ($needs_translation) {
        ob_start('custom_translate_output');
    }
}

function custom_translate_output($buffer) {
    $current_url = esc_url(home_url($_SERVER['REQUEST_URI']));

    $site_language = get_locale();
    $is_english = strpos($current_url, '/en/') !== false || $site_language === 'en_US';
    $is_german  = strpos($current_url, '/de/') !== false || $site_language === 'de_DE';

    $translations = array(
        'tr' => array(
            'Hemen Arayın' => 'Hemen Arayın',
            '+90 543 879 51 08' => '+90 543 879 51 08',
            'Halide Edip Adıvar Mah.  Gül 2 Sokak No: 10a 34382 Şişli/İstanbul' => 'Halide Edip Adıvar Mah.  Gül 2 Sokak No: 10a 34382 Şişli/İstanbul',
            'İletişim' => 'İletişim',
            'Galeri' => 'Galeri',
            'Bülten' => 'Bülten',
            'Teklif Al' => 'Teklif Al',
            'Haberdar olmak için bütene abone olun' => 'Haberdar olmak için bütene abone olun',
            '© 2024 Ledajans.com Tüm Hakları Saklıdır.' => '© 2024 Ledajans.com Tüm Hakları Saklıdır.',
            'Hayatınızı yaşama şeklinizi yansıtan Ledler' => 'Hayatınızı yaşama şeklinizi yansıtan Ledler',
            'Led Ajans, hemen her sektörde kullanılan ve daha etkin reklam imkanı veren bilgilendirme panoları, led ekranlar ve kayan yazılarda kullanılan, led modül, güç kaynakları ve kontrol kartları ile değerli müşterilerine hizmet vermektedir.' => 'Led Ajans, hemen her sektörde kullanılan ve daha etkin reklam imkanı veren bilgilendirme panoları, led ekranlar ve kayan yazılarda kullanılan, led modül, güç kaynakları ve kontrol kartları ile değerli müşterilerine hizmet vermektedir.',
        ),
        'en' => array(
            'Hemen Arayın' => 'Call Now',
            '+90 543 879 51 08' => '+44 7932 7832 51',
            'İletişim' => 'Contact', // v3 2026-10-04: Ceviri kararı (sayfa başlığıyla aynı)
            'Halide Edip Adıvar Mah.  Gül 2 Sokak No: 10a 34382 Şişli/İstanbul' => 'Halide Edip Adıvar Mah.  Gül 2 Sokak No: 10a 34382 Şişli/İstanbul',
            'Galeri' => 'Gallery',
            'Bülten' => 'Subscribe',
            'Teklif Al' => 'Get a Quote',
            'Haberdar olmak için bütene abone olun' => 'Subscribe to the newsletter to stay informed',
            '© 2024 Ledajans.com Tüm Hakları Saklıdır.' => '© 2024 Ledajans.com All Rights Reserved.',
            'Hayatınızı yaşama şeklinizi yansıtan Ledler' => 'LEDs that reflect the way you live your life',
            'Led Ajans, hemen her sektörde kullanılan ve daha etkin reklam imkanı veren bilgilendirme panoları, led ekranlar ve kayan yazılarda kullanılan, led modül, güç kaynakları ve kontrol kartları ile değerli müşterilerine hizmet vermektedir.' => 'Led Ajans provides services to its valued customers by offering LED modules, power supplies, and control cards used in information boards, LED screens, and scrolling texts, which are widely used across almost every industry to deliver more effective advertising opportunities.',
        ),
        'de' => array(
            'Hemen Arayın' => 'Jetzt anrufen',
            '+90 543 879 51 08' => '+49 1521 2401 915',
            'İletişim' => 'Kontakt',
            'Galeri' => 'Galerie',
            'Bülten' => 'Abonnieren',
            'Teklif Al' => 'Angebot erhalten',
            'Haberdar olmak için bütene abone olun' => 'Abonnieren Sie den Newsletter, um informiert zu bleiben',
            '© 2024 Ledajans.com Tüm Hakları Saklıdır.' => '© 2024 Ledajans.com Alle Rechte vorbehalten.',
            'Hayatınızı yaşama şeklinizi yansıtan Ledler' => 'LEDs, die widerspiegeln, wie Sie Ihr Leben leben',
            'Led Ajans, hemen her sektörde kullanılan ve daha etkin reklam imkanı veren bilgilendirme panoları, led ekranlar ve kayan yazılarda kullanılan, led modül, güç kaynakları ve kontrol kartları ile değerli müşterilerine hizmet vermektedir.' => 'Led Ajans bietet seinen geschätzten Kunden LED-Module, Netzteile und Steuerkarten an, die in Informationstafeln, LED-Bildschirmen und Laufschriften verwendet werden und in nahezu allen Branchen eingesetzt werden, um effektivere Werbemöglichkeiten zu schaffen.',
        ),
    );

    if ($is_english) {
        foreach ($translations['en'] as $turkish => $translated) {
            $buffer = str_replace($turkish, $translated, $buffer);
        }
    } elseif ($is_german) {
        foreach ($translations['de'] as $turkish => $translated) {
            $buffer = str_replace($turkish, $translated, $buffer);
        }
    }

    return $buffer;
}

function add_title_to_images($content) {
    if (is_singular()) {
        global $post;
        $page_title = get_the_title($post->ID);

        $pattern = '/<img(.*?)>/i';
        $replacement = '<img$1 title="' . esc_attr($page_title) . '" alt="' . esc_attr($page_title) . '">';

        $content = preg_replace($pattern, $replacement, $content);
    }

    return $content;
}
add_filter('the_content', 'add_title_to_images');

/**
 * W3TC Lazy Load token temizliği
 * Sayfada görünen {w3tc_lazyload_xxx} metinlerini kaldırır.
 */
add_filter('the_content', function ($content) {
    return preg_replace('/\{w3tc_lazyload_[^}]+\}/i', '', $content);
}, 999);

add_action('template_redirect', 'redirect_404_to_homepage');
function redirect_404_to_homepage() {
    if (is_404()) {
        wp_redirect(home_url(), 301);
        exit;
    }
}

add_filter('rank_math/sitemap/http_headers', function($headers) {
    $headers['X-Robots-Tag'] = 'index, follow';
    return $headers;
});

/**
 * Önbellek başlıkları: önce gelen tüm Cache-Control/Pragma/Expires temizlenir,
 * sonra tek satır yazılır (W3TC vb. send_headers sonrası).
 */
add_action('send_headers', function () {
    if (is_admin() || (defined('REST_REQUEST') && REST_REQUEST) || headers_sent()) {
        return;
    }

    header_remove('Cache-Control');
    header_remove('Pragma');
    header_remove('Expires');

    if (is_user_logged_in()) {
        header('Cache-Control: private, no-cache, must-revalidate, max-age=0');
    } else {
        header('Cache-Control: public, max-age=300, s-maxage=300, must-revalidate');
    }
}, 2147483647); // PHP_INT_MAX yerine sabit (eski PHP uyumu)

/**
 * Frontend performans iyileştirmeleri (güvenli set)
 */
add_action('wp_enqueue_scripts', function () {
    // Misafirlerde gereksiz dashicons yükünü kaldır
    if (!is_user_logged_in()) {
        wp_deregister_style('dashicons');
    }
}, 100);

// Emoji script/style kaldır (frontend)
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

// GTM/GA için erken bağlantı
add_filter('wp_resource_hints', function ($urls, $relation_type) {
    if ($relation_type === 'preconnect') {
        $urls[] = ['href' => 'https://www.googletagmanager.com', 'crossorigin' => 'anonymous'];
        $urls[] = ['href' => 'https://www.google-analytics.com', 'crossorigin' => 'anonymous'];
    }
    if ($relation_type === 'dns-prefetch') {
        $urls[] = '//www.googletagmanager.com';
        $urls[] = '//www.google-analytics.com';
    }
    return $urls;
}, 10, 2);

// GTM/GA script'lerini render engellemeden yükle
add_filter('script_loader_tag', function ($tag, $handle, $src) {
    if (
        strpos($src, 'googletagmanager.com/gtm.js') !== false ||
        strpos($src, 'googletagmanager.com/gtag/js') !== false
    ) {
        if (stripos($tag, ' defer') === false) {
            $tag = str_replace('<script ', '<script defer ', $tag);
        }
    }
    return $tag;
}, 10, 3);

add_action('template_redirect', function () {
    if (is_admin() || is_feed()) return;

    ob_start(function ($html) {
        $pattern = "#<script>\\s*var gtmLoaded = false;\\s*function loadGTM\\(\\)\\s*\\{[\\s\\S]*?setTimeout\\(loadGTM, 3000\\);[\\s\\S]*?window\\.addEventListener\\(e, loadGTM, \\{once:true, passive:true\\}\\);[\\s\\S]*?\\}\\);\\s*</script>#i";
        return preg_replace($pattern, '', $html, 1);
    });
}, 1);

add_action('wp_head', function () {
    if (!(is_front_page() || is_home())) {
        return;
    }

    // R2: canlı domain yerine sitenin kendi uploads'ı + hero'da gerçekten kullanılan görseller
    echo '<link rel="preload" as="image" href="' . esc_url(content_url('/uploads/2026/09/hero-stant-poster-1.webp')) . '" media="(min-width: 901px)" fetchpriority="high">' . "\n";
    echo '<link rel="preload" as="image" href="' . esc_url(content_url('/uploads/2026/09/hero-stant-poster-mobile-1.webp')) . '" media="(max-width: 900px)" fetchpriority="high">' . "\n";
}, 1);

// Anasayfada Rank Math şemalarını kapat; schema kaynağı widget JSON-LD olsun
add_filter('rank_math/json_ld', function ($data, $jsonld) {
    if (is_front_page() || is_home()) {
        return [];
    }
    return $data;
}, 99, 2);

/* =============================================================================
 * LEDAJANS Round 1 tasarım katmanı (2026-09-30)
 * assets/css/ledajans-r1.css + assets/js/ledajans-r1.js
 * Geri alma: bu bloğu silmek (veya yedek functions.php'yi geri yüklemek) yeterli.
 * ============================================================================= */
add_action('wp_enqueue_scripts', function () {
    $dir = get_stylesheet_directory();
    $uri = get_stylesheet_directory_uri();
    $css = '/assets/css/ledajans-r1.css';
    $js  = '/assets/js/ledajans-r1.js';
    if (file_exists($dir . $css)) {
        wp_enqueue_style('ledajans-r1', $uri . $css, array(), (string) filemtime($dir . $css));
    }
    if (file_exists($dir . $js)) {
        wp_enqueue_script('ledajans-r1', $uri . $js, array(), (string) filemtime($dir . $js), array('in_footer' => true, 'strategy' => 'defer'));
    }
    $js2 = '/assets/js/ledajans-r2.js';
    if (file_exists($dir . $js2)) {
        wp_enqueue_script('ledajans-r2', $uri . $js2, array(), (string) filemtime($dir . $js2), array('in_footer' => true, 'strategy' => 'defer'));
    }
}, 10000);

add_filter('body_class', function ($classes) {
    $classes[] = 'ldj-r1';
    return $classes;
});

// Kullanılmayan font CSS'leri (Inter / Roboto / Roboto Slab) — yalnızca ön yüzde, editörde değil
add_filter('style_loader_tag', function ($tag, $handle) {
    if (is_admin() || isset($_GET['elementor-preview'])) {
        return $tag;
    }
    $unused = array('elementor-gf-inter', 'elementor-gf-local-roboto', 'elementor-gf-local-robotoslab', 'rs-roboto');
    return in_array($handle, $unused, true) ? '' : $tag;
}, 10, 2);


/* =============================================================================
 * LEDAJANS Round 2 (2026-09-30): HTML çıktı filtresi
 *  - tester'da GTM/GA/Ads çıktısını kaldırır (yalnızca HTTP_HOST = tester.ledajans.com)
 *  - autoplay proje videolarında src'yi erteler (preload="none" + poster), mevcut IO script'i yükler
 *  - alt'ı boş/eksik uploads görsellerine Ortam Kütüphanesi alt metnini yazar
 * Geri alma: bu bloğu kaldırmak yeterli.
 * ============================================================================= */
function ldj_r2_is_front_html() {
    if (is_admin() || is_feed() || wp_doing_ajax() || (defined('REST_REQUEST') && REST_REQUEST) || isset($_GET['elementor-preview']) || is_customize_preview()) {
        return false;
    }
    return true;
}

add_action('template_redirect', function () {
    if (ldj_r2_is_front_html()) {
        ob_start('ldj_r2_filter_html');
    }
}, 3);

function ldj_r2_alt_for($src) {
    static $cache = array();
    if (isset($cache[$src])) {
        return $cache[$src];
    }
    $url = strtok($src, '?#');
    if (strpos($url, '//') === 0) {
        $url = 'https:' . $url;
    }
    $alt = '';
    $site_host = wp_parse_url(home_url(), PHP_URL_HOST);
    if (wp_parse_url($url, PHP_URL_HOST) === $site_host) {
        $id = attachment_url_to_postid($url);
        if (!$id) {
            $id = attachment_url_to_postid(preg_replace('#-\d+x\d+(\.[a-z0-9]+)$#i', '$1', $url));
        }
        if ($id) {
            $alt = trim((string) get_post_meta($id, '_wp_attachment_image_alt', true));
        }
    }
    $cache[$src] = $alt;
    return $alt;
}

function ldj_r2_filter_html($html) {
    if (!is_string($html) || stripos($html, '<html') === false) {
        return $html;
    }
    $orig = $html;

    // 1) GTM / GA / Ads — sadece tester
    $host = isset($_SERVER['HTTP_HOST']) ? strtolower($_SERVER['HTTP_HOST']) : '';
    if ($host === 'tester.ledajans.com') {
        $html = preg_replace('#<!--\s*Google Tag Manager \(noscript\)\s*-->.*?<!--\s*End Google Tag Manager \(noscript\)\s*-->#is', '', $html);
        $html = preg_replace('#<!--\s*Google Tag Manager\s*-->.*?<!--\s*End Google Tag Manager\s*-->#is', '', $html);
        $html = preg_replace_callback('#<script\b[^>]*>.*?</script>#is', function ($m) {
            return preg_match('#googletagmanager\.com/(gtm|gtag)|google-analytics\.com/(analytics|ga)\.js|googleadservices\.com|doubleclick\.net|\bgtag\(\s*[\'"](config|js)#i', $m[0]) ? '' : $m[0];
        }, $html);
        $html = preg_replace('#<noscript>\s*<iframe[^>]*googletagmanager\.com/ns\.html[^>]*>\s*</iframe>\s*</noscript>#i', '', $html);
        $html = preg_replace('#<link\b[^>]*href=["\'](?:https?:)?//www\.(?:googletagmanager|google-analytics)\.com/?["\'][^>]*>\s*#i', '', $html);
    }

    // 2) Autoplay videolar: src ertele + poster
    $poster_names = array('outdoor-kure-led-ekran', 'WhatsApp-Video-2026-03-12-at-11.51.08-AM-1', 'WhatsApp-Video-2026-03-12-at-11.50.57-AM', 'WhatsApp-Video-2026-03-12-at-11.51.07-AM', 'WhatsApp-Video-2026-03-12-at-11.51.07-AM-2', 'WhatsApp-Video-2026-03-12-at-11.51.07-AM-1', 'v1', 'v2-Trim');
    $html = preg_replace_callback('#<video\b([^>]*)>(.*?)</video>#is', function ($m) use ($poster_names) {
        $attrs = $m[1];
        $inner = $m[2];
        if (stripos($attrs, 'autoplay') === false || stripos($attrs, 'la1Video') !== false || stripos($attrs, 'data-ldj-lazy') !== false) {
            return $m[0];
        }
        $src = '';
        if (preg_match('#\ssrc=(["\'])([^"\']+)\1#i', $attrs, $sm)) {
            $src = $sm[2];
        } elseif (preg_match('#<source\b[^>]*\ssrc=(["\'])([^"\']+)\1#i', $inner, $sm)) {
            $src = $sm[2];
        }
        if ($src === '') {
            return $m[0];
        }
        $attrs = preg_replace('#\ssrc=(["\'])#i', ' data-ldj-src=$1', $attrs);
        $attrs = preg_replace('#\spreload=(["\'])[^"\']*\1#i', '', $attrs);
        $attrs .= ' preload="none" data-ldj-lazy="1"';
        if (stripos($attrs, ' poster=') === false) {
            $base = basename((string) wp_parse_url($src, PHP_URL_PATH), '.mp4');
            if (in_array($base, $poster_names, true)) {
                $attrs .= ' poster="' . esc_url(content_url('/uploads/2026/09/ldj-poster-' . $base . '.webp')) . '"';
            }
        }
        $inner = preg_replace('#(<source\b[^>]*?)\ssrc=(["\'])#i', '$1 data-ldj-src=$2', $inner);
        return '<video' . $attrs . '>' . $inner . '</video>';
    }, $html);

    // 3) Boş alt → Ortam Kütüphanesi alt metni
    $html = preg_replace_callback('#<img\b[^>]*>#i', function ($m) {
        $tag = $m[0];
        $has_alt = preg_match('#\salt=(["\'])(.*?)\1#is', $tag, $am);
        if ($has_alt && trim($am[2]) !== '') {
            return $tag;
        }
        if (!preg_match('#\ssrc=(["\'])([^"\']+)\1#i', $tag, $sm) || strpos($sm[2], '/wp-content/uploads/') === false) {
            return $tag;
        }
        $alt = ldj_r2_alt_for($sm[2]);
        if ($alt === '') {
            return $tag;
        }
        if ($has_alt) {
            return preg_replace('#\salt=(["\'])(.*?)\1#is', ' alt="' . esc_attr($alt) . '"', $tag, 1);
        }
        return preg_replace('#^<img\b#i', '<img alt="' . esc_attr($alt) . '"', $tag);
    }, $html);

    // Güvenlik: regex hatası (null) ya da beklenmedik küçülme olursa orijinali döndür
    if (!is_string($html) || strlen($html) < strlen($orig) * 0.8) {
        return $orig;
    }
    return $html;
}


/* =============================================================================
 * LEDAJANS Round 3 (2026-09-30)
 *  - /urunlerimiz-led-ekran/ (var olmayan URL) → /led-ekran/ 301 (404→anasayfa kuralından önce)
 *  - tester: canlı alan adına (ledajans.com) giden preload'ları kaldırır
 *  - tester: ağır görselleri Ortam Kütüphanesi'ndeki yeni optimize kopyalarla değiştirir (yalnızca <img>)
 *  - ISO kayan bant görselleri: loading=lazy; hero LCP görseli: tek fetchpriority=high + eager
 *  - içerik görsellerinde (ilk 2 hariç) eksik loading → lazy
 *  - Send eklentisi: ayar nesnesi yoksa zararsız fallback (hiçbir formu izlemez)
 * Geri alma: bu bloğu kaldırmak yeterli (R2 yedeği: backups/20260930-190917/files/...).
 * ============================================================================= */
add_action('template_redirect', function () {
    if (!is_404()) {
        return;
    }
    $path = isset($_SERVER['REQUEST_URI']) ? strtolower((string) wp_parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)) : '';
    if (rtrim($path, '/') === '/urunlerimiz-led-ekran') {
        wp_safe_redirect(home_url('/led-ekran/'), 301);
        exit;
    }
    // Eksik statik dosyalar (/wp-content/, /wp-includes/) anasayfaya yönlenmesin: gerçek 404 dönsün
    if (strpos($path, '/wp-content/') === 0 || strpos($path, '/wp-includes/') === 0) {
        remove_action('template_redirect', 'redirect_404_to_homepage');
    }
}, 1);

add_action('template_redirect', function () {
    if (ldj_r2_is_front_html()) {
        ob_start('ldj_r3_filter_html');
    }
}, 4);

function ldj_r3_image_map() {
    return array(
        '2023/01/P2.5.jpg' => '2026/09/ldj-opt-P2.5-400w.webp',
        '2023/01/PH1.53.jpg' => '2026/09/ldj-opt-PH1.53-400w.webp',
        '2023/01/fb8143a3-9d69-45a3-859c-2e2db66c334a.jpg' => '2026/09/ldj-opt-fb8143a3-9d69-45a3-859c-2e2db66c334a-400w.webp',
        '2023/01/P1.25-2-scaled.jpg' => '2026/09/ldj-opt-P1.25-2-scaled-400w.webp',
        '2023/01/P3.jpg' => '2026/09/ldj-opt-P3-400w.webp',
        '2023/01/ec8726e1-1617-438d-927c-6904cf2506f4.jpg' => '2026/09/ldj-opt-ec8726e1-1617-438d-927c-6904cf2506f4-400w.webp',
        '2023/01/PH1.53-550x632.jpg' => '2026/09/ldj-opt-PH1.53-550x632-400w.webp',
        '2023/01/P1.25-2-550x632.jpg' => '2026/09/ldj-opt-P1.25-2-550x632-400w.webp',
        '2023/01/fb8143a3-9d69-45a3-859c-2e2db66c334a-550x632.jpg' => '2026/09/ldj-opt-fb8143a3-9d69-45a3-859c-2e2db66c334a-550x632-400w.webp',
        '2023/01/P3-550x632.jpg' => '2026/09/ldj-opt-P3-550x632-400w.webp',
        '2023/01/ec8726e1-1617-438d-927c-6904cf2506f4-550x632.jpg' => '2026/09/ldj-opt-ec8726e1-1617-438d-927c-6904cf2506f4-550x632-400w.webp',
        '2026/09/iso-9001-2015-1.webp' => '2026/09/ldj-opt-iso-9001-2015-1-240w.webp',
        '2026/09/iso-45001-2018-1.webp' => '2026/09/ldj-opt-iso-45001-2018-1-240w.webp',
        '2026/09/iso-14001-2015-1.webp' => '2026/09/ldj-opt-iso-14001-2015-1-240w.webp',
        '2026/09/iso-10002-2018-1.webp' => '2026/09/ldj-opt-iso-10002-2018-1-240w.webp',
        '2026/09/iso-27001-2022-1.webp' => '2026/09/ldj-opt-iso-27001-2022-1-240w.webp',
        '2026/09/iso-31000-2018-1.webp' => '2026/09/ldj-opt-iso-31000-2018-1-240w.webp',
        '2026/09/taha-led-14064-1.webp' => '2026/09/ldj-opt-taha-led-14064-1-240w.webp',
        '2022/12/Arka-plani-kaldir-projesi-2-8.png' => '2026/09/ldj-opt-Arka-plani-kaldir-projesi-2-8-1000w.webp',
        '2022/12/P4.jpg' => '2026/09/ldj-opt-P4-1000w.webp',
        '2022/12/COLORGLIGHT.jpg' => '2026/09/ldj-opt-COLORGLIGHT-600w.webp',
        '2026/09/ic-mekan-led-ekran-panel-transparent.webp' => '2026/09/ldj-opt-ic-mekan-led-ekran-panel-transparent-900w.webp',
        '2023/01/P2.5-550x632.jpg' => '2026/09/ldj-opt-P2.5-550x632-400w.webp',
    );
}

function ldj_r3_filter_html($html) {
    if (!is_string($html) || stripos($html, '<html') === false) {
        return $html;
    }
    $host = isset($_SERVER['HTTP_HOST']) ? strtolower($_SERVER['HTTP_HOST']) : '';
    if ($host !== 'tester.ledajans.com') {
        return $html;
    }
    $orig = $html;
    $up = content_url('/uploads/');

    // 0) mu-plugin (ledajans-perf-patch) mobilde gecikmeli GTM yükleyicisini sonradan ekliyor;
    //    bayrak önceden tanımlanınca yükleyici eklenmez/çalışmaz (yalnızca tester)
    if (stripos($html, 'ledajansGtmLoaded') === false) {
        $html = preg_replace('#<head\b[^>]*>#i', '$0' . "\n" . '<script id="ldj-gtm-off">window.ledajansGtmLoaded=1;</script>', $html, 1);
    }

    // 1) Canlı alan adına giden preload'lar
    $html = preg_replace('#<link\b[^>]*rel=["\']preload["\'][^>]*href=["\']https?://(?:www\.)?ledajans\.com/[^"\']*["\'][^>]*>\s*#i', '', $html);

    // 2) İçerik başlangıcı (header sonrası)
    $content_pos = stripos($html, 'data-elementor-type="wp-page"');
    if ($content_pos === false) {
        $content_pos = strripos($html, '</header>');
    }
    if ($content_pos === false) {
        $content_pos = 0;
    }

    $map = ldj_r3_image_map();
    $from = array();
    $to = array();
    foreach ($map as $o => $n) {
        $from[] = $up . $o;
        $to[] = $up . $n;
    }

    $count = 0;
    $html = preg_replace_callback('#<img\b[^>]*>#i', function ($m) use ($from, $to, $content_pos, &$count) {
        $tag = $m[0][0];
        $pos = $m[0][1];
        $new = str_replace($from, $to, $tag);
        if ($new !== $tag) {
            // optimize kopyaya geçildi: eski srcset/sizes büyük adayları seçtirmesin
            $new = preg_replace('#\s(srcset|sizes)=(["\']).*?\2#is', '', $new);
            $tag = $new;
        }
        $is_hero = stripos($tag, 'hero-stant-poster') !== false;
        $is_iso = (bool) preg_match('#ldj-opt-(iso-|taha-led-14064)#i', $tag);
        if ($is_hero) {
            $tag = preg_replace('#\sfetchpriority=(["\'])[^"\']*\1#i', '', $tag);
            $tag = preg_replace('#\sloading=(["\'])lazy\1#i', '', $tag);
            $tag = preg_replace('#^<img\b#i', '<img fetchpriority="high"', $tag);
            return $tag;
        }
        if ($is_iso) {
            $tag = preg_replace('#\sloading=(["\'])[^"\']*\1#i', '', $tag);
            $tag = preg_replace('#^<img\b#i', '<img loading="lazy"', $tag);
            return $tag;
        }
        if ($pos > $content_pos && stripos($tag, 'fetchpriority') === false && !preg_match('#\sloading=#i', $tag)) {
            $count++;
            if ($count > 2) {
                $tag = preg_replace('#^<img\b#i', '<img loading="lazy"', $tag);
                if (!preg_match('#\sdecoding=#i', $tag)) {
                    $tag = preg_replace('#^<img\b#i', '<img decoding="async"', $tag);
                }
            }
        }
        return $tag;
    }, $html, -1, $n_img, PREG_OFFSET_CAPTURE);

    // 3) Send eklentisi fallback (ayar nesnesi kaldırılmışsa JS hatasını önle)
    $guards = array(
        'elementor' => array('send-app-elementor-form-tracker-js', 'eSendElementorFormsSettings'),
        'cf7'       => array('send-app-cf7-form-tracker-js', 'eSendCf7FormsSettings'),
    );
    foreach ($guards as $integ => $g) {
        $script_id = $g[0];
        $var = $g[1];
        if (strpos($html, 'id="' . $script_id . '"') !== false && strpos($html, $var) === false) {
            $cfg = wp_json_encode(array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'integration' => $integ,
                'nonce' => '',
                'viewedThreshold' => 0.95,
                'viewedAction' => '',
                'abandonedAction' => '',
                'formSelectors' => array('form.ldj-esend-disabled'),
                'debugOn' => false,
                'idPrefix' => '',
            ));
            $guard = '<script id="ldj-esend-guard-' . $integ . '">window.' . $var . '=window.' . $var . '||' . $cfg . ';</script>' . "\n";
            $html = preg_replace('#<script\b[^>]*id="' . preg_quote($script_id, '#') . '"#', $guard . '$0', $html, 1);
        }
    }

    if (!is_string($html) || strlen($html) < strlen($orig) * 0.8) {
        return $orig;
    }
    return $html;
}

/* =============================================================================
 * LEDAJANS R4 (tester): Slider Revolution varlıklarını slider olmayan sayfalarda kaldır
 *  - Nihai HTML'de RevSlider modülü (sr7-module / rs-module / SR7.PMH / [rev_slider) yoksa
 *    sr7.css, tptools.js, sr7.js ve SR7 satır içi yapılandırma betiği çıkarılır.
 *  - Modül bulunan sayfalarda hiçbir şeye dokunulmaz.
 * Geri alma: bu bloğu kaldırmak yeterli (R4 yedeği: backups/20260930-201856/files/...).
 * ============================================================================= */
add_action('template_redirect', function () {
    if (ldj_r2_is_front_html()) {
        ob_start('ldj_r4_revslider_filter');
    }
}, 5);

function ldj_r4_revslider_filter($html) {
    if (!is_string($html) || $html === '' || stripos($html, '<html') === false) {
        return $html;
    }
    if (strpos($html, 'plugins/revslider/') === false && strpos($html, 'window.SR7') === false) {
        return $html;
    }
    // RevSlider'ın kendi satır içi yapılandırma betikleri (içlerinde modül adları geçtiği için tespitten önce ayrılır)
    $inline_re = '#<script\b[^>]*>(?:(?!</script>).)*?window\._tpt\s*\?\?=\s*\{\}(?:(?!</script>).)*</script>#is';
    $probe = preg_replace($inline_re, '', $html);
    if ($probe === null) {
        return $html;
    }
    $probe = preg_replace('#<(?:script|link)\b[^>]*plugins/revslider/[^>]*>#i', '', $probe);
    if ($probe === null) {
        return $html;
    }
    $markers = array('<sr7-module', '<rs-module', 'SR7.PMH', '[rev_slider', 'rev_slider_wrapper', 'rs-module-wrap', 'sr7-module-bg');
    foreach ($markers as $m) {
        if (stripos($probe, $m) !== false) {
            return $html; // sayfada slider var: dokunma
        }
    }
    $out = preg_replace($inline_re, '', $html);
    if ($out === null) {
        return $html;
    }
    $out2 = preg_replace(array(
        '#<link\b[^>]*plugins/revslider/[^>]*>\s*#i',
        '#<script\b[^>]*plugins/revslider/[^>]*>\s*</script>\s*#i',
    ), '', $out);
    if ($out2 === null) {
        return $html;
    }
    return $out2;
}
/* =============================================================================
 * LEDAJANS Op B (tester): Rank Math sitemap yanıtlarına X-Robots-Tag: noindex
 *  - Mevcut satır ~137'deki 'index, follow' filtresini (öncelik 10) öncelik 99 ile ezer.
 *  - Yalnızca tester host'ta çalışır.
 * Geri alma: bu bloğu kaldırmak yeterli.
 * ============================================================================= */
add_filter('rank_math/sitemap/http_headers', function ($headers) {
    if (wp_parse_url(home_url(), PHP_URL_HOST) === 'tester.ledajans.com') {
        $headers['X-Robots-Tag'] = 'noindex, nofollow';
    }
    return $headers;
}, 99);

/* =============================================================================
 * LEDAJANS (tester): kullanılmayan mobil preload'u kaldır
 *  - mu-plugin'in wp_head'e yazdığı ldajsn2-mobile-q60.webp preload'u sayfada hiç
 *    kullanılmıyor (hero: hero-stant-poster-mobile-1.webp). Yalnızca anasayfa head'inden silinir.
 * Geri alma: bu bloğu kaldırmak yeterli (yedek: backups/preload-<ts>/functions.php).
 * ============================================================================= */
add_action('template_redirect', function () {
    if (ldj_r2_is_front_html() && is_front_page()) {
        ob_start('ldj_drop_q60_preload');
    }
}, 6);

function ldj_drop_q60_preload($html) {
    if (!is_string($html) || strpos($html, 'ldajsn2-mobile-q60.webp') === false) {
        return $html;
    }
    $out = preg_replace('#<link\b[^>]*rel=["\']preload["\'][^>]*ldajsn2-mobile-q60\.webp[^>]*>\s*#i', '', $html, 1);
    return $out === null ? $html : $out;
}

// ===================== LEDAJANS R5 (tester) — child functions.php SONUNA eklenecek taslak =====================
// Geri alma: bu bloğu silmek (functions.php yedeğinden geri yüklemek). Yalnızca dosya; DB değişikliği yok.

// R5-a) ledajans-i18n-tools eklentisinin canlı-URL'li yönlendiricisini nötrle (eklenti dosyasına DOKUNMADAN).
//       wp_safe_redirect('https://ledajans.com/...') tester'da izinli host değil -> /wp-admin/ -> wp-login.php zinciri buradan.
add_action('wp', function () {
    remove_action('template_redirect', 'ledajans_junk_urls', -1);
}, 99);

// R5-b) Tam-URL yönlendirme haritası (önek/regex YOK; /en/* ve /de/* genel kural YOK).
function ldj_r5_redirect_map() {
    return [
        '/blog/ic-mekan-led-ekran-boyutlari-p-degerleri/' => ['/pitch-secim-rehberi/', 301],
        '/blog/led-ekran-fiyatlari-2026-2/' => ['/led-ekran-fiyatlari-2026/', 301],
        '/portfolio-01/' => ['/projeler/', 301],
        '/blog/huidu-a3l-controller-karti/' => ['/huidu/', 301],
        '/bursa-led-ekran/' => ['/led-ekran/', 302], // GEÇİCİ 302 (şehir sayfası)
        '/magaza-vitrin-seffaf-led/' => ['/magaza-vitrin-led-ekran/', 301],
        '/blog/hd-a4l-nedir-huidu-led-ekran-kontrol-karti-rehberi/' => ['/huidu/', 301],
        '/seffaf-led-ekran/' => ['/magaza-vitrin-led-ekran/', 301],
        '/rehber/' => ['/blog/', 301],
        '/urunlerimiz-led-ekran/' => ['/led-ekran/', 301],
        '/blog/dis-mekan-led-ekran/' => ['/dis-mekan-led-ekran/', 301],
        '/blog/p10-panel-kirmizi-3/' => ['/p10-kirmizi-panel/', 301], // p10 2026-10-07: zincir tek adıma (eski: → /p10-panel-kirmizi/ → /p10-kirmizi-panel/)
        '/karsilastirma/' => ['/gob-vs-cob-smd/', 301],
        '/blog/hd-a4l-led-ekran-kontrol-karti/' => ['/huidu/', 301],
        '/tv-studyolari-led-ekran/' => ['/ic-mekan-led-ekran/', 301],
        '/blog/huidu-led-kontrol-karti/' => ['/huidu/', 301],
        '/blog/receiving-card-alici-kart/' => ['/kontrol-kartlari/', 301],
        '/blog/huidu-hd-a5l-controller-karti/' => ['/huidu/', 301],
        '/blog/led-ekran-fiyatlari-2026-mayis/' => ['/led-ekran-fiyatlari-2026/', 301],
        '/blog/p10-panel-kirmizi-4/' => ['/p10-kirmizi-panel/', 301], // p10 2026-10-07: zincir tek adıma (eski: → /p10-panel-kirmizi/ → /p10-kirmizi-panel/)
        '/sektorel-cozumler/' => ['/led-ekran-kullanim-alanlari/', 301],
        '/blog/cob-led-ekran-teknoloji-avantajlari/' => ['/cob-led-ekran-nedir-avantajlari/', 301],
        '/referans-galerisi/' => ['/projeler/', 301],
        '/blog/category-portfolio/ledajans/' => ['/projeler/', 301],
        '/blog/ic-mekan-led-ekranlar/' => ['/ic-mekan-led-ekran/', 301],
        '/sahne-led-ekran-kiralama/' => ['/rental-ekran/', 301],
        '/blog/led-ekran-7/' => ['/led-ekran/', 301],
        '/blog/colorlight-a200/' => ['/colorlight/', 301],
        '/blog/dis-mekan-led-ekranlar/' => ['/dis-mekan-led-ekran/', 301],
        '/blog/colorlight-x6/' => ['/colorlight/', 301],
        '/antalya-led-ekran/' => ['/led-ekran/', 302], // GEÇİCİ 302 (şehir sayfası)
        '/blog/colorlight-x8m-nedir/' => ['/colorlight/', 301],
        '/blog/category-portfolio/ledajans1/' => ['/projeler/', 301],
        '/blog/tayor-serisi-outdoor-fixed-full-color-cabinet-nedir/' => ['/dis-mekan-led-ekran/', 301],
        '/blog/led-ekran-2/' => ['/led-ekran/', 301],
        '/led-2/' => ['/led-ekran/', 301],
        '/blog/led/' => ['/led-ekran/', 301],
        '/led/' => ['/led-ekran/', 301],
        '/blog/dis-mekan-led-ekran-ip-parlaklik-dayaniklilik/' => ['/ip-koruma-led-ekran/', 301],
        '/blog/huidu-r712-receiving-card/' => ['/huidu/', 301],
        '/blog/colorlight-x4e-ve-x4m/' => ['/colorlight/', 301],
        '/blog/colorlight-a500/' => ['/colorlight/', 301],
        '/dis-mekan-led-ekranlar/' => ['/dis-mekan-led-ekran/', 301],
        '/ic-mekan-led-ekranlar/' => ['/ic-mekan-led-ekran/', 301],
        '/led-ekran-4/' => ['/led-ekran/', 301],
        '/fuar-rental-led-referansi/' => ['/fuar-led-ekran/', 301],
        '/blog/p10-panel-kirmizi-5/' => ['/p10-kirmizi-panel/', 301], // p10 2026-10-07: zincir tek adıma (eski: → /p10-panel-kirmizi/ → /p10-kirmizi-panel/)
        '/blog/led-ekran-5/' => ['/led-ekran/', 301],
        '/blog/led-ekran-4/' => ['/led-ekran/', 301],
        '/huidu-r712-receiving-card/' => ['/huidu/', 301],
        '/blog/led-ekran-6/' => ['/led-ekran/', 301],
        '/kongre-merkezi-cob-2/' => ['/projeler/', 301],
        '/blog/category/genel/page/6/' => ['/category/genel/', 301],
        '/blog/case/ic-mekan-led-ekran/' => ['/ic-mekan-led-ekran/', 301],
        '/gallery/' => ['/projeler/', 301],
        '/bursa-osb-led-referansi/' => ['/projeler/', 301],
        '/blog/led-ekran-3/' => ['/led-ekran/', 301],
        '/led-ekran-6/' => ['/led-ekran/', 301],
        '/blog/case/rental-ekran/' => ['/rental-ekran/', 301],
        '/blog/category/genel/page/5/' => ['/category/genel/', 301],
        '/blog/case/power-supply-2/' => ['/guc-kaynaklari/', 301],
        '/blog/led-2/' => ['/led-ekran/', 301],
        '/portfolio-02/' => ['/projeler/', 301],
        '/blog/case/' => ['/projeler/', 301],
        '/ankara-stadyum-led-referansi/' => ['/projeler/', 301],
        '/blog/case/ic-mekan-rgb-panel/' => ['/ic-mekan-rgb-panel/', 301],
        '/blog/tf-qs2n-kontrol-karti-2/' => ['/tf-qs2n-kontrol-karti/', 301],
        '/blog/category/genel/' => ['/category/genel/', 301],
        '/led-ekran-3/' => ['/led-ekran/', 301],
        '/p10-panel-kirmizi-4/' => ['/p10-kirmizi-panel/', 301], // p10 2026-10-07: zincir tek adıma (eski: → /p10-panel-kirmizi/ → /p10-kirmizi-panel/)
        '/de-case-rgb-panel-fuer-den-aussenbereich/' => ['/projeler/', 301],
        '/sozluk/' => ['/blog/', 301],
        '/blog/case/dis-mekan-led-ekran/' => ['/dis-mekan-led-ekran/', 301],
        '/tf-qs2n-kontrol-karti-2/' => ['/tf-qs2n-kontrol-karti/', 301],
        '/hakkımızda/' => ['/hakkimizda/', 301],
        '/led-ekran-fiyatlari-2026-2/' => ['/led-ekran-fiyatlari-2026/', 301],
        '/cob-led-ekran-ekran-nedir-avantajlari/' => ['/cob-led-ekran-nedir-avantajlari/', 301],
        '/feed/' => ['/', 301],
        '/comments/feed/' => ['/', 301],
        '/case/' => ['/projeler/', 301],
        '/case/control-cards/' => ['/en/control-cards/', 301], // R5-v37 2026-10-04 (portfolio 104 → Polylang en karşılığı)
        '/case/kontrol-kartlari-gva-eski/' => ['/kontrol-kartlari/', 301],
        '/case/steuerkarten/' => ['/de/steuerkarten/', 301], // R5-v37 2026-10-04 (portfolio 3192 → Polylang de karşılığı)
        '/case/dis-mekan-led-ekran/' => ['/dis-mekan-led-ekran/', 301],
        '/case/outdoor-led-screen/' => ['/en/outdoor-led-screen/', 301], // R5-v37 2026-10-04 (portfolio 1143 → Polylang en karşılığı)
        '/case/ic-mekan-led-ekran-gva-eski/' => ['/ic-mekan-led-ekran/', 301],
        '/case/indoor-led-display/' => ['/en/indoor-led-screen/', 301], // R5-v37 2026-10-04 (portfolio 100 → Polylang en karşılığı)
        '/case/led-anzeige-fuer-den-innenbereich/' => ['/de/indoor-led-display/', 301], // R5-v37 2026-10-04 (portfolio 3250 → Polylang de karşılığı)
        '/case/ic-mekan-rgb-panel-gva-eski/' => ['/ic-mekan-rgb-panel/', 301],
        '/case/indoor-rgb-panels/' => ['/en/indoor-rgb-panel/', 301], // R5-v37 2026-10-04 (portfolio 101 → Polylang en karşılığı)
        '/case/rgb-panel-fuer-den-innenbereich/' => ['/de/rgb-panel-innen/', 301], // R5-v37 2026-10-04 (portfolio 3207 → Polylang de karşılığı)
        '/case/outdoor-rgb-panel/' => ['/en/outdoor-rgb-panel/', 301], // R5-v37 2026-10-04 (portfolio 103 → Polylang en karşılığı)
        '/case/rgb-panel-fuer-den-aussenbereich/' => ['/dis-mekan-rgb-panel/', 301],
        '/de/indoor-rgb-panel-de/' => ['/de/rgb-panel-innen/', 301], // SLUG 2026-10-01 (Ceviri slug değişikliği, 5474)
        '/de/outdoor-rgb-panel-de/' => ['/de/rgb-panel-aussen/', 301], // SLUG 2026-10-01 (Ceviri slug değişikliği, 5476)
        '/en/blog/'   => ['/en/articles/', 301], // v44 2026-10-07: EN blog 5351 slug 'articles' (eski: → /en/blog-2/)
        '/en/blog-2/' => ['/en/articles/', 301], // v44: eski 5351 slug'ı
        '/blog-2/'    => ['/en/articles/', 301], // v44: dil önekisiz eski 5351 adresi
        '/de/blog/'   => ['/de/artikel/', 301],  // v44 2026-10-07: DE blog 5352 slug 'artikel' (eski: → /de/blog-3/)
        '/de/blog-3/' => ['/de/artikel/', 301],  // v44: eski 5352 slug'ı
        '/blog-3/'    => ['/de/artikel/', 301],  // v44: dil önekisiz eski 5352 adresi
        '/en/our-brands/' => ['/en/brands/', 301], // v45 2026-10-07: eski EN markalar adresi (404) → /en/brands/ (200)
        '/case/power-supply/' => ['/en/power-supplies/', 301], // R5-v37 2026-10-04 (portfolio 1528 → Polylang en karşılığı)
        '/case/power-supply-2/' => ['/guc-kaynaklari/', 301],
        '/case/stromversorgung/' => ['/de/netzteile/', 301], // R5-v37 2026-10-04 (portfolio 3182 → Polylang de karşılığı)
        '/case/rental-cabin/' => ['/en/rental-led-screen/', 301], // R5-v37 2026-10-04 (portfolio 102 → Polylang en karşılığı)
        '/case/rental-ekran-gva-eski/' => ['/rental-ekran/', 301],
        '/case/mietbildschirm/' => ['/de/rental-led-display/', 301], // R5-v37 2026-10-04 (portfolio 3141 → Polylang de karşılığı)
        '/case/verleihbildschirm/' => ['/de/rental-led-display/', 301], // R5-v37 2026-10-04 (portfolio 3111 → Polylang de karşılığı)
        '/en/case/' => ['/en/projects/', 301], // PROVISIONAL (çeviri botu ile tekrar bakılacak)
        '/de/case/' => ['/de/projekte/', 301], // PROVISIONAL (çeviri botu ile tekrar bakılacak)
        '/en/feed/' => ['/en/', 301], // PROVISIONAL (çeviri botu ile tekrar bakılacak)
        '/de/feed/' => ['/de/', 301], // PROVISIONAL (çeviri botu ile tekrar bakılacak)
        '/en/category/genel-en/' => ['/en/category/general/', 301], // CAT 2026-10-01 (Ceviri kategori slug değişikliği, terim 50)
        '/de/category/genel-de/' => ['/de/category/allgemein/', 301], // CAT 2026-10-01 (terim 107)
        '/en/gob-vs-cob-smd-2/' => ['/en/gob-vs-cob-vs-smd-led/', 301], // S1 2026-10-04 (Ceviri slug değişikliği, 5309)
        '/de/gob-vs-cob-smd-3/' => ['/de/gob-cob-smd-led-vergleich/', 301], // S1 2026-10-04 (Ceviri slug değişikliği, 5310)
        '/en/colorlight-4/' => ['/en/colorlight-control-cards/', 301], // S1 2026-10-04 (Ceviri slug değişikliği, 5341)
        '/de/colorlight-5/' => ['/de/colorlight-steuerkarten/', 301], // S1 2026-10-04 (Ceviri slug değişikliği, 5342)
        '/en/huidu-2/' => ['/en/huidu-control-cards/', 301], // S1 2026-10-04 (Ceviri slug değişikliği, 5339)
        '/de/huidu-3/' => ['/de/huidu-steuerkarten/', 301], // S2 2026-10-04 (Ceviri slug değişikliği, 5340)
        '/de/software-download-de/' => ['/de/software-herunterladen/', 301], // SW 2026-10-04 (Ceviri slug değişikliği, 5477)
        '/de/huidu-hdplayer-download-de/' => ['/de/huidu-hdplayer-herunterladen/', 301], // HDP 2026-10-04 (Ceviri slug değişikliği, 5473)
        '/de/indoor-vs-outdoor-led-de/' => ['/de/innen-vs-aussen-led-display/', 301], // S2 2026-10-04 (Ceviri slug değişikliği, 5475)
        '/de/uber-uns/' => ['/de/ueber-uns/', 301], // S2 2026-10-04 (Ceviri slug değişikliği, 3042)
        '/led-led-ekran/' => ['/led-ekran-nedir/', 301], // v39 2026-10-05 (yazı 606, eski 2024 sözlük yazısı → 4963 kapsamlı rehber)
        '/p10-kirmizi-panel-2/' => ['/p10-kirmizi-panel/', 301], // v39 2026-10-05 (yazı 652, aynı başlıklı kopya → 646)
        '/p10-panel-kirmizi/' => ['/p10-kirmizi-panel/', 301], // v39 2026-10-05 (yazı 620, aynı konulu kopya → 646; SEO isteği)
        '/led-panel/' => ['/led-panel-nedir/', 301], // v41a 2026-10-05 (yazı 660, eski 2024 LED panel yazısı → 4983 LED Panel Nedir; SEO isteği)
        '/led-nit-mcd/' => ['/nits-parlaklik-nedir/', 301], // v41a 2026-10-05 (yazı 609, eski 2024 nit/mcd yazısı → 4981 Nits Nedir; SEO isteği)
    ];
}
add_action('template_redirect', function () {
    if (is_admin() || wp_doing_ajax() || (defined('REST_REQUEST') && REST_REQUEST)) { return; }
    $uri  = isset($_SERVER['REQUEST_URI']) ? (string) wp_unslash($_SERVER['REQUEST_URI']) : '/';
    $path = str_replace('İ', 'i', rawurldecode((string) strtok($uri, '?'))); // 'İ' -> 'i' (mb_strtolower birleşik nokta üretmesin); ASCII 'I' -> 'ı' YAPILMAZ
    $path = function_exists('mb_strtolower') ? mb_strtolower($path, 'UTF-8') : strtolower($path);
    $path = '/' . trim($path, '/');
    $path = ($path === '/') ? '/' : $path . '/';
    // i18n eklentisinden devralınan: /gva_template* -> 410
    if (strpos($path, '/gva_template') === 0) { status_header(410); nocache_headers(); exit; }
    $map = ldj_r5_redirect_map();
    if (isset($map[$path])) {
        list($to, $code) = $map[$path];
        $dest = home_url($to);
        $qs   = isset($_SERVER['QUERY_STRING']) ? (string) wp_unslash($_SERVER['QUERY_STRING']) : '';
        if ($qs !== '') { $dest .= (strpos($dest, '?') === false ? '?' : '&') . $qs; } // sorgu dizesi korunur; hedefte ? varsa & ile
        wp_redirect($dest, (int) $code, 'LDJ-R5');
        exit;
    }
}, -2);

// R5-c) Gerçek 404: ana sayfaya 301 kuralını kaldır; WordPress hiçbir şey bulamazsa 404 + child 404.php.
//       (/en/ ve /de/ için ayrı kural YOK; yalnızca WP bulamazsa 404 olur.)
add_action('wp', function () {
    remove_action('template_redirect', 'redirect_404_to_homepage', 10); // functions.php satır 129: öncelik belirtilmemiş = 10
}, 99);

// R5-d) Menüde sonda eğik çizgi eksik link (Elementor header şablonu 43) — yalnızca çıktı, birebir eşleşme.
add_action('template_redirect', function () {
    if (is_admin() || wp_doing_ajax() || is_feed()) { return; }
    ob_start(function ($html) {
        return str_replace('href="https://tester.ledajans.com/dis-mekan-led-ekran"', 'href="https://tester.ledajans.com/dis-mekan-led-ekran/"', $html);
    });
}, 20);

// ===== LEDAJANS R5 B3-JS (GEÇİCİ) — /rental-ekran/ slider 17e560c: 404 görselli slaytları widget JS'inin atlaması için inline gizle =====
// Geri alma: bu bloğu child functions.php'den silmek. Elementor verisine dokunmaz.
add_action('wp_footer', function () {
    if (!is_page(['rental-ekran', 'rental-led-screen', 'rental-led-display'])) { return; }
    ?><script>(function(){var re=/(500×1000|500%C3%971000|Air-II)-scaled\.png/;
document.querySelectorAll('.elementor-element-17e560c .product-slide img').forEach(function(img){
 if(re.test(img.getAttribute('src')||'')){img.style.display='none';img.parentElement.style.display='none';}});})();</script><?php
}, 99);

// ===== LEDAJANS R5-D — x-default hreflang (TR sürümü), yalnızca Polylang eklemediyse =====
// Polylang free 3.8.9: 'pll_rel_hreflang_attributes' filtresi [hreflang => url] dizisi alır.
add_filter('pll_rel_hreflang_attributes', function ($hreflangs) {
    if (!is_array($hreflangs) || isset($hreflangs['x-default'])) { return $hreflangs; }
    $def = function_exists('pll_default_language') ? pll_default_language('slug') : 'tr';
    foreach ([$def, 'tr', 'tr-TR', 'tr_TR'] as $k) {
        if (!empty($hreflangs[$k])) { $hreflangs['x-default'] = $hreflangs[$k]; break; }
    }
    return $hreflangs;
}, 20);

// ===== LEDAJANS R6-6 (TASLAK, tester) — /rental-ekran/ + EN/DE: 404 görsellerin HİÇ istenmemesi =====
// Elementor verisi DEĞİŞMEZ; yalnızca render çıktısı. Kapsam: widget 17e560c (slider) ve faefd9a (Grand Air kutusu),
// yalnızca 3 rental sayfası, yalnızca birebir dosya adları. <img> korunur (B3 seçicileri için), src -> data-ldj-src-off,
// srcset/sizes kaldırılır, onerror kaldırılır, sınıf ldj-img-off eklenir. Geri alma: bu bloğu silmek.
function ldj_r6_is_rental_page() {
    return !is_admin() && is_page(array('rental-ekran', 'rental-led-screen', 'rental-led-display'));
}
function ldj_r6_img_off($html) {
    $re = '#<img\b[^>]*\bsrc=(["\'])[^"\']*/(?:Air-II-scaled|500(?:×|%C3%97|%c3%97|x)1000-scaled)\.png\1[^>]*>#u';
    $out = preg_replace_callback($re, function ($m) {
        $tag = $m[0];
        $tag = preg_replace('#\s(?:srcset|sizes|onerror)=("[^"]*"|\'[^\']*\')#i', '', $tag);
        $tag = preg_replace('#\ssrc=#i', ' data-ldj-src-off=', $tag, 1);
        if (preg_match('#\sclass=(["\'])#i', $tag)) {
            $tag = preg_replace('#\sclass=(["\'])#i', ' class=$1ldj-img-off ', $tag, 1);
        } else {
            $tag = preg_replace('#^<img\b#i', '<img class="ldj-img-off"', $tag, 1);
        }
        return $tag;
    }, $html);
    return $out === null ? $html : $out;
}
add_filter('elementor/widget/render_content', function ($content, $widget) {
    if (!ldj_r6_is_rental_page() || !is_object($widget) || !method_exists($widget, 'get_id')) { return $content; }
    if (!in_array($widget->get_id(), array('17e560c', 'faefd9a'), true)) { return $content; }
    return ldj_r6_img_off($content);
}, 20, 2);
// B3 uyarlaması: slider JS'inin atlaması için inline gizleme artık data-ldj-src-off / .ldj-img-off üzerinden.
add_action('wp_footer', function () {
    if (!ldj_r6_is_rental_page()) { return; }
    ?><script>(function(){document.querySelectorAll('.elementor-element-17e560c .product-slide img.ldj-img-off').forEach(function(img){img.style.display='none';img.parentElement.style.display='none';});})();</script><?php
}, 99);

// ===== LEDAJANS R6-2 (TASLAK) — ilk ekran görselleri, KÜÇÜK PARTİLER (her parti ayrı deploy + ölçüm) =====
// Ceviri'nin şu an düzenlediği sayfalar (1242, 5001, 5002, 5003, 5010 ve EN/DE karşılıkları) BİLİNÇLİ OLARAK KAPSAM DIŞI.

// --- Parti 1: klasik sayfalarda LCP içerik görseli loading="lazy" (içerikte sabit yazılmış, iki kez) ---
// Ölçüm: /magaza-vitrin-led-ekran/ (4969) ve /fuar-led-ekran/ (4992) — 390 ve 1440'ta LCP = ilk içerik <img>, lazy.
add_filter('wp_content_img_tag', function ($tag, $context, $attachment_id) {
    static $done = false;
    if ($done || $context !== 'the_content' || is_admin() || !is_page(array(4969, 4992))) { return $tag; }
    $done = true; // yalnızca sayfadaki İLK içerik görseli
    $tag = preg_replace('#\sloading=(["\'])lazy\1#i', '', $tag);
    if (stripos($tag, 'fetchpriority=') === false) {
        $tag = preg_replace('#^<img\b#i', '<img fetchpriority="high"', $tag, 1);
    }
    return $tag;
}, 20, 3);


// --- Parti 2: tek yazılarda tema breadcrumb arka planı LCP (breadcrumb-01.jpg, 124 KB; 390'da LCP 9,8 sn ölçüldü) ---
// Arka plan tema CSS'inden geliyor (HTML'de yok) → tarayıcı geç keşfediyor; erken preload.
add_action('wp_head', function () {
    if (is_admin() || !is_singular('post')) { return; }
    $u = content_url('/uploads/2023/01/breadcrumb-01.jpg');
    echo '<link rel="preload" as="image" href="' . esc_url($u) . '" fetchpriority="high">' . "\n";
}, 2);


// --- Parti 3: Elementor HTML slider'larında ilk (active) görsel LCP ama fetchpriority yok (1440) ---
// Ölçüm: /kontrol-kartlari/ (5008, widget e16780a) ve /dis-mekan-rgb-panel/ (3795, widget acf1258).
add_filter('elementor/widget/render_content', function ($content, $widget) {
    if (is_admin() || !is_object($widget) || !method_exists($widget, 'get_id')) { return $content; }
    $map = array(5008 => 'e16780a', 3795 => 'acf1258');
    $pid = (int) get_queried_object_id();
    if (!isset($map[$pid]) || $widget->get_id() !== $map[$pid]) { return $content; }
    // İlk <img> (active slayt): lazy varsa kaldır, fetchpriority="high" ekle — yalnızca bir kez.
    $out = preg_replace_callback('#<img\b[^>]*>#i', function ($m) {
        $t = preg_replace('#\sloading=(["\'])lazy\1#i', '', $m[0]);
        return stripos($t, 'fetchpriority=') === false ? preg_replace('#^<img\b#i', '<img fetchpriority="high"', $t, 1) : $t;
    }, $content, 1);
    return $out === null ? $content : $out;
}, 20, 2);

// ===== LEDAJANS TR 404 başlığı (Control onaylı 2026-10-01) — yalnızca is_404() + Polylang dili 'tr' =====
// Rank Math ayarı ve Polylang dizeleri DEĞİŞMEZ; EN/DE 404 başlıkları olduğu gibi kalır. Rank Math/Polylang yoksa no-op.
add_filter('rank_math/frontend/title', function ($title) {
    if (is_admin() || !function_exists('is_404') || !is_404() || !function_exists('pll_current_language')) { return $title; }
    return (pll_current_language('slug') === 'tr') ? 'Sayfa Bulunamadı - LEDAJANS' : $title;
}, 99);

/* ==========================================================================
 * REST-SNIPPET 2026-10-01: Elementor Pro Custom Code (elementor_snippet)
 * REST uçlarını oturumu olmayan ziyaretçiye kapatır (401).
 * Kapsam: /wp/v2/elementor_snippet ve tüm alt yolları (/<id>, /<id>/revisions,
 * /<id>/autosaves ...), büyük/küçük harf duyarsız. Diğer REST uçlarına dokunmaz.
 * Ön yüz çıktısı (Custom Code'un sayfaya basılması) REST kullanmaz; etkilenmez.
 * Geri alma: work/rest-snippet/ROLLBACK.md
 * ========================================================================== */
if ( ! function_exists( 'ledajans_rest_snippet_guard' ) ) {
    function ledajans_rest_snippet_guard( $result, $server, $request ) {
        if ( ! ( $request instanceof WP_REST_Request ) ) {
            return $result;
        }
        $route = '/' . ltrim( strtolower( (string) $request->get_route() ), '/' );
        if ( ! preg_match( '#^/wp/v2/elementor_snippet(?:/|$)#', $route ) ) {
            return $result;
        }
        // Çerez + geçerli X-WP-Nonce (veya uygulama parolası) ile gelen yetkili kullanıcı geçer.
        if ( function_exists( 'current_user_can' ) && current_user_can( 'edit_posts' ) ) {
            return $result;
        }
        $status = function_exists( 'rest_authorization_required_code' ) ? rest_authorization_required_code() : 401; // anonim: 401, yetkisiz üye: 403
        return new WP_Error( 'rest_forbidden', 'Bu kaynağa erişim izniniz yok.', array( 'status' => $status ) );
    }
    if ( function_exists( 'add_filter' ) ) {
        add_filter( 'rest_pre_dispatch', 'ledajans_rest_snippet_guard', 10, 3 );
    }
}

/* =============================================================================
 * LEDAJANS HF-i18n v3 (TASLAK 2026-10-04 — UYGULANMADI; taban functions.php sha256 862c43d6…94acb) — EN/DE header + footer dili
 *  v3 (Control/SEO/Ceviri/Mücahid kararları): kod içi EN/DE varsayılan metinleri (Polylang dizesi girilirse o kazanır);
 *  logo <a> href'i EN/DE ana sayfaya (img/src/srcset/class/boyut aynı); JSON-LD WebSite url+name; site adı üç kaynakta
 *  aynı sabit metin; COB – (yarım çizgi) yalnız EN/DE; 5024 modal JS DE metni yalnız EN/DE çıktısında (P3b iptal – Control);
 *  EN/DE ana sayfa @graph WebSite url'si dil ana sayfasına (HF-9);
 *  +44/+49 telefon metinleri ve tel: linkleri bu blokta HİÇ işlenmez (Mücahid kararı).
 *  Mücahid kuralları: EN/DE header/footer = TR yapısının birebir kopyası; metinler o dilde;
 *  linkler o dildeki karşılık sayfanın GÜNCEL permalinki (pll_get_post + get_permalink, sabit URL yok);
 *  menüler Polylang menü konumundan (primary → TR 42 / EN 99 / DE 119); serbest metinler pll__().
 *  Yalnızca ön yüz HTML çıktısı; veritabanına hiçbir şey yazmaz.
 *
 *  TR GARANTİSİ:
 *   - HF-1/HF-2 (header menüsü + footer) çıktı tamponu YALNIZCA pll_current_language() 'en' veya 'de'
 *     iken açılır. TR isteğinde ob_start çağrılmaz → TR HTML'i bu bloktan byte düzeyinde etkilenmez.
 *   - HF-3 (dil değiştirici) v2'de varsayılan olarak TR'de ÇALIŞMAZ (LDJ_HF_TR_SWITCHER=false). true yapılırsa TR'de yalnızca header widget c7f7f5c içindeki 6 adet
 *     dil linkinin href DEĞERİNİ değiştirir (metin, sınıf, sıra, görünüm aynı → piksel aynı).
 *     İstenirse LDJ_HF_TR_SWITCHER=false ile TR tamamen dışarıda bırakılır.
 *   - Logo: bu blok logoya (img/src/href) HİÇ dokunmaz.
 *  Element Cache: Elementor "Element Cache" 24 saat açık (Ayarlar > Performans). Bu yüzden
 *  elementor/widget/render_content yerine son HTML üzerinde çalışılır (önbellekteki widget çıktısı
 *  dilden bağımsız saklanıyor; render filtresi dil karışıklığı yapabilirdi).
 *  Sıra: tampon öncelik 9 → mevcut custom_translate_output (öncelik 10, str_replace) ÖNCE çalışır,
 *  bu blok SONRA çalışır; eşleştirme metne değil yapıya (id/sınıf/href) dayanır, eski çeviri
 *  çıktısından etkilenmez ve "Galerie"→"Galeriee" türü çift değişim olmaz.
 *  Geri alma: bu bloğu silmek (functions.php yedeğini geri yüklemek) + WPSC önbelleği temizlemek.
 * ============================================================================= */
if (!defined('LDJ_HF_MODAL_JS')) { define('LDJ_HF_MODAL_JS', true); } // v3: 5024 modal JS DE metni yalnız EN/DE çıktısında düzeltilir (P3b iptal – Control; 5024'e dokunulmaz, TR baytları değişmez)
if (!defined('LDJ_HF_TR_SWITCHER')) { define('LDJ_HF_TR_SWITCHER', true); } // v35: TR'de AÇIK — yalnız şablon 43'teki 6 dil linkinin href'i sayfanın karşılığına (HF-3); karşılık yoksa sabit href kalır

function ldj_hf_lang() {
    if (!function_exists('pll_current_language')) { return ''; }
    $l = pll_current_language('slug');
    return is_string($l) ? $l : '';
}

/* --- Çevrilecek dizeler (Polylang > Diller > Dizeler çevirileri, grup "LEDAJANS Header/Footer") --- */
function ldj_hf_strings() {
    return array(
        // header (c7f7f5c)
        'Menüyü aç', 'Menüyü kapat', 'Telefon ile iletişim', 'Tüm ürünler',
        // footer 5024
        "LEDAJANS, LED ekran, kayan yazı ve dijital tabela sistemlerinde kullanılan LED modül, güç kaynağı, kontrol kartı ve aksesuar satışında Türkiye'nin güvenilir adresidir. Toptan ve perakende satış, teknik destek ve hızlı kargo ile sektör profesyonellerinin yanındadır.",
        'Hızlı Linkler', 'Galeri', 'Ürünler', 'İletişim',
        'Anasayfa', 'Hakkımızda', 'Firma Bilgilerimiz', 'Blog', 'Colorlight', 'Huidu', 'Google Drive',
        'COB - Smart Screen', 'İç Mekan LED Ekran', 'Dış Mekan LED Ekran', 'Rental Ekran',
        'İç Mekan RGB Panel', 'Dış Mekan RGB Panel', 'Kontrol Kartları', 'Güç Kaynakları',
        'Halide Edip Adıvar Mah. Gül 2 Sk. No:10, 34382 Şişli/İstanbul',
        'Galeri %s görselini büyüt', 'Galeri görseli %s',
        'Ledajans. Tüm hakları saklıdır.', 'Kapat', 'Büyütülmüş galeri görseli',
        // header masaüstü/mobil menü dışı etiketler (menü etiketleri Polylang dil menülerinden gelir)
    );
}
add_action('init', function () {
    if (!function_exists('pll_register_string')) { return; }
    foreach (ldj_hf_strings() as $s) {
        pll_register_string('ldj_hf_' . substr(md5($s), 0, 8), $s, 'LEDAJANS Header/Footer', strlen($s) > 80);
    }
});
/* v3: kod içi varsayılan çeviriler (Ceviri onaylı metinler, 4 Eki 2026). Öncelik: Polylang dize çevirisi (P4) →
 * bu tablo → TR kaynak. Böylece P3 ile P4 arasında EN/DE'de TR metin görünmez. TR'de bu fonksiyon çağrılmaz. */
function ldj_hf_defaults() {
    return array(
        'en' => array(
            'Menüyü aç' => 'Open menu', 'Menüyü kapat' => 'Close menu', 'Telefon ile iletişim' => 'Call us', 'Tüm ürünler' => 'All Products',
            "LEDAJANS, LED ekran, kayan yazı ve dijital tabela sistemlerinde kullanılan LED modül, güç kaynağı, kontrol kartı ve aksesuar satışında Türkiye'nin güvenilir adresidir. Toptan ve perakende satış, teknik destek ve hızlı kargo ile sektör profesyonellerinin yanındadır."
                => "LEDAJANS is Turkey's trusted source for LED modules, power supplies, control cards and accessories used in LED screens, scrolling text displays and digital signage systems. With wholesale and retail sales, technical support and fast shipping, LEDAJANS stands by industry professionals.",
            'Hızlı Linkler' => 'Quick Links', 'Galeri' => 'Gallery', 'Ürünler' => 'Products', 'İletişim' => 'Contact',
            'Anasayfa' => 'Home', 'Hakkımızda' => 'About Us', 'Firma Bilgilerimiz' => 'Company Information', 'Blog' => 'Blog',
            'Colorlight' => 'Colorlight', 'Huidu' => 'Huidu', 'Google Drive' => 'Google Drive',
            'COB - Smart Screen' => 'COB – Smart Screen', 'İç Mekan LED Ekran' => 'Indoor LED Screen', 'Dış Mekan LED Ekran' => 'Outdoor LED Screen',
            'Rental Ekran' => 'LED Screen Rental', 'İç Mekan RGB Panel' => 'Indoor RGB Panel', 'Dış Mekan RGB Panel' => 'Outdoor RGB Panel',
            'Kontrol Kartları' => 'Control Cards', 'Güç Kaynakları' => 'Power Supplies',
            'Halide Edip Adıvar Mah. Gül 2 Sk. No:10, 34382 Şişli/İstanbul' => 'Halide Edip Adıvar Mah. Gül 2 Sk. No:10, 34382 Şişli/Istanbul, Turkey',
            'Galeri %s görselini büyüt' => 'Enlarge gallery image %s', 'Galeri görseli %s' => 'Gallery image %s',
            'Ledajans. Tüm hakları saklıdır.' => 'Ledajans. All rights reserved.', 'Kapat' => 'Close',
            'Büyütülmüş galeri görseli' => 'Enlarged gallery image', 'Son Yazılar' => 'Recent Posts',
        ),
        'de' => array(
            'Menüyü aç' => 'Menü öffnen', 'Menüyü kapat' => 'Menü schließen', 'Telefon ile iletişim' => 'Rufen Sie uns an', 'Tüm ürünler' => 'Alle Produkte',
            "LEDAJANS, LED ekran, kayan yazı ve dijital tabela sistemlerinde kullanılan LED modül, güç kaynağı, kontrol kartı ve aksesuar satışında Türkiye'nin güvenilir adresidir. Toptan ve perakende satış, teknik destek ve hızlı kargo ile sektör profesyonellerinin yanındadır."
                => 'LEDAJANS ist in der Türkei die verlässliche Adresse für LED-Module, Netzteile, Steuerkarten und Zubehör, die in LED-Displays, Laufschriften und Digital-Signage-Systemen eingesetzt werden. Mit Groß- und Einzelhandel, technischem Support und schnellem Versand steht LEDAJANS Branchenprofis zur Seite.',
            'Hızlı Linkler' => 'Schnellzugriff', 'Galeri' => 'Galerie', 'Ürünler' => 'Produkte', 'İletişim' => 'Kontakt',
            'Anasayfa' => 'Startseite', 'Hakkımızda' => 'Über uns', 'Firma Bilgilerimiz' => 'Firmeninformationen', 'Blog' => 'Blog',
            'Colorlight' => 'Colorlight', 'Huidu' => 'Huidu', 'Google Drive' => 'Google Drive',
            'COB - Smart Screen' => 'COB – Smart Screen', 'İç Mekan LED Ekran' => 'Indoor-LED-Display', 'Dış Mekan LED Ekran' => 'Outdoor-LED-Display',
            'Rental Ekran' => 'LED-Wand mieten', 'İç Mekan RGB Panel' => 'Indoor-RGB-Panel', 'Dış Mekan RGB Panel' => 'Outdoor-RGB-Panel',
            'Kontrol Kartları' => 'Steuerkarten', 'Güç Kaynakları' => 'Netzteile',
            'Halide Edip Adıvar Mah. Gül 2 Sk. No:10, 34382 Şişli/İstanbul' => 'Halide Edip Adıvar Mah. Gül 2 Sk. No:10, 34382 Şişli/Istanbul, Türkei',
            'Galeri %s görselini büyüt' => 'Galeriebild %s vergrößern', 'Galeri görseli %s' => 'Galeriebild %s',
            'Ledajans. Tüm hakları saklıdır.' => 'Ledajans. Alle Rechte vorbehalten.', 'Kapat' => 'Schließen',
            'Büyütülmüş galeri görseli' => 'Vergrößertes Galeriebild', 'Son Yazılar' => 'Neueste Beiträge',
        ),
    );
}
function ldj_hf_t($s) {
    $t = function_exists('pll__') ? pll__($s) : $s;
    if ($t !== $s && $t !== '') { return $t; }                 // Polylang dize çevirisi girilmiş
    $l = ldj_hf_lang(); $d = ldj_hf_defaults();
    return isset($d[$l][$s]) ? $d[$l][$s] : $s;              // kod varsayılanı (yalnız en/de) → yoksa kaynak
}

/* --- TR sayfa ID'si → hedef dildeki yayınlanmış çevirinin güncel permalinki (yoksa dil ana sayfası) --- */
function ldj_hf_home($lang) { // dil ana sayfası, her zaman sonda eğik çizgi (/en/, /de/)
    return trailingslashit(function_exists('pll_home_url') ? pll_home_url($lang) : home_url('/'));
}
/* Karşılığı olmayan TR sayfa için geçici hedef: en yakın TR hub'ın o dildeki çevirisi (EKSIK-SAYFALAR.tsv);
 * o da yoksa dil ana sayfası. 2026-10-04 itibarıyla header/footer/menüdeki 19 hedefin hepsinin EN/DE
 * karşılığı var → bu yol bugün hiç kullanılmıyor; yalnızca ileride sayfa kaldırılırsa TR'ye link çıkmasın diye. */
function ldj_hf_url_for($tr_id, $lang, $hub_tr_id = 0) {
    if ((int) $tr_id === 0) { return ldj_hf_home($lang); }
    $t = function_exists('pll_get_post') ? (int) pll_get_post((int) $tr_id, $lang) : 0;
    if ($t && get_post_status($t) === 'publish') { return get_permalink($t); }
    if ($hub_tr_id) { return ldj_hf_url_for($hub_tr_id, $lang, 0); }
    return ldj_hf_home($lang);
}
function ldj_hf_tr_url($tr_id) {
    return (int) $tr_id === 0 ? ldj_hf_home('tr') : get_permalink((int) $tr_id);
}

/* --- Footer 5024 link tablosu: TR sayfa ID'si => TR etiketi (sıra = TR sırası; yapı değişmez) --- */
function ldj_hf_footer_links() {
    return array(
        0 => 'Anasayfa', 1242 => 'Hakkımızda', 1238 => 'Firma Bilgilerimiz', 27 => 'Blog', 1262 => 'İletişim',
        4081 => 'Colorlight', 5017 => 'Huidu',
        3958 => 'COB - Smart Screen', 5002 => 'İç Mekan LED Ekran', 5003 => 'Dış Mekan LED Ekran', 5004 => 'Rental Ekran',
        5006 => 'İç Mekan RGB Panel', 3795 => 'Dış Mekan RGB Panel', 5008 => 'Kontrol Kartları', 5009 => 'Güç Kaynakları',
    );
}

/* --- HF-1: header menüsü (masaüstü ul#menu-anamenu-desktop + widget içi mobil ul#menu-anamenu) --- */
function ldj_hf_menu_tree() {
    $locs = get_nav_menu_locations(); // Polylang ön yüzde dil menüsünü döndürür (EN 99 / DE 119)
    if (empty($locs['primary'])) { return null; }
    $items = wp_get_nav_menu_items((int) $locs['primary']);
    if (!is_array($items) || !$items) { return null; }
    $by_parent = array();
    foreach ($items as $it) {
        $cls = is_array($it->classes) ? $it->classes : array();
        if (in_array('lang-item', $cls, true)) { continue; } // Polylang'ın açtığı dil alt öğeleri: aşağıda pll_the_languages ile üretilir
        $by_parent[(int) $it->menu_item_parent][] = $it;
    }
    return $by_parent;
}
function ldj_hf_switcher_links() {
    if (!function_exists('pll_the_languages')) { return array(); }
    $raw = pll_the_languages(array('raw' => 1, 'hide_if_no_translation' => 0, 'hide_current' => 0, 'force_home' => 0));
    return is_array($raw) ? $raw : array();
}
function ldj_hf_render_menu($mode, $lang, $orig_switch = null) {
    $tree = ldj_hf_menu_tree();
    if (!$tree || empty($tree[0])) { return null; }
    $e = function ($s) { return esc_html($s); };
    $mob = ($mode === 'mobile');
    $sub_label = function ($label) use ($mob, $e) { return $mob ? '<span class="menu-title">' . $e($label) . '</span>' : $e($label); };
    $html = $mob ? '<ul id="menu-anamenu" class="gva-nav-menu gva-mobile-menu">' . "\n" : '<ul id="menu-anamenu-desktop" class="gva-nav-menu gva-main-menu">' . "\n";
    foreach ($tree[0] as $top) {
        $label = $top->title;
        $cls = is_array($top->classes) ? $top->classes : array();
        $is_switch = ($top->url === '#pll_switcher') || in_array('pll-parent-menu-item', $cls, true);
        $hub = 0;
        foreach ($cls as $c) { if (preg_match('/^ldj-hub-(\d+)$/', $c, $m)) { $hub = (int) $m[1]; } }
        $href = $hub ? ldj_hf_url_for($hub, $lang) : $top->url;
        $kids = isset($tree[(int) $top->ID]) ? $tree[(int) $top->ID] : array();
        if ($is_switch && $orig_switch !== null) {
            /* v3.1 (P3.1): dil öğesi yeniden üretilmez. Şablon 43'teki özgün <li> (Polylang bayrak/ad HTML'i dahil
             * tüm işaretleme) bayt bayt korunur; yalnız üst etiketin metin düğümü ("Dil") menü öğesinin DB başlığıyla
             * (post_title: Language / Sprache) değiştirilir. Polylang'ın wp_get_nav_menu_items'ta $top->title'a
             * yazdığı bayrak <img> + <span> HTML'i hiç kullanılmaz, kaçışlanmaz. */
            $txt = ldj_hf_switch_db_title($top, $label);
            $html .= ldj_hf_switch_label($orig_switch, $txt) . "\n";
            continue;
        }
        if ($is_switch) {
            $label = ldj_hf_switch_db_title($top, $label); // v3.1: HTML başlık kaçışlanıp basılmaz
            $html .= '<li class="pll-parent-menu-item menu-item menu-item-has-children">' . "\n"
                . '<a href="#pll_switcher"><span class="item-content"><span class="menu-title">' . $e($label) . '</span><span class="caret"></span></span></a>' . "\n"
                . '<ul class="submenu-inner">' . "\n";
            foreach (ldj_hf_switcher_links() as $slug => $l) {
                $html .= '<li' . ($mob ? '' : ' class="lang-item-' . esc_attr($slug) . '"') . '><a href="' . esc_url($l['url']) . '" hreflang="' . esc_attr($l['locale']) . '" lang="' . esc_attr($l['locale']) . '">' . $sub_label($l['name']) . '</a></li>' . "\n";
            }
            $html .= '</ul>' . "\n" . '</li>' . "\n";
            continue;
        }
        $home = ((int) $top->object_id === (int) get_option('page_on_front') && $top->object === 'page') || untrailingslashit($top->url) === untrailingslashit(ldj_hf_home($lang));
        if (!$kids) {
            $html .= '<li class="menu-item' . ($home ? ' menu-item-home' : '') . '"><a href="' . esc_url($href) . '"><span class="item-content"><span class="menu-title">' . $e($label) . '</span></span></a></li>' . "\n";
            continue;
        }
        $html .= '<li class="menu-item menu-item-has-children">' . "\n"
            . '<a href="' . esc_url($href) . '"><span class="item-content"><span class="menu-title">' . $e($label) . '</span><span class="caret"></span></span></a>' . "\n"
            . '<ul class="submenu-inner">' . "\n";
        if ($mob && $hub) { // TR widget mobil kopyasındaki "Tüm ürünler" satırının karşılığı
            $html .= '<li><a href="' . esc_url($href) . '">' . $sub_label(ldj_hf_t('Tüm ürünler')) . '</a></li>' . "\n";
        }
        foreach ($kids as $k) {
            $blank = ($k->target === '_blank') ? 'target="_blank" rel="noopener" ' : '';
            $html .= '<li><a ' . $blank . 'href="' . esc_url($k->url) . '">' . $sub_label($k->title) . '</a></li>' . "\n";
        }
        $html .= '</ul>' . "\n" . '</li>' . "\n";
    }
    return $html . '</ul>';
}
/* v3.1: $open_tag ile başlayan <ul> içindeki ilk dil öğesi <li class="pll-parent-menu-item…">…</li> (dengeli) — yoksa null */
function ldj_hf_orig_switch_li($seg, $open_tag) {
    $u = strpos($seg, $open_tag);
    if ($u === false) { return null; }
    $i = strpos($seg, '<li class="pll-parent-menu-item', $u);
    if ($i === false) { return null; }
    $d = 0; $off = $i;
    while (preg_match('#<li\b|</li>#i', $seg, $m, PREG_OFFSET_CAPTURE, $off)) {
        $d += ($m[0][0] === '</li>') ? -1 : 1;
        $off = $m[0][1] + strlen($m[0][0]);
        if ($d === 0) { return substr($seg, $i, $off - $i); }
    }
    return null;
}
/* v3.2: dil öğesinin DB başlığı (menü ekranındaki Language / Sprache). Polylang wp_setup_nav_menu_item'da $item->post_title'ı
 * gettext 'Languages' (DE 'Sprachen') ile, wp_get_nav_menu_items'ta $item->title'ı bayrak HTML'iyle değiştirir; ikisi de kullanılmaz. */
function ldj_hf_switch_db_title($top, $label) {
    $t = get_post_field('post_title', (int) $top->ID, 'raw');
    return (is_string($t) && $t !== '') ? $t : wp_strip_all_tags($label);
}
/* v3.1: yalnız üst bağlantının ilk <span class="menu-title">METİN</span> metin düğümü; içinde etiket varsa dokunmaz */
function ldj_hf_switch_label($li, $txt) {
    $out = preg_replace('#(<a href="\#pll_switcher"[^>]*><span class="item-content"><span class="menu-title">)([^<]*)(</span>)#u', '${1}' . str_replace(array('\\', '$'), array('\\\\', '\\$'), esc_html($txt)) . '${3}', $li, 1);
    return ($out === null) ? $li : $out;
}
/* Dengeli <ul>…</ul> bul ve değiştir (yalnızca $start konumundan sonra ilk eşleşme) */
function ldj_hf_replace_ul($html, $open_tag, $new, $from = 0) {
    $i = strpos($html, $open_tag, $from);
    if ($i === false || $new === null) { return $html; }
    $d = 0; $off = $i;
    while (preg_match('#<ul\b|</ul>#i', $html, $m, PREG_OFFSET_CAPTURE, $off)) {
        $d += ($m[0][0] === '</ul>') ? -1 : 1;
        $off = $m[0][1] + strlen($m[0][0]);
        if ($d === 0) { return substr($html, 0, $i) . $new . substr($html, $off); }
    }
    return $html; // dengesiz: dokunma
}
function ldj_hf_header($html, $lang) {
    $a = strpos($html, 'elementor-element-c7f7f5c');
    $b = ($a === false) ? false : strpos($html, '<div id="page-content">', $a);
    if ($a === false || $b === false) { return $html; }
    $seg = substr($html, $a, $b - $a);
    $seg = ldj_hf_replace_ul($seg, '<ul id="menu-anamenu-desktop"', ldj_hf_render_menu('desktop', $lang, ldj_hf_orig_switch_li($seg, '<ul id="menu-anamenu-desktop"')));
    $seg = ldj_hf_replace_ul($seg, '<ul id="menu-anamenu" class="gva-nav-menu gva-mobile-menu">', ldj_hf_render_menu('mobile', $lang, ldj_hf_orig_switch_li($seg, '<ul id="menu-anamenu" class="gva-nav-menu gva-mobile-menu">')));
    $seg = ldj_hf_logo_href($seg, $lang, array('site-branding-logo', 'logo-mm')); // v3: yalnız href
    $seg = str_replace(
        array('aria-label="Menüyü aç"', 'aria-label="Menüyü kapat"', 'aria-label="Telefon ile iletişim"'),
        array('aria-label="' . esc_attr(ldj_hf_t('Menüyü aç')) . '"', 'aria-label="' . esc_attr(ldj_hf_t('Menüyü kapat')) . '"', 'aria-label="' . esc_attr(ldj_hf_t('Telefon ile iletişim')) . '"'),
        $seg);
    return substr($html, 0, $a) . $seg . substr($html, $b);
}

/* --- v3 Logo linki (Control kararı 2): logoyu saran <a>'nın YALNIZ href değeri TR ana sayfadan dil ana sayfasına.
 *  Eşleşme: verilen sınıflardan biri + href = TR kök ('https://tester.ledajans.com/' veya sonda / olmadan).
 *  <a> içindeki img, src, srcset, class, boyut ve diğer öznitelikler bayt bayt aynı kalır. --- */
function ldj_hf_logo_href($seg, $lang, $classes) {
    $root = preg_quote(untrailingslashit(ldj_hf_home_raw()), '#');
    $cls = implode('|', array_map(function ($c) { return preg_quote($c, '#'); }, $classes));
    $r = preg_replace('#(<a class="(?:' . $cls . ')" href=")' . $root . '/?(")#', '${1}' . esc_url(ldj_hf_home($lang)) . '${2}', $seg);
    return ($r === null) ? $seg : $r;
}

/* --- HF-2: footer 5024 (yapıya göre; öğe sayısı/sırası/sınıflar değişmez) --- */
function ldj_hf_footer($html, $lang) {
    $a = strpos($html, '<footer class="ledajans-footer">');
    $b = ($a === false) ? false : strpos($html, '</footer>', $a);
    if ($a === false || $b === false) { return $html; }
    $seg = substr($html, $a, $b - $a);
    $seg = ldj_hf_logo_href($seg, $lang, array('ledajans-footer-brand')); // v3: yalnız href
    $T = 'ldj_hf_t';
    // linkler: TR permalink → hedef dil permalink; etiket → pll__(TR etiketi)
    foreach (ldj_hf_footer_links() as $id => $label) {
        $from = ldj_hf_tr_url($id);
        $to = ldj_hf_url_for($id, $lang);
        $seg = preg_replace('#<a href="' . preg_quote($from, '#') . '" class="ledajans-footer-link">[^<]*</a>#u',
            '<a href="' . esc_url($to) . '" class="ledajans-footer-link">' . esc_html($T($label)) . '</a>', $seg);
    }
    $seg = preg_replace('#(<a href="https://drive\.google\.com/[^"]*"[^>]*class="ledajans-footer-link">)[^<]*(</a>)#u', '${1}' . esc_html($T('Google Drive')) . '${2}', $seg);
    // başlıklar (sıraya göre: Hızlı Linkler, Galeri, Ürünler, İletişim)
    $titles = array('Hızlı Linkler', 'Galeri', 'Ürünler', 'İletişim'); $n = 0;
    $seg = preg_replace_callback('#<h3 class="ledajans-footer-title">[^<]*</h3>#u', function ($m) use (&$n, $titles, $T) {
        $t = isset($titles[$n]) ? $T($titles[$n]) : null; $n++;
        return $t === null ? $m[0] : '<h3 class="ledajans-footer-title">' . esc_html($t) . '</h3>';
    }, $seg);
    // açıklama, adres, telif, galeri aria/alt, modal
    $desc = ldj_hf_strings()[4];
    $seg = preg_replace('#(<p class="ledajans-footer-desc">\s*)[^<]*?(\s*</p>)#u', '${1}' . esc_html($T($desc)) . '${2}', $seg, 1);
    $seg = preg_replace('#(<a href="https://www\.google\.com/maps/[^"]*"[^>]*class="ledajans-footer-contact-text">)[^<]*(</a>)#u', '${1}' . esc_html($T('Halide Edip Adıvar Mah. Gül 2 Sk. No:10, 34382 Şişli/İstanbul')) . '${2}', $seg, 1);
    $seg = preg_replace('#(<span id="ledajans-footer-year"></span> )[^<]*(</p>)#u', '${1}' . esc_html($T('Ledajans. Tüm hakları saklıdır.')) . '${2}', $seg, 1);
    $seg = preg_replace_callback('#(class="ledajans-gallery-btn" data-src="[^"]*" aria-label=")[^"]*?(\d+)[^"]*(")#u', function ($m) use ($T) {
        return $m[1] . esc_attr(sprintf($T('Galeri %s görselini büyüt'), $m[2])) . $m[3];
    }, $seg);
    $seg = preg_replace_callback('#(<button type="button" class="ledajans-gallery-btn"[^>]*>\s*<img\b[^>]*\balt=")[^"]*?(\d+)(")#u', function ($m) use ($T) {
        return $m[1] . esc_attr(sprintf($T('Galeri görseli %s'), $m[2])) . $m[3];
    }, $seg);
    $seg = str_replace('class="ledajans-gallery-modal-close" aria-label="Kapat"', 'class="ledajans-gallery-modal-close" aria-label="' . esc_attr($T('Kapat')) . '"', $seg);
    $seg = preg_replace('#(<img\b[^>]*id="ledajans-gallery-modal-image"[^>]*\balt=")[^"]*(")#u', '${1}' . esc_attr($T('Büyütülmüş galeri görseli')) . '${2}', $seg, 1);
    // NOT: tel: linkleri ve telefon metinleri (+44/+49 dahil) bilerek işlenmez (Mücahid kararı).
    $html = substr($html, 0, $a) . $seg . substr($html, $b);
    // v3: 5024 modal betiğindeki DE yedek alt metni (</footer> sonrası ilk <script>) — yalnız EN/DE çıktısı; TR'de tampon yok.
    if (LDJ_HF_MODAL_JS) {
        $c = strpos($html, '</footer>', $a);
        $s1 = ($c === false) ? false : strpos($html, '<script>', $c);
        $s2 = ($s1 === false) ? false : strpos($html, '</script>', $s1);
        if ($s2 !== false && $s1 - $c < 200) {
            $js = substr($html, $s1, $s2 - $s1);
            $js2 = str_replace('"Vergrößerte Bildansicht"', '"Vergrößertes Galeriebild"', $js);
            $html = substr($html, 0, $s1) . $js2 . substr($html, $s2);
        }
    }
    return $html;
}

/* --- HF-4: Custom Code 5026 "LEDAJANS Header Orange + Logo" (elementor_head) içindeki lockTrLabels()
 *  masaüstü menüde Home/Startseite → "Anasayfa", Contact/Kontakt → "İletişim" (custom_translate_output
 *  sonrası EN'de "Contact Us", DE'de "Kontakt") diye etiketleri İSTEMCİDE geri çeviriyor. HF-1 doğru
 *  etiketi üretse bile ekranda "Anasayfa" görünür (simülasyonda doğrulandı). EN/DE'de yalnızca eşleme
 *  tablosu boşaltılır; translate="no" ve dropThemeCanvas() aynen kalır. TR'de tampon yok → değişmez.
 *  Alternatif (kalıcı): 5026 kodunda lockTrLabels() başına
 *  if (!/^tr/i.test(document.documentElement.lang || "")) return;  → bu Elementor Custom Code yazımıdır. */
function ldj_hf_unlock_labels($html) {
    $a = strpos($html, '<script id="ledajans-header-identity-js">');
    if ($a === false) { return $html; }
    $b = strpos($html, '</script>', $a);
    if ($b === false) { return $html; }
    $seg = substr($html, $a, $b - $a);
    $seg2 = preg_replace('#(function lockTrLabels\(\) \{\s*var map = )\{[^{}]*\}#u', '${1}{}', $seg, 1);
    return ($seg2 === null) ? $html : substr($html, 0, $a) . $seg2 . substr($html, $b);
}

/* --- HF-5: Tek yazı şablonu gva__template 186, gva-heading-block ec189c7 "Son Yazılar" (WP widget DEĞİL;
 *  Polylang widget dil ayarı / widget_title etkisiz). Polylang'da zaten kayıtlı ve çevrili "Son Yazılar"
 *  dizgesi (EN Recent Posts / DE Neueste Beiträge) pll__ ile kullanılır; yeni dizge gerekmez. */
function ldj_hf_recent_posts_title($html) {
    $a = strpos($html, 'elementor-element-ec189c7');
    if ($a === false) { return $html; }
    $b = strpos($html, '</h2>', $a);
    if ($b === false || $b - $a > 2000) { return $html; }
    $seg = substr($html, $a, $b - $a);
    $seg2 = str_replace('<span>Son Yazılar</span>', '<span>' . esc_html(ldj_hf_t('Son Yazılar')) . '</span>', $seg);
    return substr($html, 0, $a) . $seg2 . substr($html, $b);
}

/* --- HF-9 (SEO kararı 16:23, v3): EN/DE ana sayfa — Elementor HTML widget f1e2fc2 (sayfa 5227/5228 içeriği,
 *  "LEDAJANS Ana Sayfa JSON-LD Schema (@graph)") içindeki WebSite düğümü. Rank Math dışı; Rank Math şeması ana sayfada
 *  öncelik 99 filtresiyle boşaltıldığı için ana sayfadaki TEK WebSite düğümü budur. Düğüm Organization ve LocalBusiness
 *  ile aynı @graph/<script> içinde → blok veya düğüm kaldırmak güvenilir değil (Organization/LocalBusiness de gider ya da
 *  JSON yeniden kodlanır). Seçilen yol: blok kalır, YALNIZ WebSite düğümünün "url" değeri dil ana sayfası (sonda /).
 *  Organization/LocalBusiness url'leri, @id, name, inLanguage, publisher, potentialAction değişmez. --- */
function ldj_hf_home_jsonld($html, $lang) {
    $root = preg_quote(untrailingslashit(ldj_hf_home_raw()), '#');
    $r = preg_replace_callback('#<script type="application/ld\+json">(.*?)</script>#s', function ($m) use ($root, $lang) {
        if (strpos($m[1], '"@graph"') === false) { return $m[0]; }
        $x = preg_replace('#("@type":\s*"WebSite",\s*"@id":\s*"' . $root . '/\#website",(?:\s*"[a-zA-Z@]+":\s*"[^"]*",)*?\s*"url":\s*")' . $root . '/?(")#',
            '${1}' . ldj_hf_home($lang) . '${2}', $m[0], 1);
        return ($x === null) ? $m[0] : $x;
    }, $html);
    return ($r === null) ? $html : $r;
}

function ldj_hf_filter($html) {
    if (!is_string($html) || stripos($html, '<html') === false) { return $html; }
    $lang = ldj_hf_lang();
    if ($lang !== 'en' && $lang !== 'de') { return $html; } // çifte güvence
    $orig = $html;
    $html = ldj_hf_footer(ldj_hf_header($html, $lang), $lang);
    $html = ldj_hf_unlock_labels($html);
    $html = ldj_hf_recent_posts_title($html);
    $html = ldj_hf_breadcrumb($html, $lang);
    $html = ldj_hf_home_jsonld($html, $lang); // v3 HF-9
    $html = ldj_hf_home_blogcards($html, $lang); // v40 BLOG-1
    return (is_string($html) && strlen($html) > strlen($orig) * 0.9) ? $html : $orig;
}
add_action('template_redirect', function () {
    if (!function_exists('ldj_r2_is_front_html') || !ldj_r2_is_front_html()) { return; }
    $lang = ldj_hf_lang();
    if ($lang === 'en' || $lang === 'de') { ob_start('ldj_hf_filter'); } // TR: tampon AÇILMAZ
}, 9);

/* --- HF-3: dil değiştirici (header widget c7f7f5c içindeki sabit 3+3 dil linki) — her dilde --- */
/* TR'de yalnızca 6 href değeri değişir; hedef = pll_the_languages(raw) (Polylang çeviri bağı;    */
/* çevirisi olmayan sayfada Polylang'ın kendi davranışı: o dilin ana sayfası).                     */
add_action('template_redirect', function () {
    if (!function_exists('ldj_r2_is_front_html') || !ldj_r2_is_front_html()) { return; }
    $lang = ldj_hf_lang();
    if ($lang === 'tr' && !LDJ_HF_TR_SWITCHER) { return; }
    if ($lang === '') { return; }
    ob_start(function ($html) use ($lang) {
        if (!is_string($html) || stripos($html, '<html') === false) { return $html; }
        $a = strpos($html, 'elementor-element-c7f7f5c');
        $b = ($a === false) ? false : strpos($html, '<div id="page-content">', $a);
        if ($a === false || $b === false) { return $html; }
        $links = ldj_hf_switcher_links();
        if (!$links) { return $html; }
        $seg = substr($html, $a, $b - $a);
        foreach ($links as $l) {
            if ($lang === 'tr' && !empty($l['no_translation'])) { continue; } // v35: TR'de karşılık yoksa mevcut sabit href korunur
            $seg = preg_replace('#href="[^"]*"( hreflang="' . preg_quote($l['locale'], '#') . '" lang="' . preg_quote($l['locale'], '#') . '")#', 'href="' . esc_url($l['url']) . '"$1', $seg);
        }
        return substr($html, 0, $a) . $seg . substr($html, $b);
    });
}, 8); // 8 < 9: HF-1'in DIŞINDA → HF-1 (EN/DE) zaten doğru URL üretir, HF-3 aynı değeri yeniden yazar (idempotent)

/* --- HF-6: og:site_name (Rank Math titles.website_name = "LED Ekran - RGB Panel - LED Görüntü Sistemleri")
 *  v3 (SEO kararı): og:site_name, JSON-LD WebSite name ve Polylang knowledgegraph_name çevirisi ÜÇÜ DE AYNI metin.
 *  Ayıraç TR kaynağıyla aynı: boşluk + ASCII tire (U+002D) + boşluk.
 *  Polylang çevirileri dizeye göre tutulur ve "LED Ekran - RGB Panel - LED Görüntü Sistemleri" dizesi knowledgegraph_name
 *  satırında zaten kayıtlı (DE bugün "LED-Displays - RGB-Panels - …"). Ayrı dize kaydı çakışacağından v3'te
 *  pll_register_string kaldırıldı; filtreler bu sabit metni kullanır, P4'te Ceviri knowledgegraph_name'e aynı metni girer.
 *  TR'de filtre değeri AYNEN döndürür → TR og:site_name ve JSON-LD değişmez. */
function ldj_hf_site_name($name, $lang) {
    $approved = array(
        'en' => 'LED Screen - RGB Panel - LED Display Systems',
        'de' => 'LED-Display - RGB-Panel - LED-Anzeigesysteme',
    );
    return isset($approved[$lang]) ? $approved[$lang] : $name;
}
/* og:site_name kaynağı: Rank Math > Başlıklar & Meta > "Web Sitesi Adı" (titles.website_name). Rank Math free bu alanı
 * Polylang'a dizge olarak KAYDETMİYOR (yalnız knowledgegraph_name kayıtlı). Sosyal alanlara yazılmaz; çıktı filtresi: */
add_filter('rank_math/opengraph/facebook/og_site_name', function ($name) {
    $lang = ldj_hf_lang();
    if ($lang !== 'en' && $lang !== 'de') { return $name; } // TR: değer aynen
    return ldj_hf_site_name($name, $lang);
});


/* --- HF-7: Tema breadcrumb'ı (EN/DE) — "Home" bağlantısı
 *  Kaynak 1: parent tema modins/includes/functions.php modins_general_breadcrumbs() satır 60/69:
 *     esc_html__('Home','modins') + esc_url(home_url())  → Polylang home_url'i /en, /de (SONDA EĞİK ÇİZGİ YOK) yapıyor
 *     → /en ve /de 301 ile /en/, /de/'ye gidiyor (33 EN + 33 DE sayfa; widget b34583d, sayfa başlık alanı).
 *  Kaynak 2: modins-themer eklentisi el-widgets/dynamic-tags/post-breadcrumb.php breadcrumbs() satır 214/223 (widget
 *     gva_post_breadcrumb 1070ed4, tek yazı şablonu 186): aynı kod ama eklenti dosyası Polylang'ın home_url
 *     filtresinden geçmiyor → EN/DE yazılarda "Home" TR ana sayfaya (https://tester.ledajans.com) gidiyor (8 EN + 8 DE yazı).
 *  Çözüm: EN/DE'de yalnızca <ol class="breadcrumb"> içindeki İLK <li><a>'nın href'i dil ana sayfası (sonda /) ve
 *  metni pll__('Anasayfa') (EN Home / DE Startseite) olur. TR'de tampon açılmaz. Tema/eklenti dosyasına dokunulmaz. */
function ldj_hf_breadcrumb($html, $lang) {
    $home = esc_url(ldj_hf_home($lang));
    $label = esc_html(ldj_hf_t('Anasayfa'));
    $base = preg_quote(untrailingslashit(ldj_hf_home_raw()), '#');
    return preg_replace('#(<ol class="breadcrumb"><li><a href=")' . $base . '(?:/' . preg_quote($lang, '#') . ')?/?(">)[^<]*(</a>)#u',
        '${1}' . $home . '${2}' . $label . '${3}', $html);
}
function ldj_hf_home_raw() { // Polylang filtresiz kök adres (get_option('home'))
    return (string) get_option('home');
}

/* --- HF-8 (SEO kararı, v3'te AÇIK): Rank Math JSON-LD "WebSite" düğümü, yalnız EN/DE.
 *  url = dil ana sayfası sonda / ile (https://tester.ledajans.com/en/, …/de/; hreflang adresleriyle aynı),
 *  name = og:site_name ile aynı onaylı metin. @id, inLanguage, publisher ve diğer alanlar değişmez.
 *  Ana sayfada mevcut filtre (öncelik 99) Rank Math şemasını boşaltıyor; bu filtre 100'de, boş diziye dokunmaz.
 *  Rank Math ayarlarına/sosyal alanlarına yazılmaz. */
add_filter('rank_math/json_ld', function ($data, $jsonld) {
    $lang = ldj_hf_lang();
    if (($lang !== 'en' && $lang !== 'de') || !is_array($data) || !$data) { return $data; }
    foreach ($data as $k => $node) {
        if (is_array($node) && isset($node['@type']) && $node['@type'] === 'WebSite') {
            if (array_key_exists('url', $node)) { $data[$k]['url'] = ldj_hf_home($lang); }
            if (array_key_exists('name', $node)) { $data[$k]['name'] = ldj_hf_site_name($node['name'], $lang); }
        }
    }
    return $data;
}, 100, 2);

// HF-9 (v33): EN/DE'de gizli Elementor footer'ını (176/75) hiç basma.
// Tema: includes/hook.php modins_get_footer_layout (öncelik 10, 1 parametre);
// footer.php ve elementor/hooks.php 0 dönünce footer-main'i ve footer CSS'ini atlar. TR değişmez.
add_filter('modins_get_footer_layout', function ($footer_id) {
    $lang = ldj_hf_lang();
    return ($lang === 'en' || $lang === 'de') ? 0 : $footer_id;
}, 99);

// HF-10 (v34): EN/DE iç sayfalarda Rank Math #person düğümünün yalnız name değeri.
// @id/@type ve diğer düğümler (#organization, #localbusiness, WebSite...) değişmez; Rank Math ayarına yazılmaz.
add_filter('rank_math/json_ld', function ($data, $jsonld) {
    $lang = ldj_hf_lang();
    if (($lang !== 'en' && $lang !== 'de') || is_front_page() || !is_array($data) || !$data) { return $data; }
    foreach ($data as $k => $node) {
        if (is_array($node) && isset($node['@id']) && substr((string) $node['@id'], -8) === '/#person' && array_key_exists('name', $node)) {
            $data[$k]['name'] = ldj_hf_site_name($node['name'], $lang);
        }
    }
    return $data;
}, 100, 2);

// HF-11 (v36): TR WebSite JSON-LD url'si sonda eğik çizgiyle. Rank Math (snippets/class-website.php) url = get_home_url() → eğik çizgisiz.
// Yalnız TR, yalnız @type WebSite ve url = kök adres olan düğüm; @id, name ve diğer düğümler değişmez. Rank Math ayarına yazılmaz.
add_filter('rank_math/json_ld', function ($data, $jsonld) {
    if (ldj_hf_lang() !== 'tr' || !is_array($data) || !$data) { return $data; }
    $root = untrailingslashit(ldj_hf_home_raw());
    foreach ($data as $k => $node) {
        if (is_array($node) && isset($node['@type'], $node['url']) && $node['@type'] === 'WebSite' && is_string($node['url']) && $node['url'] === $root) {
            $data[$k]['url'] = $root . '/';
        }
    }
    return $data;
}, 100, 2);

// HF-12 (v36b): TR ana sayfada Rank Math şeması kapalı; WebSite düğümü Elementor HTML widget'ında (1248 / f1e2fc2) sabit.
// Yalnız TR + ön sayfa: HF-9 ile aynı yapısal değiştirme (ldj_hf_home_jsonld) → WebSite url'si 'https://…/' olur; başka bayt değişmez.
add_action('template_redirect', function () {
    if (!function_exists('ldj_r2_is_front_html') || !ldj_r2_is_front_html()) { return; }
    if (ldj_hf_lang() !== 'tr' || !is_front_page()) { return; }
    ob_start(function ($html) {
        return (is_string($html) && stripos($html, '<html') !== false) ? ldj_hf_home_jsonld($html, 'tr') : $html;
    });
}, 9);

// ===== LEDAJANS R5-v37 — portfolio CPT ve category_portfolio taksonomisi Rank Math sitemap'inden çıkarılır =====
// Yalnız filtre; Rank Math ayarlarına yazılmaz. İmza (Rank Math belgeleri): ($exclude, $type) / ($exclude, $taxonomy), 10, 2.
// Geri alma: bu bloğu silmek + Rank Math sitemap önbelleği / WPSC temizliği.
add_filter('rank_math/sitemap/exclude_post_type', function ($exclude, $type) {
    return ($type === 'portfolio') ? true : $exclude;
}, 10, 2);
add_filter('rank_math/sitemap/exclude_taxonomy', function ($exclude, $taxonomy) {
    return ($taxonomy === 'category_portfolio') ? true : $exclude;
}, 10, 2);

/* =============================================================================
 * v40 BLOG (TASLAK — UYGULANMADI): EN/DE ana sayfa (5227/5228) blog kaydırıcısı, Elementor HTML widget 585fac3.
 *  Sorun: (1) widget'taki sabit (yedek) 10 kartın href'i TR yazılar; (2) JS /wp-json/wp/v2/posts?lang=xx çağırıyor
 *  ama Polylang (ücretsiz) REST'te lang parametresini süzmüyor ve window.LEDajansBlogLang hiç tanımlanmıyor →
 *  JS de dil karışık 10 yazı basıyor (TR+EN+DE).
 *  Çözüm (yalnız çıktı/sorgu; veritabanına ve Elementor verisine yazılmaz):
 *   BLOG-1  EN/DE ön sayfa HTML'inde #ledajans-blog-track içindeki kart href'leri → pll_get_post ile dil eşi
 *           (yayında değilse href değişmez). Metinler zaten widget'ta çevrili.
 *   BLOG-2  EN/DE ön sayfada wp_head'e window.LEDajansBlogLang = 'en'|'de' (TR'de basılmaz → TR baytları aynı).
 *   BLOG-3  REST /wp/v2/posts: lang parametresi geçerli bir Polylang diliyse language taksonomisiyle süz.
 *           lang verilmeyen istekler değişmez; context=edit, is_admin() ve edit_posts yetkili oturum istekleri
 *           (düzenleyici) hiç süzülmez. Widget JS isteği credentials:'omit' → misafir → süzülür.
 *  TR: widget JS'te LEDajansBlogLang yalnız yorum satırında; TR'de lang gönderilmez, BLOG-2 TR'de basılmaz,
 *      BLOG-1 yalnız EN/DE tamponunda → TR çıktısı değişmez (TR karışık dil listesi bu pakette düzeltilmez).
 *  Geri alma: bu bloğu ve ldj_hf_filter içindeki BLOG-1 satırını silmek + WPSC temizliği.
 * ============================================================================= */
function ldj_hf_home_blogcards($html, $lang) {
    if (!is_front_page() || !function_exists('pll_get_post')) { return $html; }
    $a = strpos($html, 'id="ledajans-blog-track"');
    if ($a === false) { return $html; }
    $b = strpos($html, '<script', $a);         // kartlar: track açılışı ile widget <script>'i arası
    if ($b === false || $b - $a > 40000) { return $html; }
    $seg = substr($html, $a, $b - $a);
    // home_url() EN/DE'de Polylang filtresiyle /en, /de döner → TR kart href'leri eşleşmez (r3 01:41 testi). Filtresiz kök:
    $home = preg_quote(untrailingslashit(ldj_hf_home_raw()), '#');
    $seg2 = preg_replace_callback('#(<a href=")(' . $home . '/[^"]*)(" class="ledajans-blog-card-wrap")#', function ($m) use ($lang) {
        // url_to_postid() EN/DE isteğinde Polylang dil süzgecine takılıp TR yazı için 0 döndürür (v40 01:34 testi);
        // get_page_by_path doğrudan slug sorgusu yapar, dil süzgecinden etkilenmez.
        $path = trim((string) wp_parse_url($m[2], PHP_URL_PATH), '/');
        $po = ($path !== '' && strpos($path, '/') === false) ? get_page_by_path($path, OBJECT, 'post') : null;
        $id = $po ? (int) $po->ID : 0;
        if (!$id) { return $m[0]; }
        $t = (int) pll_get_post($id, $lang);
        if (!$t || $t === $id || get_post_status($t) !== 'publish') { return $m[0]; }
        return $m[1] . esc_url(get_permalink($t)) . $m[3];
    }, $seg);
    return ($seg2 === null) ? $html : substr($html, 0, $a) . $seg2 . substr($html, $b);
}
add_action('wp_head', function () {
    if (!is_front_page()) { return; }
    $l = ldj_hf_lang();
    if ($l !== 'en' && $l !== 'de') { return; }
    echo "<script>window.LEDajansBlogLang=" . wp_json_encode($l) . ";</script>\n";
}, 1);
add_filter('rest_post_query', function ($args, $request) {
    // Düzenleyici/yönetim istekleri hariç: context=edit, wp-admin, ya da düzenleme yetkili oturum (Gutenberg, Polylang/Rank Math panelleri).
    if ($request->get_param('context') === 'edit' || is_admin() || current_user_can('edit_posts')) { return $args; }
    $l = $request->get_param('lang');
    if (!is_string($l) || $l === '' || !function_exists('pll_languages_list')) { return $args; }
    if (!in_array($l, (array) pll_languages_list(array('fields' => 'slug')), true)) { return $args; }
    $args['tax_query'] = isset($args['tax_query']) && is_array($args['tax_query']) ? $args['tax_query'] : array();
    $args['tax_query'][] = array('taxonomy' => 'language', 'field' => 'slug', 'terms' => array($l));
    return $args;
}, 10, 2);

/* =============================================================================
 * JSON-LD-PUB (v42, Design / Control 05.10): Publisher, #localbusiness tekrarı, sayfa author düğümü.
 * Rank Math/Polylang ayarına yazılmaz; yalnız çıktı filtreleri. Geri alma: bu bloğu silmek.
 * ============================================================================= */
function ldj_jp_root() { return untrailingslashit(ldj_hf_home_raw()); }
function ldj_jp_org_ref() { return ['@id' => ldj_jp_root() . '/#organization']; }
// Dizideki tüm {"@id": $from} referanslarını {"@id": $to} yapar (iç içe).
function ldj_jp_reref($v, $from, $to) {
    if (!is_array($v)) { return $v; }
    if (count($v) === 1 && isset($v['@id']) && $v['@id'] === $from) { return ['@id' => $to]; }
    foreach ($v as $k => $x) { if (is_array($x)) { $v[$k] = ldj_jp_reref($x, $from, $to); } }
    return $v;
}
// JP-1 Publisher: iç sayfalarda Rank Math'in #person düğümü ([Person, Organization], ad = site başlığı) → #organization "LEDAJANS".
// Ana sayfa Rank Math şeması zaten boş (öncelik 99). HF-10 (öncelik 100) ad değişikliği bu düğümde artık etkisiz kalır.
add_filter('rank_math/json_ld', function ($data, $jsonld) {
    if (!is_array($data) || !$data || is_front_page() || is_home()) { return $data; }
    $root = ldj_jp_root(); $p = $root . '/#person'; $o = $root . '/#organization'; $hit = false;
    foreach ($data as $k => $node) {
        if (is_array($node) && isset($node['@id']) && $node['@id'] === $p) {
            $data[$k] = [
                '@type' => 'Organization', '@id' => $o, 'name' => 'LEDAJANS', 'url' => $root . '/',
                'logo' => ['@type' => 'ImageObject', 'url' => $root . '/wp-content/uploads/2026/09/ledajans-logo.webp', 'width' => 1024, 'height' => 218],
            ];
            $hit = true;
        }
    }
    return $hit ? ldj_jp_reref($data, $p, $o) : $data;
}, 110, 2);
// JP-3 Author: SAYFALARDA (is_page; yazılar değil) Article author'ı kullanıcı Person'ı yerine #organization.
// Başka yerde referansı kalmayan /author/ Person düğümü çıkarılır. (/de/kontakt/ "Mücahid Özer" /de/author/cursor/, diğerleri "admin")
add_filter('rank_math/json_ld', function ($data, $jsonld) {
    if (!is_array($data) || !$data || !is_page() || is_front_page()) { return $data; }
    $o = ldj_jp_root() . '/#organization'; $authors = [];
    foreach ($data as $k => $node) {
        if (is_array($node) && isset($node['author']['@id']) && strpos((string) $node['author']['@id'], '/author/') !== false) {
            $authors[$node['author']['@id']] = true; $data[$k]['author'] = ['@id' => $o];
        }
    }
    if (!$authors) { return $data; }
    foreach ($data as $k => $node) {
        if (!is_array($node) || !isset($node['@id'], $authors[$node['@id']])) { continue; }
        $ref = wp_json_encode(['@id' => $node['@id']]); $used = false; // başka bir düğümde {"@id": X} referansı var mı
        foreach ($data as $k2 => $n2) { if ($k2 !== $k && strpos(wp_json_encode($n2), substr($ref, 1, -1)) !== false) { $used = true; break; } }
        if (!$used) { unset($data[$k]); }
    }
    return $data;
}, 111, 2);
// JP-2 #localbusiness tekrarı: ön sayfada (TR/EN/DE) Elementor widget'ları (1248/5227/5228) iki LocalBusiness basıyor:
// tekil blok + @graph içindeki zengin düğüm (geo, openingHours, areaServed, hasOfferCatalog). Yalnız TEKİL blok kaldırılır;
// @graph'ta aynı @id yoksa dokunulmaz. Başka bayt değişmez (JP-4 hariç).
function ldj_jp_front_ld($html) {
    if (!is_string($html) || stripos($html, '<html') === false) { return $html; }
    $root = ldj_jp_root(); $lb = $root . '/#localbusiness';
    if (!preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $m, PREG_OFFSET_CAPTURE)) { return $html; }
    $inGraph = false; $single = [];
    foreach ($m[1] as $i => $c) {
        $j = json_decode($c[0], true); if (!is_array($j)) { continue; }
        if (isset($j['@graph']) && is_array($j['@graph'])) { foreach ($j['@graph'] as $n) { if (isset($n['@id']) && $n['@id'] === $lb) { $inGraph = true; } } }
        elseif (isset($j['@type'], $j['@id']) && $j['@type'] === 'LocalBusiness' && $j['@id'] === $lb) { $single[] = $m[0][$i]; }
    }
    if ($inGraph) { foreach (array_reverse($single) as $s) { $html = substr_replace($html, '', $s[1], strlen($s[0])); } }
    $q = preg_quote($root, '#');
    // JP-4 (SEO 05:30): kökle verilen url'ler eğik çizgili; publisher'a #organization @id'si. Kart diff'i (kart/) ile aynı JSON sonucu.
    // Yalnız ld+json <script> içinde; Organization/LocalBusiness düğümünde @id'den sonraki ilk "url"; logo/görsel/sameAs/target/CollectionPage.url dokunulmaz.
    $x = preg_replace_callback('#<script type="application/ld\+json">(.*?)</script>#s', function ($m) use ($root, $q) {
        $y = preg_replace('#"publisher":\s*\{\s*"@type":\s*"Organization",\s*"name":\s*"LEDAJANS",\s*"url":\s*"' . $q . '/?"\s*\}#',
            '"publisher": { "@type": "Organization", "@id": "' . $root . '/#organization", "name": "LEDAJANS", "url": "' . $root . '/" }', $m[0]);
        if ($y === null) { return $m[0]; }
        $z = preg_replace('#("@type":\s*"(?:Organization|LocalBusiness)",\s*"@id":\s*"' . $q . '/\#(?:organization|localbusiness)",[^{}]*?"url":\s*")' . $q . '(")#',
            '${1}' . $root . '/${2}', $y);
        if ($z === null) { return $y; }
        // v45 JP-5 (Control 07.10): ön sayfa widget JSON-LD'sinde Organization logo ImageObject'i iç sayfadaki değerlerle (1024×218).
        // Yalnız ld+json içinde, url ledajans-logo.webp olan ImageObject'in width/height'i; görünen logo/header'a dokunulmaz.
        $w = preg_replace('#("logo":\s*\{\s*"@type":\s*"ImageObject",\s*"url":\s*"' . $q . '/wp-content/uploads/2026/09/ledajans-logo\.webp",\s*"width":\s*)\d+(,\s*"height":\s*)\d+#',
            '${1}1024${2}218', $z);
        return ($w === null) ? $z : $w;
    }, $html);
    return ($x === null) ? $html : $x;
}
add_action('template_redirect', function () {
    if (!function_exists('ldj_r2_is_front_html') || !ldj_r2_is_front_html() || !is_front_page()) { return; }
    ob_start('ldj_jp_front_ld');
}, 8);

/* =============================================================================
 * BLOG-4 (v43 taslak, 07.10): blog listeleri sayfa dilinde. Elementor/Polylang/DB'ye yazılmaz; yalnız çıktı.
 *  Bulgu: ana sayfa slider JS'i REST'i window.LEDajansBlogLang varsa lang= ile çağırıyor; EN/DE'de BLOG-2 basıyor,
 *  TR'de basılmıyor → TR slider'a en yeni EN/DE yazıları geliyor. Blog sayfaları (27 / 5351 / 5352, aynı Polylang
 *  çeviri grubu) JS'i lang hiç göndermiyor ve süzgeç yok → üç dilde 143 yazının karışık listesi.
 *  BLOG-4a: TR ön sayfada wp_head'e window.LEDajansBlogLang="tr" (EN/DE BLOG-2 ile aynı biçim).
 *  BLOG-4b: blog sayfası çeviri grubundaki sayfada çıktıdaki REST adresine lang=<sayfa dili> eklenir (yalnız tam
 *           eşleşen tek literal; bulunmazsa/birden fazlaysa dokunulmaz). Süzme BLOG-3 (rest_post_query) ile yapılır.
 *  Kalıcı: dil sayfanın Polylang dilinden, sayfa kimliği çeviri grubundan; yeni yazı/yeni dil eşi eklenince elle iş yok.
 *  Geri alma: bu bloğu silmek + WPSC (6 sayfa) temizliği.
 * ============================================================================= */
add_action('wp_head', function () {
    if (!is_front_page() || ldj_hf_lang() !== 'tr') { return; }
    echo "<script>window.LEDajansBlogLang=\"tr\";</script>\n";
}, 1);
function ldj_blog4_lang() {
    if (!is_page() || !function_exists('pll_get_post_translations') || !function_exists('pll_get_post_language')) { return ''; }
    $id = (int) get_queried_object_id();
    $group = array_map('intval', (array) pll_get_post_translations(27)); // TR /blog/ = 27; EN 5351, DE 5352
    if (!$id || !in_array($id, $group, true)) { return ''; }
    $l = (string) pll_get_post_language($id, 'slug');
    return in_array($l, (array) pll_languages_list(array('fields' => 'slug')), true) ? $l : '';
}
function ldj_blog4_buf($html) {
    $l = ldj_blog4_lang();
    if ($l === '' || !is_string($html) || stripos($html, '<html') === false) { return $html; }
    $old = "/wp-json/wp/v2/posts?status=publish&orderby=date&order=desc&_embed=1&per_page=";
    if (substr_count($html, $old) !== 1) { return $html; }
    return str_replace($old, "/wp-json/wp/v2/posts?lang=" . $l . "&status=publish&orderby=date&order=desc&_embed=1&per_page=", $html);
}
add_action('template_redirect', function () {
    if (ldj_blog4_lang() === '') { return; }
    ob_start('ldj_blog4_buf');
}, 8);

// v46 2026-10-07 (Design / Control): YALNIZ TESTER İÇİN. Rank Math site haritası dosya önbelleği (uploads/rank-math/) kapalı;
// harita her istekte canlı üretilir (eski /en/blog-2/, /de/blog-3/ kayıtları önbellekte kalıyordu). Google'a açılırken bu satır KALDIRILACAK.
add_filter('rank_math/sitemap/enable_caching', '__return_false');
