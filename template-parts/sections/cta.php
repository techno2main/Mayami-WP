<?php
/**
 * Template part - CTA Section
 * 
 * @package Mayami
 */

$cta_kicker = cmb2_get_option('mayami_landing_options', 'cta_kicker') ?: '05 / Don\'t sleep on it';
$cta_title_left = cmb2_get_option('mayami_landing_options', 'cta_title_left') ?: 'Press';
$cta_title_right = cmb2_get_option('mayami_landing_options', 'cta_title_right') ?: 'play.';
$cta_description = cmb2_get_option('mayami_landing_options', 'cta_description') ?: 'Stream the single. Watch the video. Tag and ride the wave.';
$cta_hashtag = cmb2_get_option('mayami_landing_options', 'cta_hashtag') ?: '#MayamiMyMiami';

$link_spotify = cmb2_get_option('mayami_landing_options', 'link_spotify') ?: 'https://open.spotify.com/intl-fr/track/3rzrziofCOwRrI1r99IUbQ?si=a2cd3f4cbe364a94';
$link_tiktok = cmb2_get_option('mayami_landing_options', 'link_tiktok') ?: 'https://www.tiktok.com/@ellenemasri';
$link_instagram = cmb2_get_option('mayami_landing_options', 'link_instagram') ?: 'https://www.instagram.com/ellenemasri/';

$texture_image = get_template_directory_uri() . '/assets/mayami-texture.jpg';
?>
<section id="cta" class="relative overflow-hidden bg-[oklch(0.68_0.17_182)] py-24 text-ink sm:py-32">
    <img
        src="<?php echo esc_url($texture_image); ?>"
        alt=""
        width="1920"
        height="1280"
        loading="lazy"
        class="absolute inset-0 h-full w-full object-cover opacity-20 mix-blend-screen"
    />
    <div class="relative mx-auto max-w-5xl px-5 sm:px-8">
        <div class="mb-4 flex justify-end gap-4">
            <a href="#footer" aria-label="Section suivante" class="inline-flex items-center justify-center text-xl leading-none text-ink/70 transition hover:text-ink">↓</a>
            <a href="#release" aria-label="Section précédente" class="inline-flex items-center justify-center text-xl leading-none text-ink/70 transition hover:text-ink">↑</a>
        </div>
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="font-poster text-xs uppercase tracking-[0.3em] text-ink/80"><?php echo esc_html($cta_kicker); ?></p>
                <h2 class="mt-3 font-display text-6xl leading-[0.85] sm:text-[140px]">
                    <span class="text-ink"><?php echo esc_html($cta_title_left); ?> </span>
                    <span class="text-ink"><?php echo esc_html($cta_title_right); ?></span>
                </h2>
            </div>
        </div>
        <p class="mt-6 max-w-xl text-lg text-ink/85">
            <?php echo esc_html($cta_description); ?> <span class="font-bold text-ink"><?php echo esc_html($cta_hashtag); ?></span>
        </p>
        <div class="mt-10 flex flex-wrap gap-3">
            <a href="<?php echo esc_url($link_spotify); ?>" target="_blank" rel="noreferrer" class="btn-pop btn-magenta">Stream the Single</a>
            <a href="#video" class="btn-pop btn-aqua">Watch the Video</a>
            <a href="<?php echo esc_url($link_tiktok); ?>" target="_blank" rel="noreferrer" class="btn-pop" style="background: linear-gradient(135deg, #111318 0%, #1f2230 65%, #2b1430 100%); color: var(--cream) !important;">TikTok</a>
            <a href="<?php echo esc_url($link_instagram); ?>" target="_blank" rel="noreferrer" class="btn-pop" style="background: linear-gradient(135deg, #f58529 0%, #dd2a7b 48%, #8134af 74%, #515bd4 100%); color: var(--cream) !important;">Instagram</a>
        </div>
    </div>
</section>
