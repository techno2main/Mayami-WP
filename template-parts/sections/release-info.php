<?php
/**
 * Template part - Release Info Section
 * 
 * @package Mayami
 */

$release_kicker = cmb2_get_option('mayami_landing_options', 'release_kicker') ?: '04 / Release Info';
$release_title_left = cmb2_get_option('mayami_landing_options', 'release_title_left') ?: 'The';
$release_title_highlight = cmb2_get_option('mayami_landing_options', 'release_title_highlight') ?: 'credits';
$release_rows = cmb2_get_option('mayami_landing_options', 'release_rows');

if (empty($release_rows)) {
    $release_rows = array(
        array('key' => 'Artists', 'value' => 'Richard Bona & Ellene Masri'),
        array('key' => 'Title', 'value' => 'Mayami, My Miami'),
        array('key' => 'Release date', 'value' => 'May 29th'),
        array('key' => 'Location', 'value' => 'Miami, USA'),
        array('key' => 'Video', 'value' => 'Coming soon'),
    );
}

$cover_image = cmb2_get_option('mayami_landing_options', 'release_cover_image') ?: (get_template_directory_uri() . '/assets/mayami-cover.jpg');
?>
<section id="release" class="relative bg-cream py-20 text-ink sm:py-28">
    <div class="mx-auto grid max-w-6xl grid-cols-1 gap-10 px-5 sm:px-8 md:grid-cols-[1fr_1.3fr] md:items-center">
        <div class="relative">
            <span class="tape -top-4 left-8 h-6 w-24"></span>
            <img
                src="<?php echo esc_url($cover_image); ?>"
                alt="Single cover"
                width="1024"
                height="1024"
                loading="lazy"
                class="aspect-square w-full rounded-2xl border-2 border-ink object-cover"
                style="box-shadow: 10px 10px 0 var(--magenta)"
            />
        </div>
        <div>
            <div class="mb-4 flex justify-end gap-4">
                <a href="#cta" aria-label="Section suivante" class="inline-flex items-center justify-center text-xl leading-none text-ink/70 transition hover:text-ink">↓</a>
                <a href="#video" aria-label="Section précédente" class="inline-flex items-center justify-center text-xl leading-none text-ink/70 transition hover:text-ink">↑</a>
            </div>
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="font-poster text-xs uppercase tracking-[0.3em] text-ink/60"><?php echo esc_html($release_kicker); ?></p>
                    <h2 class="mt-2 font-display text-4xl leading-[0.95] sm:text-6xl">
                        <?php echo esc_html($release_title_left); ?> <span class="text-magenta"><?php echo esc_html($release_title_highlight); ?></span>
                    </h2>
                </div>
            </div>
            <dl class="mt-8 divide-y divide-ink/15 border-y-2 border-ink">
                <?php foreach ($release_rows as $row): ?>
                    <div class="flex items-center justify-between gap-4 py-3.5">
                        <dt class="font-poster text-[11px] uppercase tracking-[0.22em] text-ink/60 sm:text-xs"><?php echo esc_html($row['key']); ?></dt>
                        <dd class="font-display text-base sm:text-xl"><?php echo esc_html($row['value']); ?></dd>
                    </div>
                <?php endforeach; ?>
            </dl>
        </div>
    </div>
</section>
