<?php
/**
 * Plugin Name: LEDAJANS Tester UX
 * Description: Project posters, homepage navigation, product CTAs and quote discovery on tester.
 */

defined('ABSPATH') || exit;

function ldj_tester_ux_page() {
    return wp_parse_url(home_url('/'), PHP_URL_HOST) === 'tester.ledajans.com'
        ? (int) get_queried_object_id() : 0;
}

add_filter('elementor/frontend/the_content', static function ($content) {
    $page = ldj_tester_ux_page();
    if ($page === 7171 && strpos($content, 'data-ldj-mimari="full"') !== false) {
        return str_replace('<h1>Ölçüyü pitch’e çevir</h1>', '<h2>Ölçüyü pitch’e çevir</h2>', $content);
    }
    if (!in_array($page, array(1248, 4956), true) || strpos($content, 'ledajans-project-card') === false) {
        return $content;
    }

    // Keep the source available to the existing details dialog without loading it on the card.
    return preg_replace_callback('~<video\b[^>]*>.*?</video>~is', static function ($match) {
        $tags = new WP_HTML_Tag_Processor($match[0]);
        if (!$tags->next_tag(array('tag_name' => 'video', 'class_name' => 'ledajans-project-video'))) {
            return $match[0];
        }
        $poster = $tags->get_attribute('poster');
        $src = $tags->get_attribute('src') ?: $tags->get_attribute('data-ldj-src');
        $tags->remove_class('ledajans-project-video');
        foreach (array('src', 'autoplay', 'loop', 'data-ldj-src', 'data-ldj-lazy') as $attr) {
            $tags->remove_attribute($attr);
        }
        $tags->set_attribute('hidden', true);
        $tags->set_attribute('preload', 'none');
        $has_source = $tags->next_tag('SOURCE');
        if ($has_source) {
            $src = $tags->get_attribute('data-src') ?: $tags->get_attribute('src') ?: $tags->get_attribute('data-ldj-src') ?: $src;
            $tags->remove_attribute('src');
            $tags->remove_attribute('data-ldj-src');
            if ($src) {
                $tags->set_attribute('data-src', $src);
            }
        }
        $verified = array(
            'outdoor-kure-led-ekran', 'WhatsApp-Video-2026-03-12-at-11.51.08-AM-1',
            'WhatsApp-Video-2026-03-12-at-11.50.57-AM', 'WhatsApp-Video-2026-03-12-at-11.51.07-AM',
            'WhatsApp-Video-2026-03-12-at-11.51.07-AM-2', 'WhatsApp-Video-2026-03-12-at-11.51.07-AM-1',
            'v1', 'v2-Trim',
        );
        $base = basename((string) wp_parse_url((string) $src, PHP_URL_PATH), '.mp4');
        if (!$poster && in_array($base, $verified, true)) {
            $poster = content_url('/uploads/2026/09/ldj-poster-' . $base . '.webp');
        }
        $fallback = '<span class="ldj-ux-poster-label">Proje detaylarını inceleyin</span>';
        $image = $poster ? '<img class="ledajans-project-image ldj-ux-poster" alt="" loading="lazy" decoding="async" src="' . esc_url($poster) . '">' : '';
        $video = $tags->get_updated_html();
        if (!$has_source && $src) {
            $video = substr($video, 0, -8) . '<source data-src="' . esc_url($src) . '"></video>';
        }
        return $video . $fallback . $image;
    }, $content) ?? $content;
});

add_action('wp_head', static function () {
    if (!in_array(ldj_tester_ux_page(), array(1248, 4956, 5001, 7171, 27, 1262), true)) {
        return;
    }
    ?>
<style id="ledajans-tester-ux-css">
.ldj-ux-solutions { width: min(74rem, 100%); margin: 0 auto; padding: clamp(2.5rem,5vw,4.75rem) clamp(1rem,4vw,3rem); }
.ldj-ux-kicker { color: #a94118; font-size: .8rem; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
.ldj-ux-solutions > p { max-width: 60ch; color: #4b5563; }
.ldj-ux-solution-grid { display: grid; grid-template-columns: repeat(3,minmax(0,1fr)); gap: 1rem; }
.ldj-ux-solution-grid a { display: grid; gap: .35rem; padding: 1.2rem; border-radius: 16px; border: 1px solid #dce1e8; color: #1b2232; background: #f7f8fa; text-decoration: none; }
.ldj-ux-solution-grid a:hover { border-color: #ac421a; }
.ldj-ux-solution-grid a span { color: #4b5563; }
.ldj-ux-solution-grid a b { color: #a94118; font-size: .9rem; }
.ledajans-project-image-wrap > video[hidden] { display: none !important; }
.ldj-ux-poster-label { display: grid; place-items: center; min-height: 8rem; padding: 1rem; color: #f8fafc; background: linear-gradient(145deg,#242f44,#161b28); text-align: center; }
.ledajans-project-image-wrap .ldj-ux-poster-label { position: absolute; inset: 0; }
.ledajans-project-image-wrap > .ldj-ux-poster { position: absolute; inset: 0; object-fit: cover; }
.ledajans-modal__media-wrap > .ldj-ux-poster-label { width: 100%; }
.ledajans-modal__media-wrap > img.ldj-ux-poster { object-fit: contain; }
body .ledajans-project-card .ledajans-project-link { color: #a94118; font-weight: 700; }
.page-id-1248 .ledajans-projects-seo-block { display: none; }
.page-id-5001 .la-hero-actions > a.la-btn-outline { border-color: transparent; background: transparent; padding-inline: .7rem; min-height: 44px; text-decoration: underline; text-underline-offset: .23em; }
.page-id-5001 .la-hero-actions > a.la-btn-outline:hover { color: #ffc6a4; }
.page-id-5001 .la-card { min-width: 0; border-radius: 18px; }
.page-id-5001 .la-card-title { min-block-size: 2.6em; }
.page-id-7171 .custom-breadcrumb { background-image: none !important; background-color: #171d2a; }
.page-id-7171 .custom-breadcrumb .breadcrumb-main { padding-block: 1rem !important; }
.page-id-7171 .custom-breadcrumb .heading-title { display: none; }
.page-id-7171 .single-content-inner { padding-top: 1rem; }
.page-id-7171 .ldj-eng .ldj-page { padding-block-start: clamp(1rem,3vw,2.25rem); }
.page-id-7171 .ldj-eng :is(.ldj-panel,.ldj-fields label,.ldj-stat) { min-width: 0; }
.ldj-ux-featured { margin: 0 0 1.5rem; padding: 1rem 1.25rem; background: #171d2a; color: #fff; border-radius: 16px; }
.ldj-ux-featured strong { display: block; margin-bottom: .35rem; }
.ldj-ux-featured a { display: inline-flex; align-items: center; min-height: 44px; margin-inline-end: 1.25rem; color: #ffcfb5; text-decoration: underline; text-underline-offset: .18em; }
.ldj-ux-topics { display: flex; flex-wrap: wrap; justify-content: center; gap: .45rem; margin-bottom: 1.2rem; }
.ldj-ux-topics button { min-height: 44px; padding: .45rem .85rem; border: 1px solid #cbd2de; border-radius: 999px; background: white; color: #1b2232; font-size: .9rem; font-weight: 700; }
.ldj-ux-topics button[aria-pressed="true"] { background: #171d2a; color: #fff; border-color: #171d2a; }
.ldj-ux-topic-active :is(.ledajans-blog-pages,.ledajans-blog-pagination,.ledajans-blog-search-results) { display: none !important; }
.ldj-ux-topic-results[hidden], .ldj-ux-topic-count[hidden] { display: none !important; }
.ldj-ux-topic-count { margin-block: .5rem 1rem; color: #4b5563; text-align: center; }
.ldj-ux-topic-label { color: #a94118; font-weight: 700; }
.page-id-27 .ledajans-blog-category[hidden] { display: none !important; }
.page-id-27 .ledajans-blog-card { display: flex; flex-direction: column; min-width: 0; }
.page-id-27 .ledajans-blog-card-body { display: flex; flex-direction: column; flex: 1; }
.page-id-27 .ledajans-blog-card-body .ledajans-blog-link { margin-top: auto; align-self: flex-start; }
.ldj-ux-quote-note { margin-block: 0 1rem; padding: .85rem 1rem; border-inline-start: 4px solid #ac421a; border-radius: 10px; background: #fff2e9; color: #632c15; font-weight: 700; }
.ldj-ux-mail-note { display: block; margin-block: .55rem 0; color: #4b5563; }
.page-id-1262 :is(.ledajans-iletisim-sol,.ledajans-iletisim-sag,.ledajans-ofis-kart) { min-width: 0; }
.page-id-1262 :is(.ledajans-ofis-adres,.ledajans-ofis-tel), .ledajans-footer-contact-text { overflow-wrap: anywhere; }
:is(.ldj-ux-solutions,.ldj-ux-topics,.ldj-ux-featured) :is(a,button):focus-visible { outline: 2px solid #ac421a; outline-offset: 3px; }
@media (max-width: 767px) {
    .ldj-ux-solution-grid { grid-template-columns: minmax(0,1fr); gap: .65rem; }
    .page-id-5001 .la-card-title { min-block-size: 0; }
    .page-id-5001 .la-hero-actions { gap: .35rem .65rem; }
    .page-id-5001 .la-hero-actions > a.la-btn-primary { width: 100%; justify-content: center; }
    .ledajans-projects-filters > button, .ledajans-project-card .ledajans-project-link,
    #ledajans-iletisim-form button { min-height: 44px; }
    .ledajans-footer-socials { flex-wrap: wrap; }
    .ledajans-footer-socials a { min-width: 44px; min-height: 44px; }
    .page-id-1262 #ledajans-iletisim-form :is(input,select,textarea) { max-width: 100%; font-size: 16px; }
    .page-id-7171 .ldj-eng .ldj-fields :is(input,select) { max-width: 100%; font-size: 16px; }
    .ldj-ux-featured { padding-inline: 1rem; }
}
</style>
    <?php
}, 100);

add_action('wp_footer', static function () {
    if (!in_array(ldj_tester_ux_page(), array(1248, 4956, 27, 1262), true)) {
        return;
    }
    ?>
<script id="ledajans-tester-ux-js">
(() => {
    const find = (selector, root = document) => root.querySelector(selector);
    const make = (tag, className, text) => {
        const element = document.createElement(tag);
        if (className) element.className = className;
        if (text) element.textContent = text;
        return element;
    };

    function arrangeHome() {
        const page = find('.elementor-1248[data-elementor-type="wp-page"]');
        if (!page) return;
        const ids = ['e4b8d5d', 'eb848b5', 'bad766e', 'b29e528', '70aa15e', 'd7bbef6',
            'b1899f7', '4bbe4a5', '7ac271a', 'ad77344', 'd9d8c15', '4d4078c', 'f3b96b1'];
        const sections = ids.map(id => find(':scope > [data-id="' + id + '"]', page));
        if (sections.some(section => !section)) return;
        const choices = make('section', 'ldj-ux-solutions');
        choices.id = 'ldj-ux-solutions';
        choices.setAttribute('aria-labelledby', 'ldj-ux-solutions-title');
        const heading = make('h2', '', 'Kullanım alanınıza uygun LED ekranı bulun');
        heading.id = 'ldj-ux-solutions-title';
        choices.append(make('span', 'ldj-ux-kicker', 'LED ekran çözümleri'), heading,
            make('p', '', 'Ekranı nerede kullanacağınızı seçin. Uygun ürünleri ve uygulama örneklerini inceleyin.'));
        const grid = make('div', 'ldj-ux-solution-grid');
        [
            ['/ic-mekan-led-ekran/', 'İç mekân', 'Mağaza, showroom ve toplantı alanları'],
            ['/dis-mekan-led-ekran/', 'Dış mekân', 'Bina cephesi, açık alan ve reklam panoları'],
            ['/rental-ekran/', 'Kiralama', 'Fuar, sahne ve geçici etkinlikler'],
        ].forEach(([url, name, description]) => {
            const link = make('a');
            link.href = url;
            link.append(make('strong', '', name), make('span', '', description), make('b', '', 'Çözümü incele →'));
            grid.append(link);
        });
        choices.append(grid);
        const boundary = document.createComment('LEDAJANS homepage sections');
        sections.at(-1).after(boundary);
        sections.slice(1).forEach(section => boundary.before(section));
        sections[0].after(choices);
        boundary.remove();
    }

    function projectMedia() {
        const overlays = document.querySelectorAll('#ledajansProjectModal');
        if (!overlays.length) return;
        let opener;
        let poster;
        document.addEventListener('click', event => {
            const button = event.target.closest('.ledajans-project-card .ledajans-project-link');
            if (!button) return;
            opener = button;
            poster = find('.ldj-ux-poster', button.closest('.ledajans-project-card'));
        }, true);
        const watchPoster = image => image.addEventListener('error', () => image.remove(), {once: true});
        document.querySelectorAll('img.ldj-ux-poster').forEach(image => {
            if (image.complete && !image.naturalWidth) image.remove();
            else watchPoster(image);
        });
        overlays.forEach(overlay => {
            const media = find('#ledajansModalMedia', overlay);
            const close = find('#ledajansModalClose', overlay);
            if (!media || !close) return;
            new MutationObserver(() => {
                if (overlay.getAttribute('aria-hidden') === 'true') {
                    if (overlay.contains(document.activeElement)) opener?.focus();
                    return;
                }
                const video = find('video', media);
                if (video && poster) video.poster = poster.src;
                close.focus({preventScroll: true});
            }).observe(overlay, {attributes: true, attributeFilter: ['aria-hidden']});
            media.addEventListener('error', event => {
                if (event.target.tagName !== 'VIDEO') return;
                const fallback = make('div', 'ldj-ux-poster-label', 'Video şu anda oynatılamıyor.');
                if (poster) {
                    const image = poster.cloneNode();
                    image.removeAttribute('loading');
                    watchPoster(image);
                    media.replaceChildren(fallback, image);
                } else {
                    media.replaceChildren(fallback);
                }
            }, true);
        });
    }

    function blogTopics() {
        const section = find('.ledajans-blog-section');
        const pages = section && find('.ledajans-blog-pages', section);
        const search = section && find('.ledajans-blog-search', section);
        if (!pages || !search) return;
        const normalize = text => text.toLocaleLowerCase('tr-TR');
        const topics = [
            ['Tümü', null], ['Fiyat Rehberleri', /fiyat|bütçe/], ['Ürün Teknolojileri', /cob|gob|smd|piksel|panel|teknoloji/],
            ['Teknik Destek', /huidu|colorlight|kontrol kart|yazılım|kurulum|bağlantı|bakım/],
            ['Projeler ve Uygulamalar', /proje|vitrin|cephe|stadyum|uygulama/],
            ['Rental ve Etkinlik', /rental|kirala|fuar|etkinlik|sahne/],
        ];
        const featured = make('div', 'ldj-ux-featured');
        featured.append(make('strong', '', 'Öne çıkan rehberler'));
        [
            ['/led-ekran-nasil-secilir-rehber/', 'LED ekran nasıl seçilir?'],
            ['/led-ekran-fiyatlari-2026/', '2026 LED ekran fiyatları'],
            ['/rental-led-ekran-kiralama-fiyatlari-2026/', 'Kiralama fiyatları'],
        ].forEach(([url, title]) => {
            const link = make('a', '', title);
            link.href = url;
            featured.append(link);
        });
        const controls = make('div', 'ldj-ux-topics');
        controls.setAttribute('role', 'group');
        controls.setAttribute('aria-label', 'Blog konuları');
        const count = make('p', 'ldj-ux-topic-count');
        count.setAttribute('role', 'status');
        const results = make('div', 'ledajans-blog-grid ldj-ux-topic-results');
        count.hidden = results.hidden = true;
        let active = 0;
        const titleOf = card => normalize(find('h3', card)?.textContent || '');
        function labelCard(card) {
            const category = find('.ledajans-blog-category', card);
            if (!category || category.hidden || normalize(category.textContent.trim()) !== 'genel') return;
            const topic = [1, 3, 5, 2, 4].map(index => topics[index]).find(([, rule]) => rule.test(titleOf(card)));
            if (!topic) return;
            category.after(make('span', 'ldj-ux-topic-label', topic[0]));
            category.hidden = true;
        }
        function render() {
            const allCards = Array.from(pages.querySelectorAll('.ledajans-blog-card'));
            allCards.forEach(labelCard);
            section.classList.toggle('ldj-ux-topic-active', Boolean(active));
            count.hidden = results.hidden = !active;
            if (!active) {
                results.replaceChildren();
                return;
            }
            const term = normalize(search.value.trim());
            const cards = allCards.filter(card => topics[active][1].test(titleOf(card)) && normalize(card.textContent).includes(term));
            count.textContent = cards.length ? cards.length + ' yazı bulundu.' : 'Bu konu ve aramaya uygun yazı bulunamadı.';
            results.replaceChildren(...cards.map(card => card.cloneNode(true)));
        }
        topics.forEach(([name], index) => {
            const button = make('button', '', name);
            button.type = 'button';
            button.setAttribute('aria-pressed', String(index === 0));
            button.addEventListener('click', () => {
                active = index;
                Array.from(controls.children).forEach((item, i) => item.setAttribute('aria-pressed', String(i === index)));
                render();
            });
            controls.append(button);
        });
        pages.before(featured, controls, count, results);
        search.setAttribute('aria-label', 'Blog yazılarında ara');
        search.addEventListener('input', render);
        render();
        new MutationObserver(render).observe(pages, {childList: true, subtree: true});
    }

    function quoteNotice() {
        const form = find('#ledajans-iletisim-form');
        if (!form) return;
        const message = find('#ledajans-message', form);
        const submit = find('button[type="submit"]', form);
        if (message?.value.startsWith('Proje mimarından gelen ön seçim:')) {
            form.before(make('p', 'ldj-ux-quote-note', 'Proje özeti formunuza eklendi.'));
        }
        if (submit) {
            submit.textContent = 'E-posta taslağını aç';
            const note = make('small', 'ldj-ux-mail-note', 'Mesajınızı göndermek için açılan e-posta uygulamasında Gönder’e dokunun.');
            note.id = 'ldj-ux-mail-note';
            submit.setAttribute('aria-describedby', note.id);
            submit.after(note);
        }
    }

    arrangeHome();
    projectMedia();
    blogTopics();
    quoteNotice();
})();
</script>
    <?php
}, 100);
