<?php
/**
 * Template part - Social Section
 * 
 * @package Mayami
 */

$social_kicker = cmb2_get_option('mayami_landing_options', 'social_kicker') ?: '02 / Follow';
$social_title_left = cmb2_get_option('mayami_landing_options', 'social_title_left') ?: 'Join the';
$social_title_right = cmb2_get_option('mayami_landing_options', 'social_title_right') ?: 'journey';
$social_description = cmb2_get_option('mayami_landing_options', 'social_description') ?: 'Snippets, behind-the-scenes drop into the daily Miami diary.';

$link_tiktok = cmb2_get_option('mayami_landing_options', 'link_tiktok') ?: 'https://www.tiktok.com/@ellenemasri';
$link_instagram = cmb2_get_option('mayami_landing_options', 'link_instagram') ?: 'https://www.instagram.com/ellenemasri/';
$link_youtube_video = cmb2_get_option('mayami_landing_options', 'link_youtube_video') ?: 'https://www.youtube.com/watch?v=WiB_UoexqVo&pp=0gcJCQoLAYcqIYzv';
?>
<section id="social" class="relative overflow-hidden bg-magenta py-20 text-ink sm:py-28">
    <div class="relative mx-auto max-w-6xl px-5 sm:px-8">
        <div class="mb-4 flex justify-end gap-4">
            <a href="#video" aria-label="Section suivante" class="inline-flex items-center justify-center text-xl leading-none text-ink/70 transition hover:text-ink">↓</a>
            <a href="#stream" aria-label="Section précédente" class="inline-flex items-center justify-center text-xl leading-none text-ink/70 transition hover:text-ink">↑</a>
        </div>
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="font-poster text-xs uppercase tracking-[0.3em] text-ink"><?php echo esc_html($social_kicker); ?></p>
                <h2 class="mt-2 font-display text-5xl leading-[0.9] sm:text-7xl">
                    <span class="text-stack-magenta"><?php echo esc_html($social_title_left); ?> </span>
                    <span class="text-stack-blue"><?php echo esc_html($social_title_right); ?></span>
                </h2>
            </div>
        </div>
        <p class="mt-4 max-w-xl text-lg text-ink/90">
            <?php echo esc_html($social_description); ?>
        </p>

        <div class="mt-10 grid grid-cols-1 gap-5 md:grid-cols-3">
            <a href="<?php echo esc_url($link_tiktok); ?>" target="_blank" rel="noreferrer" class="group relative overflow-hidden rounded-2xl border-2 border-cream bg-ink p-5 text-cream transition hover:-translate-y-1" style="box-shadow: 8px 8px 0 var(--magenta)">
                <p class="font-poster text-[11px] uppercase tracking-[0.3em] opacity-80">Primary</p>
                <p class="flex items-center gap-3 font-display text-3xl sm:text-4xl"><i class="fa-brands fa-tiktok text-2xl sm:text-3xl" aria-hidden="true"></i><span>TikTok</span></p>
                <p class="mt-2 text-sm opacity-90">Catch the snippet trending right now.</p>
                <span class="mt-4 inline-flex items-center gap-2 font-poster uppercase tracking-wider">
                    Follow @ellenemasri <span class="transition group-hover:translate-x-1">→</span>
                </span>
            </a>
            <a href="<?php echo esc_url($link_instagram); ?>" target="_blank" rel="noreferrer" class="group relative overflow-hidden rounded-2xl border-2 border-cream bg-aqua p-5 text-ink transition hover:-translate-y-1" style="box-shadow: 8px 8px 0 var(--aqua)">
                <p class="font-poster text-[11px] uppercase tracking-[0.3em] opacity-70">Primary</p>
                <p class="flex items-center gap-3 font-display text-3xl sm:text-4xl"><i class="fa-brands fa-instagram text-2xl sm:text-3xl" aria-hidden="true"></i><span>Instagram</span></p>
                <p class="mt-2 text-sm opacity-80">Daily Miami diary, drops & exclusives.</p>
                <span class="mt-4 inline-flex items-center gap-2 font-poster uppercase tracking-wider">
                    Follow @ellenemasri <span class="transition group-hover:translate-x-1">→</span>
                </span>
            </a>
            <a href="<?php echo esc_url($link_youtube_video); ?>" target="_blank" rel="noreferrer" class="group relative overflow-hidden rounded-2xl border-2 border-cream bg-background p-5 text-ink transition hover:-translate-y-1" style="box-shadow: 8px 8px 0 var(--cream)">
                <p class="font-poster text-[11px] uppercase tracking-[0.3em] opacity-70">Watch</p>
                <p class="flex items-center gap-3 font-display text-3xl sm:text-4xl"><i class="fa-brands fa-youtube text-2xl sm:text-3xl" aria-hidden="true"></i><span>YouTube</span></p>
                <p class="mt-2 text-sm opacity-80">Official video & visualizers.</p>
                <span class="mt-4 inline-flex items-center gap-2 font-poster uppercase tracking-wider">
                    Subscribe @ellenemasri <span class="transition group-hover:translate-x-1">→</span>
                </span>
            </a>
        </div>
    </div>
</section>
