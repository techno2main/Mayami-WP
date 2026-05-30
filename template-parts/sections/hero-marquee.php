<?php
/**
 * Template part - Hero Marquee
 *
 * @package Mayami
 */

$marquee_items = cmb2_get_option('mayami_landing_options', 'marquee_items');

$link_spotify = cmb2_get_option('mayami_landing_options', 'link_spotify') ?: 'https://open.spotify.com/intl-fr/track/3rzrziofCOwRrI1r99IUbQ?si=a2cd3f4cbe364a94';
$link_apple_music = cmb2_get_option('mayami_landing_options', 'link_apple_music') ?: 'https://music.apple.com/fr/song/mayami-my-miami/6771742499';
$link_youtube_music = cmb2_get_option('mayami_landing_options', 'link_youtube_music') ?: 'https://youtu.be/EH_QcQ92hSk?si=gpybhKJbZrDN1Ew5';
$link_deezer = cmb2_get_option('mayami_landing_options', 'link_deezer') ?: 'https://link.deezer.com/s/33p3MydevJFz4yqu2aEam';
$link_amazon_music = cmb2_get_option('mayami_landing_options', 'link_amazon_music') ?: 'https://music.amazon.com/tracks/B0H2FR3WHQ?marketplaceId=ATVPDKIKX0DER&musicTerritory=US&ref=dm_sh_gPJPR79AtgfLS0EFarS9Xwi57';
$link_soundcloud = cmb2_get_option('mayami_landing_options', 'link_soundcloud') ?: 'https://soundcloud.com/ellenemasri';
$marquee_logo_png = cmb2_get_option('mayami_landing_options', 'marquee_logo_png') ?: (get_template_directory_uri() . '/assets/mayami-logo.png');
$hide_marquee_visual = !empty(cmb2_get_option('mayami_landing_options', 'marquee_logo_hidden'));

$show_platform_icons = true;
$open_platform_icons_in_new_tab = false;

if (is_array($marquee_items)) {
    $marquee_items = array_values(array_filter($marquee_items, static function ($item) {
        if (!is_array($item)) {
            return false;
        }

        if (!empty($item['is_hidden'])) {
            return false;
        }

        $label = strtolower(trim(remove_accents((string) ($item['label'] ?? ''))));
        if (strpos($label, 'out now') !== false) {
            return false;
        }

        if ($label === 'icone plateformes' || $label === 'icones stream' || $label === 'afficher les icones') {
            return false;
        }

        return !empty($item['label']) && !empty($item['href']);
    }));
}

if (empty($marquee_items)) {
    $marquee_items = array(
        array('label' => 'Mayami, My Miami', 'href' => '#hero', 'external' => false, 'is_hidden' => ''),
        array('label' => 'Stream · Watch · Share', 'href' => '#stream', 'external' => false, 'is_hidden' => ''),
    );
}

$desktop_left_item = $marquee_items[0] ?? array('label' => 'Mayami, My Miami', 'href' => '#page-top', 'external' => false);
$desktop_center_item = null;
$desktop_right_item = null;
foreach ($marquee_items as $item) {
    $label = strtolower(trim((string) ($item['label'] ?? '')));
    if (strpos($label, 'stream') !== false && strpos($label, 'watch') !== false) {
        $desktop_center_item = $item;
    }

    if (strpos($label, 'ellene') !== false) {
        $desktop_right_item = $item;
    }
}
if (!$desktop_center_item) {
    $desktop_center_item = $marquee_items[1] ?? array('label' => 'Stream · Watch · Share', 'href' => '#stream', 'external' => false);
}
if (!$desktop_right_item) {
    $desktop_right_item = array('label' => 'Ellene Leya Masri', 'href' => '#social', 'external' => false);
}

$stream_platforms = cmb2_get_option('mayami_landing_options', 'stream_platforms');
if (!is_array($stream_platforms) || empty($stream_platforms)) {
    $stream_platforms = array(
        array('is_active' => 'on', 'label' => 'Spotify', 'href' => $link_spotify),
        array('is_active' => 'on', 'label' => 'Apple Music', 'href' => $link_apple_music),
        array('is_active' => 'on', 'label' => 'YouTube Music', 'href' => $link_youtube_music),
        array('is_active' => 'on', 'label' => 'Deezer', 'href' => $link_deezer),
        array('is_active' => 'on', 'label' => 'Amazon Music', 'href' => $link_amazon_music),
        array('is_active' => 'on', 'label' => 'SoundCloud', 'href' => $link_soundcloud),
    );
}

$platform_icon_map = array(
    'spotify' => array('icon' => 'fa-spotify', 'label' => 'Spotify'),
    'apple-music' => array('icon' => 'fa-apple', 'label' => 'Apple Music'),
    'youtube-music' => array('icon' => 'fa-youtube', 'label' => 'YouTube Music'),
    'deezer' => array('icon' => 'fa-deezer', 'label' => 'Deezer'),
    'amazon-music' => array('icon' => 'fa-amazon', 'label' => 'Amazon Music'),
    'soundcloud' => array('icon' => 'fa-soundcloud', 'label' => 'SoundCloud'),
);

$marquee_platform_links = array();
if ($show_platform_icons) {
    foreach ($stream_platforms as $platform) {
        $is_active = !empty($platform['is_active']);
        $label = isset($platform['label']) ? trim((string) $platform['label']) : '';
        $href = isset($platform['href']) ? trim((string) $platform['href']) : '';

        if (!$is_active || $label === '' || $href === '') {
            continue;
        }

        $key = sanitize_title($label);
        if (!isset($platform_icon_map[$key])) {
            continue;
        }

        $marquee_platform_links[] = array(
            'href' => $open_platform_icons_in_new_tab ? $href : '#stream',
            'platform' => $key,
            'icon' => $platform_icon_map[$key]['icon'],
            'label' => $platform_icon_map[$key]['label'],
            'external' => $open_platform_icons_in_new_tab,
        );
    }
}

$mobile_title = !empty($desktop_left_item['label']) ? $desktop_left_item['label'] : 'Mayami, My Miami';
$mobile_stream_link = $desktop_center_item;
?>
<style>
    #hero-marquee {
        border-top: 2px solid var(--ink);
        border-bottom: 2px solid var(--ink);
        background: var(--ink);
    }

    #hero-marquee-desktop.marquee-track {
        animation: none !important;
        transform: none !important;
        max-width: 80rem;
        margin: 0 auto;
        padding: 0 20px;
    }

    #hero-marquee .marquee-scroller {
        display: block;
        width: 100%;
        animation: none !important;
        transform: none !important;
    }

    #hero-marquee .marquee-line-desktop {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        align-items: center;
        gap: 14px;
        width: 100%;
        padding-bottom: 10px;
    }

    #hero-marquee .marquee-col-left {
        justify-self: start;
    }

    #hero-marquee .marquee-col-center {
        justify-self: center;
    }

    #hero-marquee .marquee-col-right {
        justify-self: end;
        display: inline-flex;
        align-items: center;
    }

    #hero-marquee .marquee-link {
        color: var(--cream);
        transition: color .15s ease;
    }

    #hero-marquee .marquee-link:hover {
        color: var(--aqua);
    }

    #hero-marquee .marquee-platform-icons {
        display: inline-flex;
        align-items: center;
        gap: 14px;
    }

    #hero-marquee .marquee-platform-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 0;
        background: transparent;
        padding: 0;
        color: var(--cream);
        font-size: 18px;
        line-height: 1;
        transition: color .15s ease, transform .15s ease;
        cursor: pointer;
    }

    #hero-marquee .marquee-platform-link:hover {
        color: var(--aqua);
        transform: translateY(-1px);
    }

    #hero-marquee .marquee-logo-row {
        display: grid;
        grid-template-columns: auto 1fr;
        align-items: center;
        gap: 16px;
        width: 100%;
        max-width: 80rem;
        margin: 0 auto;
        padding: 0 20px 16px 0;
    }

    #hero-marquee .marquee-logo-row.no-visual {
        grid-template-columns: 1fr;
    }

    #hero-marquee .marquee-logo-mark,
    #hero-marquee .marquee-logo-year {
        flex: 0 0 auto;
        color: oklch(0.92 0.18 95);
        line-height: 1;
        opacity: 0.9;
        white-space: nowrap;
    }

    #hero-marquee .marquee-logo-image {
        display: block;
        width: auto;
        height: auto;
        max-width: 220px;
        justify-self: start;
    }

    #hero-marquee .marquee-logo-copy {
        display: inline-flex;
        align-items: center;
        justify-self: end;
        gap: 10px;
        text-align: right;
    }

    #hero-marquee .marquee-logo-mark {
        font-family: "Brush Script MT", "Segoe Script", "Snell Roundhand", cursive;
        font-size: 20px;
        letter-spacing: 0.02em;
    }

    #hero-marquee .marquee-logo-year {
        font-family: var(--font-poster);
        font-size: 14px;
        letter-spacing: 0.22em;
        text-align: right;
    }

    #hero-marquee-mobile {
        display: none;
    }

    @media (min-width: 768px) {
        #hero-marquee-desktop.marquee-track {
            padding: 0 32px;
        }

        #hero-marquee .marquee-platform-icons {
            gap: 26px;
        }

        #hero-marquee .marquee-platform-link {
            font-size: 26px;
        }

        #hero-marquee .marquee-logo-row {
            padding: 0 32px 18px 0;
        }

        #hero-marquee .marquee-logo-copy {
            gap: 12px;
        }

        #hero-marquee .marquee-logo-mark {
            font-size: 20px;
        }

        #hero-marquee .marquee-logo-year {
            font-size: 14px;
        }

        #hero-marquee .marquee-logo-image {
            max-width: 280px;
        }
    }

    @media (max-width: 767px) {
        #hero-marquee-desktop {
            display: none;
        }

        #hero-marquee-mobile {
            display: flex;
            flex-direction: column;
            align-items: stretch;
            gap: 8px;
            padding: 0 12px;
            width: 100%;
        }

        #hero-marquee .marquee-logo-row {
            padding: 0 12px 12px 0;
        }

        #hero-marquee .marquee-logo-copy {
            gap: 8px;
        }

        #hero-marquee .marquee-logo-mark {
            font-size: 13px;
            letter-spacing: 0.18em;
        }

        #hero-marquee .marquee-logo-year {
            font-size: 11px;
            letter-spacing: 0.18em;
        }

        #hero-marquee .marquee-logo-image {
            max-width: 160px;
        }

        #hero-marquee .marquee-mobile-row {
            display: flex;
            align-items: center;
            width: 100%;
        }

        #hero-marquee .marquee-mobile-row-top {
            justify-content: flex-start;
        }

        #hero-marquee .marquee-mobile-row-bottom {
            display: none;
        }

        #hero-marquee .marquee-mobile-title {
            color: var(--cream);
            font-size: 14px;
            line-height: 1;
            letter-spacing: 0.08em;
            transition: color .15s ease;
            white-space: nowrap;
        }

        #hero-marquee .marquee-mobile-title:hover {
            color: var(--aqua);
        }

        #hero-marquee .marquee-mobile-row-top .marquee-platform-icons {
            margin-left: auto;
            gap: 8px;
            flex-shrink: 0;
        }

        #hero-marquee .marquee-mobile-row-top .marquee-platform-link {
            font-size: 18px;
        }
    }
</style>
<div id="hero-marquee" class="relative z-20 overflow-hidden py-3">
    <div class="marquee-logo-row<?php echo $hide_marquee_visual ? ' no-visual' : ''; ?>">
        <?php if (!$hide_marquee_visual): ?>
            <img src="<?php echo esc_url($marquee_logo_png); ?>" alt="Mayami" class="marquee-logo-image" loading="lazy" decoding="async" />
        <?php endif; ?>
        <div class="marquee-logo-copy">
            <?php
                $right_href = !empty($desktop_right_item['href']) ? $desktop_right_item['href'] : '#social';
                $right_label = !empty($desktop_right_item['label']) ? $desktop_right_item['label'] : 'Ellene Leya Masri';
                $right_is_external = false;
                $right_target = $right_is_external ? '_blank' : '_self';
                $right_rel = $right_is_external ? 'noreferrer' : '';
            ?>
            <a href="<?php echo esc_url($right_href); ?>" <?php if ($right_is_external): ?>target="<?php echo esc_attr($right_target); ?>" rel="<?php echo esc_attr($right_rel); ?>"<?php endif; ?> class="marquee-logo-mark marquee-link">
                <?php echo esc_html($right_label); ?>
            </a>
        </div>
    </div>
    <div id="hero-marquee-mobile">
        <div class="marquee-mobile-row marquee-mobile-row-top">
            <a href="#page-top" class="marquee-mobile-title font-poster uppercase"><?php echo esc_html($mobile_title); ?></a>
            <?php if (!empty($marquee_platform_links)): ?>
                <span class="marquee-platform-icons">
                    <?php foreach ($marquee_platform_links as $platform): ?>
                        <button type="button" data-open-platform="<?php echo esc_attr($platform['platform']); ?>" aria-label="<?php echo esc_attr($platform['label']); ?>" title="<?php echo esc_attr($platform['label']); ?>" class="marquee-platform-link">
                            <i class="fa-brands <?php echo esc_attr($platform['icon']); ?>" aria-hidden="true"></i>
                        </button>
                    <?php endforeach; ?>
                </span>
            <?php endif; ?>
        </div>

        <div class="marquee-mobile-row marquee-mobile-row-bottom">
            <?php
                $mobile_stream_href = !empty($mobile_stream_link['href']) ? $mobile_stream_link['href'] : '#stream';
                $mobile_stream_label = !empty($mobile_stream_link['label']) ? $mobile_stream_link['label'] : 'Stream · Watch · Share';
                $mobile_stream_external = false;
                $mobile_stream_target = $mobile_stream_external ? '_blank' : '_self';
                $mobile_stream_rel = $mobile_stream_external ? 'noreferrer' : '';
            ?>
            <a href="<?php echo esc_url($mobile_stream_href); ?>" <?php if ($mobile_stream_external): ?>target="<?php echo esc_attr($mobile_stream_target); ?>" rel="<?php echo esc_attr($mobile_stream_rel); ?>"<?php endif; ?> class="marquee-mobile-stream-link font-poster uppercase"><?php echo esc_html($mobile_stream_label); ?></a>
        </div>
    </div>

    <div id="hero-marquee-desktop" class="marquee-track whitespace-nowrap">
        <div class="marquee-scroller">
            <div class="marquee-line-desktop font-poster text-lg uppercase tracking-widest text-cream">
                <div class="marquee-col-left">
                    <?php
                        $left_href = !empty($desktop_left_item['href']) ? $desktop_left_item['href'] : '#page-top';
                        $left_label = !empty($desktop_left_item['label']) ? $desktop_left_item['label'] : $mobile_title;
                        $left_is_external = false;
                        if (strtolower(trim((string) $left_label)) === strtolower(trim((string) $mobile_title))) {
                            $left_href = '#page-top';
                            $left_is_external = false;
                        }
                        $left_target = $left_is_external ? '_blank' : '_self';
                        $left_rel = $left_is_external ? 'noreferrer' : '';
                    ?>
                    <a href="<?php echo esc_url($left_href); ?>" <?php if ($left_is_external): ?>target="<?php echo esc_attr($left_target); ?>" rel="<?php echo esc_attr($left_rel); ?>"<?php endif; ?> class="marquee-link">
                        <?php echo esc_html($left_label); ?>
                    </a>
                </div>

                <div class="marquee-col-center">
                    <?php
                        $center_href = !empty($desktop_center_item['href']) ? $desktop_center_item['href'] : '#stream';
                        $center_label = !empty($desktop_center_item['label']) ? $desktop_center_item['label'] : 'Stream · Watch · Share';
                        $center_is_external = false;
                        $center_target = $center_is_external ? '_blank' : '_self';
                        $center_rel = $center_is_external ? 'noreferrer' : '';
                    ?>
                    <a href="<?php echo esc_url($center_href); ?>" <?php if ($center_is_external): ?>target="<?php echo esc_attr($center_target); ?>" rel="<?php echo esc_attr($center_rel); ?>"<?php endif; ?> class="marquee-link">
                        <?php echo esc_html($center_label); ?>
                    </a>
                </div>

                <div class="marquee-col-right">
                    <?php if (!empty($marquee_platform_links)): ?>
                        <span class="marquee-platform-icons">
                            <?php foreach ($marquee_platform_links as $platform): ?>
                                <button type="button" data-open-platform="<?php echo esc_attr($platform['platform']); ?>" aria-label="<?php echo esc_attr($platform['label']); ?>" title="<?php echo esc_attr($platform['label']); ?>" class="marquee-platform-link">
                                    <i class="fa-brands <?php echo esc_attr($platform['icon']); ?>" aria-hidden="true"></i>
                                </button>
                            <?php endforeach; ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
