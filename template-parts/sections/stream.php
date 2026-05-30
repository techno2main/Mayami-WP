<?php
/**
 * Template part - Stream Section
 * 
 * @package Mayami
 */

$stream_kicker = trim((string) cmb2_get_option('mayami_landing_options', 'stream_kicker'));
$stream_title_prefix = trim((string) cmb2_get_option('mayami_landing_options', 'stream_title_prefix'));
$stream_title_highlight = trim((string) cmb2_get_option('mayami_landing_options', 'stream_title_highlight'));
$stream_title_logo_url = '';

if ($stream_title_highlight !== '' && filter_var($stream_title_highlight, FILTER_VALIDATE_URL)) {
    $stream_title_logo_url = $stream_title_highlight;
}

$stream_title_logo_alt = $stream_title_prefix !== '' ? $stream_title_prefix . ' logo' : 'Stream logo';
$stream_availability_text = trim((string) cmb2_get_option('mayami_landing_options', 'stream_availability_text'));
$stream_card_label = trim((string) cmb2_get_option('mayami_landing_options', 'stream_card_label'));

$stream_platforms = cmb2_get_option('mayami_landing_options', 'stream_platforms');
if (!is_array($stream_platforms)) {
    $stream_platforms = array();
}

$active_stream_platforms_count = 0;

$stream_platform_meta = array(
    'spotify' => array('icon' => 'fa-spotify', 'icon_style' => 'brands', 'color' => '#1DB954', 'inline_player' => false),
    'apple-music' => array('icon' => 'fa-apple', 'icon_style' => 'brands', 'color' => '#FC3C44', 'inline_player' => false),
    'youtube-music' => array('icon' => 'fa-youtube', 'icon_style' => 'brands', 'color' => '#FF0000', 'inline_player' => false),
    'deezer' => array('icon' => 'fa-deezer', 'icon_style' => 'brands', 'color' => '#A238FF', 'inline_player' => false),
    'amazon-music' => array('icon' => 'fa-amazon', 'icon_style' => 'brands', 'color' => '#00A8E1', 'inline_player' => false),
    'soundcloud' => array('icon' => 'fa-soundcloud', 'icon_style' => 'brands', 'color' => '#FF5500', 'inline_player' => false),
);
?>
<style>
    #stream .stream-title-line {
        display: flex;
        align-items: center;
        gap: 14px;
        flex-wrap: nowrap;
        max-width: 100%;
    }

    #stream .stream-title-text {
        flex: 0 0 auto;
    }

    #stream .stream-title-logo {
        width: min(43vw, 170px);
        height: auto;
        max-width: 100%;
        object-fit: contain;
        flex: 0 1 auto;
    }

    @media (min-width: 640px) {
        #stream .stream-title-logo {
            width: min(30vw, 240px);
        }
    }
</style>
<section id="stream" class="relative bg-[#6a1b78] py-20 sm:py-28">
    <div class="absolute inset-0 grain"></div>
    <div class="relative mx-auto max-w-6xl px-5 sm:px-8">
        <div class="mb-4 flex justify-end gap-4">
            <a href="#social" aria-label="Section suivante" class="inline-flex items-center justify-center text-xl leading-none text-cream/80 transition hover:text-aqua">↓</a>
            <a href="#hero" aria-label="Section précédente" class="inline-flex items-center justify-center text-xl leading-none text-cream/80 transition hover:text-aqua">↑</a>
        </div>
        <div class="mb-10 flex items-end justify-between gap-4">
            <div>
                <p class="font-poster text-xs uppercase tracking-[0.3em] text-cream/80"><?php echo esc_html($stream_kicker); ?></p>
                <h2 class="stream-title-line mt-2 font-display text-4xl leading-[0.9] text-cream sm:text-6xl">
                    <span class="stream-title-text"><?php echo esc_html($stream_title_prefix); ?></span>
                    <?php if ($stream_title_logo_url !== ''): ?>
                        <img src="<?php echo esc_url($stream_title_logo_url); ?>" alt="<?php echo esc_attr($stream_title_logo_alt); ?>" class="stream-title-logo" loading="lazy" decoding="async" />
                    <?php endif; ?>
                </h2>
            </div>
            <div class="flex items-center gap-3">
                <span class="hidden font-poster text-sm uppercase tracking-[0.2em] text-cream/80 sm:block">
                    <?php echo esc_html($stream_availability_text); ?>
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($stream_platforms as $platform_index => $platform):
                $platform_is_active = !empty($platform['is_active']);
                $platform_label = isset($platform['label']) ? trim((string) $platform['label']) : '';
                $platform_href = isset($platform['href']) ? trim((string) $platform['href']) : '';

                if (!$platform_is_active || $platform_label === '' || $platform_href === '') {
                    continue;
                }

                $platform_key = sanitize_title($platform_label);
                if ($platform_key === '') {
                    $platform_key = 'platform-' . $platform_index;
                }

                $platform_meta = isset($stream_platform_meta[$platform_key]) ? $stream_platform_meta[$platform_key] : array(
                    'icon' => 'fa-link',
                    'icon_style' => 'solid',
                    'color' => '#410b49',
                    'inline_player' => false,
                );
                $icon_style_class = (isset($platform_meta['icon_style']) && $platform_meta['icon_style'] === 'solid') ? 'fa-solid' : 'fa-brands';

                $active_stream_platforms_count++;
                ?>
                <a href="<?php echo esc_url($platform_href); ?>" target="_blank" rel="noreferrer" data-platform="<?php echo esc_attr($platform_key); ?>" data-has-player="<?php echo !empty($platform_meta['inline_player']) ? '1' : '0'; ?>" aria-expanded="false" class="platform-card group relative flex items-center justify-between rounded-2xl border-2 border-ink bg-cream px-6 py-5 text-ink transition hover:-translate-y-1 hover:-translate-x-0.5" style="box-shadow: 6px 6px 0 var(--ink)">
                    <div>
                        <p class="font-poster text-[10px] uppercase tracking-[0.25em] opacity-70"><?php echo esc_html($stream_card_label); ?></p>
                        <p class="flex items-center gap-2 font-display text-2xl leading-none">
                            <span class="text-[0.9em]" style="color: <?php echo esc_attr($platform_meta['color']); ?>;" aria-hidden="true"><i class="<?php echo esc_attr($icon_style_class); ?> <?php echo esc_attr($platform_meta['icon']); ?>"></i></span>
                            <span><?php echo esc_html($platform_label); ?></span>
                        </p>
                    </div>
                    <span class="font-poster text-2xl transition group-hover:translate-x-1">→</span>
                </a>
            <?php endforeach; ?>

            <?php if ($active_stream_platforms_count === 0): ?>
                <div class="rounded-2xl border-2 border-ink bg-cream px-6 py-5 text-ink" style="box-shadow: 6px 6px 0 var(--ink)">
                    <p class="font-poster text-[10px] uppercase tracking-[0.25em] opacity-70">Listen</p>
                    <p class="mt-1 font-display text-2xl leading-none">Aucune plateforme active</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
