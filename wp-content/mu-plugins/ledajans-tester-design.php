<?php
/**
 * Plugin Name: LEDAJANS Tester Design
 * Description: Unifies the public tester site's references, engineering tools and cards.
 */

defined('ABSPATH') || exit;

add_action('wp_enqueue_scripts', static function () {
    // The production site may share the same WordPress code.
    if (wp_parse_url(home_url('/'), PHP_URL_HOST) !== 'tester.ledajans.com') {
        return;
    }

    wp_register_style('ledajans-tester-design', false, array(), '1.0.0');
    wp_enqueue_style('ledajans-tester-design');
    wp_add_inline_style('ledajans-tester-design', <<<'CSS'
.ldj-eng {
    --tester-ink: #17191f;
    --tester-muted: #4b5563;
    --tester-paper: #f5f6f8;
    --tester-line: #dce1e8;
    --tester-orange: #f46f2c;
    --tester-action: #ac421a;
    --tester-radius: 18px;
    --tester-width: 74rem;
    --tester-gutter: clamp(1rem, 4vw, 3rem);
    --tester-section: clamp(2.75rem, 6vw, 5.5rem);
    --tester-shadow: 0 12px 34px -24px rgba(19, 25, 38, .45);
}

body .ldj-eng {
    font-family: inherit;
}

.ldj-eng :is(.ldj-class-inner, .ldj-export-inner, .ldj-met-inner, .ldj-tool-inner, .ldj-page) {
    max-width: var(--tester-width);
    margin-inline: auto;
    padding-inline: var(--tester-gutter);
}

.ldj-eng:is(.ldj-export, .ldj-met, .ldj-tool) {
    padding: var(--tester-section) 0;
}

.ldj-eng .ldj-page {
    padding-block: var(--tester-section);
}

.ldj-eng :is(h1, h2, h3) {
    color: var(--tester-ink);
    line-height: 1.13;
    text-wrap: balance;
}

.ldj-eng :is(.ldj-lead, .ldj-copy, .ldj-note, .ldj-fine) {
    color: var(--tester-muted);
}

.ldj-eng :is(p.ldj-kicker, .ldj-card em, .ldj-node > span) {
    color: var(--tester-action);
    letter-spacing: .095em;
}

/* Both references stay visible and their existing links jump to the cards. */
.ldj-eng.ldj-imza {
    padding-top: var(--tester-section);
    background: radial-gradient(ellipse at 85% 0, #32231f, transparent 50%), #14171e;
    color: #f8fafc;
}

.ldj-eng .ldj-imza-nav,
.ldj-eng .ldj-imza-rail {
    max-width: var(--tester-width);
    margin-inline: auto;
    padding-inline: var(--tester-gutter);
}

.ldj-eng .ldj-imza-nav {
    justify-content: flex-start;
    padding-top: 0;
    margin-bottom: 1rem;
}

.ldj-eng .ldj-imza-nav a {
    display: inline-flex;
    align-items: center;
    min-height: 44px;
    padding-inline: .95rem;
    color: #f8fafc;
    background: rgba(255, 255, 255, .065);
    border-color: #55515a;
    font-weight: 700;
}

.ldj-eng .ldj-imza-nav a:hover {
    border-color: var(--tester-orange);
    color: #ffc7a7;
}

.ldj-eng .ldj-imza-rail {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: clamp(.9rem, 2vw, 1.5rem);
    overflow: visible;
    padding-bottom: var(--tester-section);
    scroll-snap-type: none;
}

.ldj-eng .ldj-imza-slide {
    display: flex;
    flex-direction: column;
    min-width: 0;
    min-height: 0;
    overflow: hidden;
    border: 1px solid #42424b;
    border-radius: var(--tester-radius);
    background: #1d2029;
    scroll-margin-top: 7rem;
}

.ldj-eng .ldj-imza-slide:target {
    border-color: var(--tester-orange);
}

.ldj-eng .ldj-imza-copy {
    order: 1;
    padding: clamp(1.2rem, 2.6vw, 2rem);
}

.ldj-eng .ldj-imza-copy h2 {
    max-width: none;
    color: #f8fafc;
}

/* R1 sets page-wide heading dimensions with !important. */
body.ldj-r1 #wp-main-content .ldj-eng .ldj-imza-copy h2 {
    font-size: clamp(1.65rem, 1.05rem + 1.6vw, 2.6rem) !important;
    line-height: 1.1 !important;
}

.ldj-eng .ldj-imza-copy p {
    margin-top: .7rem;
    color: #d0d5df;
    font-size: .98rem;
    line-height: 1.65;
}

.ldj-eng .ldj-imza-copy p.ldj-kicker {
    margin: 0 0 .5rem;
    color: #ffc7a7;
    font-size: .72rem;
}

.ldj-eng .ldj-imza-shot {
    display: block;
    padding: 0;
    background: #0e1015;
}

.ldj-eng .ldj-imza-shot img {
    display: block;
    width: 100%;
    height: auto;
    max-height: 22rem;
    aspect-ratio: 16 / 9;
    border-radius: 0;
    object-fit: contain;
}

.ldj-eng .ldj-imza-shot figcaption {
    margin: 0;
    padding: .55rem clamp(1.2rem, 2.6vw, 2rem);
    color: #c1c8d4;
    border-top: 1px solid #353944;
}

.ldj-eng .ldj-class {
    padding: var(--tester-section) 0;
    background: var(--tester-paper);
}

.ldj-eng .ldj-case {
    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
    gap: 0;
    align-items: center;
}

.ldj-eng .ldj-case img {
    min-height: 0;
    height: auto;
    aspect-ratio: 4 / 3;
    object-fit: contain;
    background: #14171e;
}

.ldj-eng .ldj-case > div {
    padding: clamp(1.25rem, 3vw, 2.25rem);
}

.ldj-eng .ldj-case .ldj-meta {
    padding: 0;
}

/* Shared surfaces for the homepage modules and full engineering pages. */
.ldj-eng :is(.ldj-case, .ldj-card, .ldj-node, .ldj-gauge, .ldj-panel, .ldj-row),
.ldj-eng.ldj-tool .ldj-tool-inner > div {
    min-width: 0;
    border: 1px solid var(--tester-line);
    border-radius: var(--tester-radius);
    background: #fff;
    box-shadow: var(--tester-shadow);
}

.ldj-eng :is(.ldj-card, .ldj-node, .ldj-gauge, .ldj-panel),
.ldj-eng.ldj-tool .ldj-tool-inner > div {
    padding: clamp(1rem, 2.4vw, 1.65rem);
}

.ldj-eng :is(.ldj-cards, .ldj-nodes, .ldj-gauges, .ldj-layout, .ldj-tool-inner, .ldj-split) {
    gap: clamp(.85rem, 2vw, 1.4rem);
}

.ldj-eng :is(.ldj-met-inner, .ldj-export-inner) > p:not(.ldj-kicker) {
    margin-top: .65rem;
}

.ldj-eng :is(.ldj-card strong, .ldj-gauge strong, .ldj-stat strong) {
    color: var(--tester-ink);
    overflow-wrap: anywhere;
}

.ldj-eng :is(.ldj-tabs, .ldj-cals) button {
    min-height: 44px;
    padding-inline: 1rem;
    color: var(--tester-ink);
    border-color: #cdd4df;
    font-weight: 700;
}

.ldj-eng .ldj-tabs button[aria-selected="true"],
.ldj-eng .ldj-cals button[aria-pressed="true"] {
    color: #fff;
    background: var(--tester-ink);
    border-color: var(--tester-ink);
    box-shadow: inset 0 -3px var(--tester-orange);
}

.ldj-eng :is(.ldj-tabs, .ldj-cals) button:hover,
.ldj-eng .ldj-card:hover {
    border-color: var(--tester-action);
}

.ldj-eng.ldj-tool {
    background: var(--tester-paper);
}

.ldj-eng :is(.ldj-fields input, .ldj-fields select) {
    width: 100%;
    min-width: 0;
    min-height: 46px;
    font-size: 1rem;
    border-color: #cbd3df;
    background-color: #fff;
}

.ldj-eng .ldj-layout > [aria-live],
.ldj-eng.ldj-tool .ldj-tool-inner > [aria-live] {
    color: #f8fafc;
    background: #1b1e26;
    border-color: #2f3542;
}

.ldj-eng [aria-live] :is(.ldj-copy, .ldj-fine, .ldj-stat span) {
    color: #c1c9d6;
}

.ldj-eng [aria-live] .ldj-pitch {
    color: #ffc7a7;
    line-height: 1.1;
}

.ldj-eng [aria-live] .ldj-stat {
    min-width: 0;
    background: #252a35;
    border: 1px solid #434958;
}

.ldj-eng [aria-live] .ldj-stat strong,
.ldj-eng .ldj-iso h2 {
    color: #f8fafc;
}

.ldj-eng [aria-live] .ldj-grid {
    min-width: 0;
    margin: 0;
    border: 1px solid #434958;
}

.ldj-eng [aria-live] .ldj-grid canvas {
    height: clamp(6.5rem, 12vw, 10rem);
}

.ldj-eng.ldj-met a.ldj-go,
.ldj-eng .ldj-page a.ldj-go,
.ldj-eng .ldj-actions a,
.ldj-eng .ldj-actions a.pri,
.ldj-eng .ldj-export-links a {
    display: inline-flex;
    align-items: center;
    min-height: 44px;
    background: var(--tester-action);
    color: #fff;
}

.ldj-eng :is(.ldj-actions, .ldj-export-links) a.alt {
    color: var(--tester-ink);
    border-color: #cdd4df;
    background: #fff;
}

.ldj-eng [aria-live] .ldj-actions a.alt {
    background: transparent;
    border-color: #687282;
    color: #f8fafc;
}

.ldj-eng :is(a, button, input, select):focus-visible {
    outline: 2px solid var(--tester-orange);
    outline-offset: 3px;
}

/* Align public catalog, support and contact cards with the same visual rhythm. */
body :is(.ledajans-cert-page, .ledajans-ofisler-section, .la-wrapper)
    :is(.ledajans-cert-cta, .ledajans-ofis-kart, .la-card, .la-cta-box),
body :is(.ledajans-iletisim-grid, .ledajans-projects, .ledajans-home-certs, .ledajans-brands)
    :is(.ledajans-iletisim-sol, .ledajans-iletisim-sag, .ledajans-project-card, .ledajans-home-cert-card, .ledajans-brand-card) {
    border-radius: 18px;
}

body :is(.ledajans-ofisler-section, .la-wrapper) :is(.ledajans-ofis-kart, .la-card) {
    min-width: 0;
    box-shadow: 0 12px 34px -24px rgba(19, 25, 38, .45);
}

body .ledajans-ofis-kart :is(.ledajans-ofis-adres, .ledajans-ofis-iletisim) {
    overflow-wrap: anywhere;
}

@media (max-width: 860px) {
    .ldj-eng :is(.ldj-layout, .ldj-tool-inner, .ldj-split) {
        grid-template-columns: minmax(0, 1fr);
    }
}

@media (max-width: 800px) {
    .ldj-eng :is(.ldj-imza-rail, .ldj-case) {
        grid-template-columns: minmax(0, 1fr);
    }

    .ldj-eng .ldj-gauges {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 420px) {
    .ldj-eng .ldj-gauge strong {
        font-size: 1rem;
    }
}

@media (prefers-reduced-motion: reduce) {
    .ldj-eng .ldj-card,
    .ldj-eng .ldj-track i {
        transition: none;
    }

    .ldj-eng .ldj-card:hover {
        transform: none;
    }
}
CSS
    );
}, 10000);
