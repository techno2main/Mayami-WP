<?php
/**
 * Template part - Stream Section
 * 
 * @package Mayami
 */

$stream_kicker = cmb2_get_option('mayami_landing_options', 'stream_kicker') ?: '01 / Listen';
$stream_title_prefix = cmb2_get_option('mayami_landing_options', 'stream_title_prefix') ?: 'Stream';
$stream_title_highlight = cmb2_get_option('mayami_landing_options', 'stream_title_highlight') ?: 'MAYAMI';
$stream_title_logo_url = 'https://ellenemasri.pro/wp-content/uploads/2026/05/logo-1.png';
$stream_availability_text = cmb2_get_option('mayami_landing_options', 'stream_availability_text') ?: 'Available everywhere';
$stream_card_label = cmb2_get_option('mayami_landing_options', 'stream_card_label') ?: 'Listen on';

$link_spotify = cmb2_get_option('mayami_landing_options', 'link_spotify') ?: 'https://open.spotify.com/intl-fr/track/3rzrziofCOwRrI1r99IUbQ?si=a2cd3f4cbe364a94';
$link_apple_music = cmb2_get_option('mayami_landing_options', 'link_apple_music') ?: 'https://music.apple.com/fr/song/mayami-my-miami/6771742499';
$link_youtube_music = cmb2_get_option('mayami_landing_options', 'link_youtube_music') ?: 'https://youtu.be/EH_QcQ92hSk?si=gpybhKJbZrDN1Ew5';
$link_deezer = cmb2_get_option('mayami_landing_options', 'link_deezer') ?: 'https://link.deezer.com/s/33p3MydevJFz4yqu2aEam';
$link_amazon_music = cmb2_get_option('mayami_landing_options', 'link_amazon_music') ?: 'https://music.amazon.com/tracks/B0H2FR3WHQ?marketplaceId=ATVPDKIKX0DER&musicTerritory=US&ref=dm_sh_gPJPR79AtgfLS0EFarS9Xwi57';
$link_soundcloud = cmb2_get_option('mayami_landing_options', 'link_soundcloud') ?: 'https://soundcloud.com/ellenemasri';
$default_stream_platforms = array(
    array('is_active' => 'on', 'label' => 'Spotify', 'href' => $link_spotify),
    array('is_active' => 'on', 'label' => 'Apple Music', 'href' => $link_apple_music),
    array('is_active' => 'on', 'label' => 'YouTube Music', 'href' => $link_youtube_music),
    array('is_active' => 'on', 'label' => 'Deezer', 'href' => $link_deezer),
    array('is_active' => 'on', 'label' => 'Amazon Music', 'href' => $link_amazon_music),
    array('is_active' => 'on', 'label' => 'SoundCloud', 'href' => $link_soundcloud),
);
$stream_platforms = cmb2_get_option('mayami_landing_options', 'stream_platforms');
if (!is_array($stream_platforms) || empty($stream_platforms)) {
    $stream_platforms = $default_stream_platforms;
}

$active_stream_platforms = array();

$stream_platform_meta = array(
    'spotify' => array('icon' => 'fa-spotify', 'icon_style' => 'brands', 'color' => '#1DB954', 'inline_player' => true),
    'apple-music' => array('icon' => 'fa-apple', 'icon_style' => 'brands', 'color' => '#FC3C44', 'inline_player' => true),
    'youtube-music' => array('icon' => 'fa-youtube', 'icon_style' => 'brands', 'color' => '#FF0000', 'inline_player' => true),
    'deezer' => array('icon' => 'fa-deezer', 'icon_style' => 'brands', 'color' => '#A238FF', 'inline_player' => true),
    'amazon-music' => array('icon' => 'fa-amazon', 'icon_style' => 'brands', 'color' => '#00A8E1', 'inline_player' => true),
    'soundcloud' => array('icon' => 'fa-soundcloud', 'icon_style' => 'brands', 'color' => '#FF5500', 'inline_player' => true),
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
                    <img src="<?php echo esc_url($stream_title_logo_url); ?>" alt="<?php echo esc_attr($stream_title_highlight); ?>" class="stream-title-logo" loading="lazy" decoding="async" />
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

                $active_stream_platforms[] = array(
                    'key' => $platform_key,
                    'label' => $platform_label,
                    'href' => $platform_href,
                    'meta' => $platform_meta,
                );
                ?>
                <a href="<?php echo esc_url($platform_href); ?>" data-platform="<?php echo esc_attr($platform_key); ?>" data-has-player="<?php echo !empty($platform_meta['inline_player']) ? '1' : '0'; ?>" aria-expanded="false" class="platform-card group relative flex items-center justify-between rounded-2xl border-2 border-ink bg-cream px-6 py-5 text-ink transition hover:-translate-y-1 hover:-translate-x-0.5" style="box-shadow: 6px 6px 0 var(--ink)">
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
        </div>

        <?php foreach ($active_stream_platforms as $platform_data):
            $platform_key = $platform_data['key'];
            $platform_label = $platform_data['label'];
            $platform_href = $platform_data['href'];
            $platform_meta = $platform_data['meta'];
            ?>
            <div id="player-mobile-<?php echo esc_attr($platform_key); ?>" class="platform-player-mobile mt-6 overflow-hidden rounded-2xl border-2 border-ink bg-cream p-2" style="box-shadow: 6px 6px 0 var(--ink);">
                <?php if (!empty($platform_meta['inline_player'])): ?>
                    <?php if ($platform_key === 'spotify'): ?>
                        <iframe title="Spotify player" src="https://open.spotify.com/embed/track/3rzrziofCOwRrI1r99IUbQ?utm_source=generator" width="100%" height="352" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>
                    <?php elseif ($platform_key === 'apple-music'): ?>
                        <iframe title="Apple Music player" src="https://embed.music.apple.com/fr/song/mayami-my-miami/6771742499" width="100%" height="175" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>
                    <?php elseif ($platform_key === 'youtube-music'): ?>
                        <iframe title="YouTube Music player" src="https://www.youtube.com/embed/EH_QcQ92hSk" width="100%" height="352" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>
                    <?php elseif ($platform_key === 'deezer'): ?>
                        <iframe title="Deezer player" src="https://widget.deezer.com/widget/dark/track/4034160411" width="100%" height="352" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>
                    <?php elseif ($platform_key === 'amazon-music'): ?>
                        <iframe
                            id="AmazonMusicEmbedB0H2FR3WHQMobile"
                            title="Amazon Music player"
                            src="https://music.amazon.com/embed/B0H2FR3WHQ/?id=zXPr2RCMij&marketplaceId=ATVPDKIKX0DER&musicTerritory=US"
                            width="100%"
                            height="352"
                            frameborder="0"
                            loading="lazy"
                            style="border-radius:20px;max-width:100%;display:block;margin:0 auto"
                        ></iframe>
                    <?php elseif ($platform_key === 'soundcloud'): ?>
                        <iframe title="SoundCloud player" src="https://w.soundcloud.com/player/?url=https%3A%2F%2Fsoundcloud.com%2Fellenemasri&color=%23ff5500&auto_play=false&show_user=true" width="100%" height="352" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="rounded-2xl border-2 border-ink bg-cream p-5" style="box-shadow: 6px 6px 0 var(--ink);">
                        <p class="font-poster text-sm uppercase tracking-[0.2em] text-ink"><?php echo esc_html($platform_label); ?></p>
                        <p class="mt-2 text-sm text-ink/75">Cette plateforme ne propose pas de lecteur intégré fiable. Ouvre le lien dans un nouvel onglet.</p>
                        <a href="<?php echo esc_url($platform_href); ?>" target="_blank" rel="noreferrer" class="mt-4 inline-flex items-center gap-2 rounded-full border-2 border-ink bg-aqua px-4 py-2 font-poster text-xs uppercase tracking-[0.15em] text-ink">Ouvrir ↗</a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <?php foreach ($active_stream_platforms as $platform_data):
            $platform_key = $platform_data['key'];
            $platform_label = $platform_data['label'];
            $platform_href = $platform_data['href'];
            $platform_meta = $platform_data['meta'];
            ?>
            <div id="player-desktop-<?php echo esc_attr($platform_key); ?>" class="platform-player-desktop mt-6 overflow-hidden rounded-2xl border-2 border-ink bg-cream p-2" style="box-shadow: 6px 6px 0 var(--ink);">
                <?php if (!empty($platform_meta['inline_player'])): ?>
                    <?php if ($platform_key === 'spotify'): ?>
                        <iframe title="Spotify player" src="https://open.spotify.com/embed/track/3rzrziofCOwRrI1r99IUbQ?utm_source=generator" width="100%" height="352" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>
                    <?php elseif ($platform_key === 'apple-music'): ?>
                        <iframe title="Apple Music player" src="https://embed.music.apple.com/fr/song/mayami-my-miami/6771742499" width="100%" height="175" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>
                    <?php elseif ($platform_key === 'youtube-music'): ?>
                        <iframe title="YouTube Music player" src="https://www.youtube.com/embed/EH_QcQ92hSk" width="100%" height="352" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>
                    <?php elseif ($platform_key === 'deezer'): ?>
                        <iframe title="Deezer player" src="https://widget.deezer.com/widget/dark/track/4034160411" width="100%" height="352" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>
                    <?php elseif ($platform_key === 'amazon-music'): ?>
                        <iframe
                            id="AmazonMusicEmbedB0H2FR3WHQDesktop"
                            title="Amazon Music player"
                            src="https://music.amazon.com/embed/B0H2FR3WHQ/?id=zXPr2RCMij&marketplaceId=ATVPDKIKX0DER&musicTerritory=US"
                            width="100%"
                            height="352"
                            frameborder="0"
                            loading="lazy"
                            style="border-radius:20px;max-width:100%;display:block;margin:0 auto"
                        ></iframe>
                    <?php elseif ($platform_key === 'soundcloud'): ?>
                        <iframe title="SoundCloud player" src="https://w.soundcloud.com/player/?url=https%3A%2F%2Fsoundcloud.com%2Fellenemasri&color=%23ff5500&auto_play=false&show_user=true" width="100%" height="352" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="rounded-2xl border-2 border-ink bg-cream p-5" style="box-shadow: 6px 6px 0 var(--ink);">
                        <p class="font-poster text-sm uppercase tracking-[0.2em] text-ink"><?php echo esc_html($platform_label); ?></p>
                        <p class="mt-2 text-sm text-ink/75">Cette plateforme ne propose pas de lecteur intégré fiable. Ouvre le lien dans un nouvel onglet.</p>
                        <a href="<?php echo esc_url($platform_href); ?>" target="_blank" rel="noreferrer" class="mt-4 inline-flex items-center gap-2 rounded-full border-2 border-ink bg-aqua px-4 py-2 font-poster text-xs uppercase tracking-[0.15em] text-ink">Ouvrir ↗</a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>
