<?php
/**
 * Template part - Stream Section
 * 
 * @package Mayami
 */

$stream_kicker = cmb2_get_option('mayami_landing_options', 'stream_kicker') ?: '01 / Listen';
$stream_title_prefix = cmb2_get_option('mayami_landing_options', 'stream_title_prefix') ?: 'Stream';
$stream_title_highlight = cmb2_get_option('mayami_landing_options', 'stream_title_highlight') ?: 'MAYAMI';
$stream_availability_text = cmb2_get_option('mayami_landing_options', 'stream_availability_text') ?: 'Available everywhere';
$stream_card_label = cmb2_get_option('mayami_landing_options', 'stream_card_label') ?: 'Listen on';

$link_spotify = cmb2_get_option('mayami_landing_options', 'link_spotify') ?: 'https://open.spotify.com/intl-fr/track/3rzrziofCOwRrI1r99IUbQ?si=a2cd3f4cbe364a94';
$link_apple_music = cmb2_get_option('mayami_landing_options', 'link_apple_music') ?: 'https://music.apple.com/fr/song/mayami-my-miami/6771742499';
$link_youtube_music = cmb2_get_option('mayami_landing_options', 'link_youtube_music') ?: 'https://youtu.be/EH_QcQ92hSk?si=gpybhKJbZrDN1Ew5';
$link_deezer = cmb2_get_option('mayami_landing_options', 'link_deezer') ?: 'https://link.deezer.com/s/33p3MydevJFz4yqu2aEam';
$link_amazon_music = cmb2_get_option('mayami_landing_options', 'link_amazon_music') ?: 'https://music.amazon.com/tracks/B0H2FR3WHQ?marketplaceId=ATVPDKIKX0DER&musicTerritory=US&ref=dm_sh_gPJPR79AtgfLS0EFarS9Xwi57';
$link_soundcloud = cmb2_get_option('mayami_landing_options', 'link_soundcloud') ?: 'https://soundcloud.com/ellenemasri';
?>
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
                <h2 class="mt-2 font-display text-4xl leading-[0.9] text-cream sm:text-6xl">
                    <?php echo esc_html($stream_title_prefix); ?> <span style="-webkit-text-stroke: 0.5px #13f7bc; color: #410b49;"><?php echo esc_html($stream_title_highlight); ?></span>
                </h2>
            </div>
            <div class="flex items-center gap-3">
                <span class="hidden font-poster text-sm uppercase tracking-[0.2em] text-cream/80 sm:block">
                    <?php echo esc_html($stream_availability_text); ?>
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="space-y-3">
                <a href="<?php echo esc_url($link_spotify); ?>" data-platform="spotify" aria-expanded="false" class="platform-card group relative flex items-center justify-between rounded-2xl border-2 border-ink bg-cream px-6 py-5 text-ink transition hover:-translate-y-1 hover:-translate-x-0.5" style="box-shadow: 6px 6px 0 var(--ink)">
                    <div>
                        <p class="font-poster text-[10px] uppercase tracking-[0.25em] opacity-70"><?php echo esc_html($stream_card_label); ?></p>
                        <p class="flex items-center gap-2 font-display text-2xl leading-none">
                            <span class="text-[0.9em]" style="color: #1DB954;" aria-hidden="true"><i class="fa-brands fa-spotify"></i></span>
                            <span>Spotify</span>
                        </p>
                    </div>
                    <span class="font-poster text-2xl transition group-hover:translate-x-1">→</span>
                </a>
                <div id="player-mobile-spotify" class="platform-player-mobile overflow-hidden rounded-2xl border-2 border-ink bg-cream p-2" style="box-shadow: 6px 6px 0 var(--ink);">
                    <iframe title="Spotify player" src="https://open.spotify.com/embed/track/3rzrziofCOwRrI1r99IUbQ?utm_source=generator" width="100%" height="352" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>
                </div>
            </div>

            <div class="space-y-3">
                <a href="<?php echo esc_url($link_apple_music); ?>" data-platform="apple-music" aria-expanded="false" class="platform-card group relative flex items-center justify-between rounded-2xl border-2 border-ink bg-cream px-6 py-5 text-ink transition hover:-translate-y-1 hover:-translate-x-0.5" style="box-shadow: 6px 6px 0 var(--ink)">
                    <div>
                        <p class="font-poster text-[10px] uppercase tracking-[0.25em] opacity-70"><?php echo esc_html($stream_card_label); ?></p>
                        <p class="flex items-center gap-2 font-display text-2xl leading-none">
                            <span class="text-[0.9em]" style="color: #FC3C44;" aria-hidden="true"><i class="fa-brands fa-apple"></i></span>
                            <span>Apple Music</span>
                        </p>
                    </div>
                    <span class="font-poster text-2xl transition group-hover:translate-x-1">→</span>
                </a>
                <div id="player-mobile-apple-music" class="platform-player-mobile overflow-hidden rounded-2xl border-2 border-ink bg-cream p-2" style="box-shadow: 6px 6px 0 var(--ink);">
                    <iframe title="Apple Music player" src="https://embed.music.apple.com/fr/song/mayami-my-miami/6771742499" width="100%" height="352" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>
                </div>
            </div>

            <div class="space-y-3">
                <a href="<?php echo esc_url($link_youtube_music); ?>" data-platform="youtube-music" aria-expanded="false" class="platform-card group relative flex items-center justify-between rounded-2xl border-2 border-ink bg-cream px-6 py-5 text-ink transition hover:-translate-y-1 hover:-translate-x-0.5" style="box-shadow: 6px 6px 0 var(--ink)">
                    <div>
                        <p class="font-poster text-[10px] uppercase tracking-[0.25em] opacity-70"><?php echo esc_html($stream_card_label); ?></p>
                        <p class="flex items-center gap-2 font-display text-2xl leading-none">
                            <span class="text-[0.9em]" style="color: #FF0000;" aria-hidden="true"><i class="fa-brands fa-youtube"></i></span>
                            <span>YouTube Music</span>
                        </p>
                    </div>
                    <span class="font-poster text-2xl transition group-hover:translate-x-1">→</span>
                </a>
                <div id="player-mobile-youtube-music" class="platform-player-mobile overflow-hidden rounded-2xl border-2 border-ink bg-cream p-2" style="box-shadow: 6px 6px 0 var(--ink);">
                    <iframe title="YouTube Music player" src="https://www.youtube.com/embed/EH_QcQ92hSk" width="100%" height="352" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>
                </div>
            </div>

            <div class="space-y-3">
                <a href="<?php echo esc_url($link_deezer); ?>" data-platform="deezer" aria-expanded="false" class="platform-card group relative flex items-center justify-between rounded-2xl border-2 border-ink bg-cream px-6 py-5 text-ink transition hover:-translate-y-1 hover:-translate-x-0.5" style="box-shadow: 6px 6px 0 var(--ink)">
                    <div>
                        <p class="font-poster text-[10px] uppercase tracking-[0.25em] opacity-70"><?php echo esc_html($stream_card_label); ?></p>
                        <p class="flex items-center gap-2 font-display text-2xl leading-none">
                            <span class="text-[0.9em]" style="color: #A238FF;" aria-hidden="true"><i class="fa-brands fa-deezer"></i></span>
                            <span>Deezer</span>
                        </p>
                    </div>
                    <span class="font-poster text-2xl transition group-hover:translate-x-1">→</span>
                </a>
                <div id="player-mobile-deezer" class="platform-player-mobile overflow-hidden rounded-2xl border-2 border-ink bg-cream p-2" style="box-shadow: 6px 6px 0 var(--ink);">
                    <iframe title="Deezer player" src="https://widget.deezer.com/widget/dark/track/4034160411" width="100%" height="352" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>
                </div>
            </div>

            <div class="space-y-3">
                <a href="<?php echo esc_url($link_amazon_music); ?>" data-platform="amazon-music" aria-expanded="false" class="platform-card group relative flex items-center justify-between rounded-2xl border-2 border-ink bg-cream px-6 py-5 text-ink transition hover:-translate-y-1 hover:-translate-x-0.5" style="box-shadow: 6px 6px 0 var(--ink)">
                    <div>
                        <p class="font-poster text-[10px] uppercase tracking-[0.25em] opacity-70"><?php echo esc_html($stream_card_label); ?></p>
                        <p class="flex items-center gap-2 font-display text-2xl leading-none">
                            <span class="text-[0.9em]" style="color: #00A8E1;" aria-hidden="true"><i class="fa-brands fa-amazon"></i></span>
                            <span>Amazon Music</span>
                        </p>
                    </div>
                    <span class="font-poster text-2xl transition group-hover:translate-x-1">→</span>
                </a>
                <div id="player-mobile-amazon-music" class="platform-player-mobile rounded-2xl border-2 border-ink bg-cream p-5" style="box-shadow: 6px 6px 0 var(--ink);">
                    <p class="font-poster text-sm uppercase tracking-[0.2em] text-ink">Amazon Music</p>
                    <p class="mt-2 text-sm text-ink/75">This platform does not provide a reliable embeddable player. Open it in a new tab.</p>
                    <a href="<?php echo esc_url($link_amazon_music); ?>" target="_blank" rel="noreferrer" class="mt-4 inline-flex items-center gap-2 rounded-full border-2 border-ink bg-aqua px-4 py-2 font-poster text-xs uppercase tracking-[0.15em] text-ink">Open Amazon Music ↗</a>
                </div>
            </div>

            <div class="space-y-3">
                <a href="<?php echo esc_url($link_soundcloud); ?>" data-platform="soundcloud" aria-expanded="false" class="platform-card group relative flex items-center justify-between rounded-2xl border-2 border-ink bg-cream px-6 py-5 text-ink transition hover:-translate-y-1 hover:-translate-x-0.5" style="box-shadow: 6px 6px 0 var(--ink)">
                    <div>
                        <p class="font-poster text-[10px] uppercase tracking-[0.25em] opacity-70"><?php echo esc_html($stream_card_label); ?></p>
                        <p class="flex items-center gap-2 font-display text-2xl leading-none">
                            <span class="text-[0.9em]" style="color: #FF5500;" aria-hidden="true"><i class="fa-brands fa-soundcloud"></i></span>
                            <span>SoundCloud</span>
                        </p>
                    </div>
                    <span class="font-poster text-2xl transition group-hover:translate-x-1">→</span>
                </a>
                <div id="player-mobile-soundcloud" class="platform-player-mobile overflow-hidden rounded-2xl border-2 border-ink bg-cream p-2" style="box-shadow: 6px 6px 0 var(--ink);">
                    <iframe title="SoundCloud player" src="https://w.soundcloud.com/player/?url=https%3A%2F%2Fsoundcloud.com%2Fellenemasri&color=%23ff5500&auto_play=false&show_user=true" width="100%" height="352" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>
                </div>
            </div>
        </div>

        <!-- Desktop player (hidden on mobile, shown below grid on desktop) -->
        <div id="player-desktop-spotify" class="platform-player-desktop mt-6 overflow-hidden rounded-2xl border-2 border-ink bg-cream p-2" style="box-shadow: 6px 6px 0 var(--ink);">
            <iframe title="Spotify player" src="https://open.spotify.com/embed/track/3rzrziofCOwRrI1r99IUbQ?utm_source=generator" width="100%" height="352" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>
        </div>
        <div id="player-desktop-apple-music" class="platform-player-desktop mt-6 overflow-hidden rounded-2xl border-2 border-ink bg-cream p-2" style="box-shadow: 6px 6px 0 var(--ink);">
            <iframe title="Apple Music player" src="https://embed.music.apple.com/fr/song/mayami-my-miami/6771742499" width="100%" height="352" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>
        </div>
        <div id="player-desktop-youtube-music" class="platform-player-desktop mt-6 overflow-hidden rounded-2xl border-2 border-ink bg-cream p-2" style="box-shadow: 6px 6px 0 var(--ink);">
            <iframe title="YouTube Music player" src="https://www.youtube.com/embed/EH_QcQ92hSk" width="100%" height="352" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>
        </div>
        <div id="player-desktop-deezer" class="platform-player-desktop mt-6 overflow-hidden rounded-2xl border-2 border-ink bg-cream p-2" style="box-shadow: 6px 6px 0 var(--ink);">
            <iframe title="Deezer player" src="https://widget.deezer.com/widget/dark/track/4034160411" width="100%" height="352" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>
        </div>
        <div id="player-desktop-amazon-music" class="platform-player-desktop mt-6 rounded-2xl border-2 border-ink bg-cream p-5" style="box-shadow: 6px 6px 0 var(--ink);">
            <p class="font-poster text-sm uppercase tracking-[0.2em] text-ink">Amazon Music</p>
            <p class="mt-2 text-sm text-ink/75">This platform does not provide a reliable embeddable player. Open it in a new tab.</p>
            <a href="<?php echo esc_url($link_amazon_music); ?>" target="_blank" rel="noreferrer" class="mt-4 inline-flex items-center gap-2 rounded-full border-2 border-ink bg-aqua px-4 py-2 font-poster text-xs uppercase tracking-[0.15em] text-ink">Open Amazon Music ↗</a>
        </div>
        <div id="player-desktop-soundcloud" class="platform-player-desktop mt-6 overflow-hidden rounded-2xl border-2 border-ink bg-cream p-2" style="box-shadow: 6px 6px 0 var(--ink);">
            <iframe title="SoundCloud player" src="https://w.soundcloud.com/player/?url=https%3A%2F%2Fsoundcloud.com%2Fellenemasri&color=%23ff5500&auto_play=false&show_user=true" width="100%" height="352" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>
        </div>
    </div>
</section>
