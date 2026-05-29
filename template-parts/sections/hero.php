<?php
/**
 * Template part - Hero Section
 * 
 * @package Mayami
 */

// Récupérer les valeurs des champs CMB2
$hero_top_artist = cmb2_get_option('mayami_landing_options', 'hero_top_artist') ?: 'Richard Bona & Ellen Masri';
$hero_top_cta_label = cmb2_get_option('mayami_landing_options', 'hero_top_cta_label') ?: 'Out tomorrow';
$hero_badge_text = cmb2_get_option('mayami_landing_options', 'hero_badge_text') ?: 'New Single · Out Tomorrow';
$hero_subtitle = cmb2_get_option('mayami_landing_options', 'hero_subtitle') ?: 'Mayami, My Miami';
$hero_description = cmb2_get_option('mayami_landing_options', 'hero_description') ?: 'A sun-soaked love letter to the city. Stream it, watch it, share it and follow the journey from the painted walls of Miami.';
$hero_stream_label = cmb2_get_option('mayami_landing_options', 'hero_stream_label') ?: '◉ Stream';
$hero_stream_href = cmb2_get_option('mayami_landing_options', 'hero_stream_href') ?: 'https://ffm.to/mayami';
$hero_watch_label = cmb2_get_option('mayami_landing_options', 'hero_watch_label') ?: '▶ Watch';
$hero_watch_href = cmb2_get_option('mayami_landing_options', 'hero_watch_href') ?: '#video';
?>
<style>
    #hero .hero-top-cta {
        display: none;
    }

    @media (min-width: 768px) {
        #hero .hero-top-cta {
            display: inline-flex;
        }
    }
</style>
<section id="hero" class="relative w-full overflow-hidden bg-background">
    <!-- Background image -->
    <img 
        src="<?php echo esc_url(get_template_directory_uri() . '/assets/background.jpeg'); ?>" 
        alt="" 
        width="768" 
        height="1366" 
        loading="eager" 
        class="absolute inset-0 h-full w-full -scale-x-100 object-cover opacity-32 mix-blend-normal"
        style="filter: brightness(1.18) saturate(0.92);"
    />
    <div class="absolute inset-0 grain grain-soft"></div>

    <!-- Top bar -->
    <header class="relative z-10 mx-auto flex max-w-7xl items-center justify-between px-5 pt-5 sm:px-8">
        <span class="font-poster text-sm uppercase tracking-[0.2em] text-ink">
            <?php echo esc_html($hero_top_artist); ?>
        </span>
        <a
            href="#stream"
            class="hero-top-cta rounded-full border-2 border-ink bg-cream px-4 py-1.5 text-xs font-extrabold uppercase tracking-wider text-ink shadow-[3px_3px_0_var(--ink)] transition hover:-translate-y-0.5"
        >
            <?php echo esc_html($hero_top_cta_label); ?>
        </a>
    </header>

    <div class="relative z-10 mx-auto grid max-w-7xl grid-cols-1 items-center gap-10 px-5 pb-10 pt-10 sm:px-8 sm:pb-16 sm:pt-14 md:grid-cols-[1.15fr_1fr] md:gap-14 md:pb-32 md:pt-16">
        <!-- Hero copy -->
        <div>
            <div class="mb-4 flex justify-end">
                <a href="#stream" aria-label="Section suivante" class="inline-flex items-center justify-center text-xl leading-none text-ink/80 transition hover:text-ink">↓</a>
            </div>
            <div class="mb-4 inline-flex w-fit items-center gap-2 rounded-full border-2 border-ink bg-[oklch(0.88_0.19_95)] px-3 py-1 text-[11px] font-extrabold uppercase tracking-[0.18em] text-ink shadow-[3px_3px_0_var(--ink)] wiggle">
                <span class="h-1.5 w-1.5 rounded-full bg-aqua"></span>
                <?php echo esc_html($hero_badge_text); ?>
            </div>

            <p class="font-poster text-xs uppercase tracking-[0.35em] text-ink">
                <?php echo esc_html($hero_subtitle); ?>
            </p>

            <div class="mt-4">
                <img 
                    src="<?php echo esc_url(get_template_directory_uri() . '/assets/mayami-logo.png'); ?>" 
                    alt="Mayami" 
                    width="1200" 
                    height="620" 
                    class="inline-block h-auto w-full max-w-120 select-none sm:max-w-140" 
                    draggable="false"
                />
                <h1 class="sr-only">Mayami, My Miami</h1>
            </div>

            <p class="mt-6 max-w-xl text-base font-semibold text-ink sm:text-lg">
                <?php echo esc_html($hero_description); ?>
            </p>

            <div class="mt-7 flex items-center gap-3">
                <a href="<?php echo esc_url($hero_stream_href); ?>" target="_blank" rel="noreferrer" class="btn-pop btn-magenta"><?php echo esc_html($hero_stream_label); ?></a>
                <a href="<?php echo esc_url($hero_watch_href); ?>" class="btn-pop btn-aqua"><?php echo esc_html($hero_watch_label); ?></a>
            </div>
        </div>

        <!-- Artist portrait slider -->
        <?php get_template_part('template-parts/sections/hero-slider'); ?>
    </div>
</section>
