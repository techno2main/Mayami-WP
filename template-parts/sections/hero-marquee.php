<?php
/**
 * Template part - Hero Marquee
 * 
 * @package Mayami
 */

$marquee_items = cmb2_get_option('mayami_landing_options', 'marquee_items');
$link_spotify = cmb2_get_option('mayami_landing_options', 'link_spotify') ?: 'https://open.spotify.com/intl-fr/track/3rzrziofCOwRrI1r99IUbQ?si=a2cd3f4cbe364a94';
$marquee_play_link = cmb2_get_option('mayami_landing_options', 'marquee_play_link');
$marquee_show_music_icon = cmb2_get_option('mayami_landing_options', 'marquee_show_music_icon');
$music_link = !empty($marquee_play_link) ? $marquee_play_link : $link_spotify;
$show_music_icon = $marquee_show_music_icon !== 'off' && $marquee_show_music_icon !== '0' && $marquee_show_music_icon !== 0;

if (is_array($marquee_items)) {
    $marquee_items = array_values(array_filter($marquee_items, static function ($item) {
        if (!is_array($item)) {
            return false;
        }

        if (!empty($item['is_hidden'])) {
            return false;
        }

        return !empty($item['label']) && !empty($item['href']);
    }));
}

if (empty($marquee_items)) {
    $marquee_items = [
        ['label' => 'Mayami, My Miami', 'href' => '#hero', 'external' => false, 'is_hidden' => ''],
        ['label' => 'Out Now!', 'href' => '#stream', 'external' => false, 'is_hidden' => ''],
        ['label' => 'Ellene Masri', 'href' => 'https://www.tiktok.com/@ellenemasri', 'external' => true, 'is_hidden' => ''],
        ['label' => 'Stream · Watch · Share', 'href' => '#video', 'external' => false],
    ];
}

// Insert a music icon link in the marquee flow (between "Mayami, My Miami" and "Out Now!" when possible).
$display_items = [];
$music_inserted = false;
foreach ($marquee_items as $item) {
    $display_items[] = [
        'type' => 'link',
        'item' => $item,
    ];

    $label = strtolower(trim((string) ($item['label'] ?? '')));
    if ($show_music_icon && !$music_inserted && $label === 'mayami, my miami') {
        $display_items[] = [
            'type' => 'music',
        ];
        $music_inserted = true;
    }
}

if ($show_music_icon && !$music_inserted) {
    array_splice($display_items, min(1, count($display_items)), 0, [[
        'type' => 'music',
    ]]);
}

$mobile_title = !empty($marquee_items[0]['label']) ? $marquee_items[0]['label'] : 'Menu';
$mobile_items = $display_items;
if (!empty($mobile_items) && isset($mobile_items[0]['type']) && $mobile_items[0]['type'] === 'link') {
    $first_mobile_label = strtolower(trim((string) ($mobile_items[0]['item']['label'] ?? '')));
    if ($first_mobile_label === strtolower(trim((string) $mobile_title))) {
        array_shift($mobile_items);
    }
}

$mobile_link_items = array_values(array_filter($mobile_items, static function ($entry) {
    return isset($entry['type']) && $entry['type'] === 'link';
}));

usort($mobile_link_items, static function ($a, $b) {
    $labelA = strtolower(trim((string) ($a['item']['label'] ?? '')));
    $labelB = strtolower(trim((string) ($b['item']['label'] ?? '')));

    $weight = static function ($label) {
        if (strpos($label, 'out') !== false) return 1;
        if (strpos($label, 'stream') !== false && strpos($label, 'watch') !== false) return 2;
        if (strpos($label, 'ellene') !== false) return 3;
        return 99;
    };

    return $weight($labelA) <=> $weight($labelB);
});

$build_spotify_embed_url = static function ($url) {
    if (empty($url) || !is_string($url)) {
        return '';
    }

    $parts = wp_parse_url($url);
    if (empty($parts['host']) || stripos($parts['host'], 'spotify.com') === false || empty($parts['path'])) {
        return '';
    }

    $segments = array_values(array_filter(explode('/', trim($parts['path'], '/'))));
    if (empty($segments)) {
        return '';
    }

    $locale_pattern = '/^(?:[a-z]{2}(?:-[a-z]{2})?|intl-[a-z]{2})$/i';
    $offset = 0;
    if (isset($segments[0]) && preg_match($locale_pattern, $segments[0])) {
        $offset = 1;
    }

    $type = $segments[$offset] ?? '';
    $id = $segments[$offset + 1] ?? '';
    if (!$type || !$id) {
        return '';
    }

    if (!in_array($type, array('track', 'album', 'artist', 'playlist'), true)) {
        return '';
    }

    return sprintf('https://open.spotify.com/embed/%s/%s?utm_source=generator', rawurlencode($type), rawurlencode($id));
};

$spotify_embed_url = $build_spotify_embed_url($link_spotify);
?>
<style>
    #hero-marquee .marquee-track {
        overflow: hidden;
        width: 100%;
    }

    #hero-marquee .marquee-scroller {
        display: flex;
        width: max-content;
        gap: 0;
        animation: none;
    }

    #hero-marquee .marquee-line {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 40px;
        min-width: 100vw;
        padding: 0 20px;
        white-space: nowrap;
        flex-shrink: 0;
    }

    @media (min-width: 768px) {
        #hero-marquee .marquee-scroller {
            animation: mayami-marquee-scroll 24s linear infinite;
            will-change: transform;
        }

        #hero-marquee .marquee-track:hover .marquee-scroller {
            animation-play-state: paused;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        #hero-marquee .marquee-scroller {
            animation: none !important;
        }
    }

    @keyframes mayami-marquee-scroll {
        from {
            transform: translateX(0);
        }
        to {
            transform: translateX(-33.3333%);
        }
    }

    #hero-marquee-desktop {
        display: block;
    }

    #hero-marquee-mobile,
    #hero-marquee-mobile-panel {
        display: none;
    }

    #hero-marquee .marquee-player {
        display: none;
    }

    #hero-marquee .marquee-player.is-active {
        display: block;
    }

    #hero-marquee .marquee-play-glyph {
        display: inline-block;
        font-size: 14px;
        line-height: 1;
        transform: translateY(-1px);
    }

    @media (max-width: 767px) {
        #hero-marquee-desktop {
            display: none;
        }

        #hero-marquee-mobile {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 0 12px;
        }

        #hero-marquee-mobile-panel {
            display: none;
            padding: 8px 12px 0;
        }

        #hero-marquee-mobile-panel.is-open {
            display: block;
        }

        #hero-marquee-mobile-panel .mobile-links {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px 10px;
            font-size: 13px;
            line-height: 1.2;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        #hero-marquee-mobile-panel .mobile-links .star-aqua {
            color: var(--aqua);
        }

        #hero-marquee-mobile-panel .mobile-links .star-magenta {
            color: var(--magenta);
        }
    }
</style>
<div id="hero-marquee" class="relative z-20 overflow-hidden border-y-2 border-ink bg-ink py-3">
    <div id="hero-marquee-mobile">
        <div class="flex items-center gap-2">
            <span class="font-poster text-sm uppercase tracking-[0.2em] text-cream"><?php echo esc_html($mobile_title); ?></span>
            <span class="star-aqua">✦</span>
            <?php if ($show_music_icon): ?>
            <button type="button" aria-label="Play on Spotify" title="Play on Spotify" aria-expanded="false" data-spotify-url="<?php echo esc_url($music_link); ?>" class="js-marquee-spotify-toggle inline-flex cursor-pointer items-center justify-center transition hover:text-aqua text-cream">
                <span class="marquee-play-glyph" aria-hidden="true">&#9654;</span>
            </button>
            <?php endif; ?>
        </div>
        <button type="button" class="js-marquee-mobile-toggle inline-flex h-8 w-8 items-center justify-center rounded-full border border-cream/40 text-cream transition hover:text-aqua" aria-label="Open marquee menu" aria-expanded="false" aria-controls="hero-marquee-mobile-panel">
            <i class="fa-solid fa-bars" aria-hidden="true"></i>
        </button>
    </div>

    <div id="hero-marquee-mobile-panel" aria-hidden="true">
        <div class="mobile-links font-poster text-cream">
            <?php foreach ($mobile_link_items as $index => $entry):
                $item = $entry['item'];
                $href = !empty($item['href']) ? $item['href'] : '#';
                $label = !empty($item['label']) ? $item['label'] : '';
                $is_external = !empty($item['external']);
                $target = $is_external ? '_blank' : '_self';
                $rel = $is_external ? 'noreferrer' : '';
            ?>
                <a href="<?php echo esc_url($href); ?>" <?php if ($is_external): ?>target="<?php echo esc_attr($target); ?>" rel="<?php echo esc_attr($rel); ?>"<?php endif; ?> class="transition hover:text-aqua"><?php echo esc_html($label); ?></a>

                <?php if ($index < count($mobile_link_items) - 1): ?>
                    <span class="<?php echo $index % 2 === 0 ? 'star-aqua' : 'star-magenta'; ?>">✦</span>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="hero-marquee-desktop" class="marquee-track whitespace-nowrap">
        <div class="marquee-scroller">
            <?php for ($copy = 0; $copy < 3; $copy++): ?>
            <div class="marquee-line font-poster text-lg uppercase tracking-widest text-cream">
                <?php foreach ($display_items as $index => $entry):
                    $colors = ['text-aqua', 'text-magenta'];
                    $separator_color = $colors[$index % 2];
                ?>
                <?php if ($entry['type'] === 'music'): ?>
                <button type="button" aria-label="Play on Spotify" title="Play on Spotify" aria-expanded="false" data-spotify-url="<?php echo esc_url($music_link); ?>" class="js-marquee-spotify-toggle inline-flex cursor-pointer items-center justify-center transition hover:text-aqua">
                    <span class="marquee-play-glyph" aria-hidden="true">&#9654;</span>
                </button>
                <?php else:
                    $item = $entry['item'];
                    $href = !empty($item['href']) ? $item['href'] : '#';
                    $label = !empty($item['label']) ? $item['label'] : '';
                    $is_external = !empty($item['external']);
                    $target = $is_external ? '_blank' : '_self';
                    $rel = $is_external ? 'noreferrer' : '';
                ?>
                <a href="<?php echo esc_url($href); ?>" <?php if ($is_external): ?>target="<?php echo esc_attr($target); ?>" rel="<?php echo esc_attr($rel); ?>"<?php endif; ?> class="transition hover:text-aqua">
                    <?php echo esc_html($label); ?>
                </a>
                <?php endif; ?>
                <?php if ($index < count($display_items) - 1): ?>
                <span class="<?php echo esc_attr($separator_color); ?>">✦</span>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <?php endfor; ?>
        </div>
    </div>

    <?php if (!empty($spotify_embed_url)) : ?>
    <div id="marquee-spotify-player" class="marquee-player mt-3 px-5" aria-live="polite">
        <div class="overflow-hidden rounded-2xl border-2 border-ink bg-cream p-2" style="box-shadow: 6px 6px 0 var(--ink);">
            <iframe title="Spotify player" data-src="<?php echo esc_url($spotify_embed_url); ?>" width="100%" height="152" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>
        </div>
    </div>
    <?php endif; ?>
</div>
