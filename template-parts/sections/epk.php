<?php
/**
 * Template part - EPK Section
 *
 * @package Mayami
 */

$epk_payload = mayami_get_epk_front_payload();

if (!mayami_has_epk_payload_data($epk_payload)) {
    return;
}

$epk_zones = array_values(array_filter($epk_payload['zones'], static function ($zone) {
    return mayami_get_epk_zone_href($zone) !== '';
}));

$is_preview = mayami_is_epk_preview_request();
?>
<section id="epk" class="mayami-epk-section"<?php echo $is_preview ? ' data-epk-preview="1"' : ''; ?>>
    <div class="mayami-epk-shell">
        <div class="mayami-epk-header">
            <div>
                <?php if (!empty($epk_payload['kicker'])): ?>
                    <p class="mayami-epk-kicker"><?php echo esc_html($epk_payload['kicker']); ?></p>
                <?php endif; ?>
                <?php if (!empty($epk_payload['title'])): ?>
                    <h2 class="mayami-epk-title"><?php echo esc_html($epk_payload['title']); ?></h2>
                <?php endif; ?>
            </div>

            <?php if ($is_preview): ?>
                <span class="mayami-epk-preview-pill">Brouillon admin</span>
            <?php endif; ?>
        </div>

        <?php if (!empty($epk_payload['description'])): ?>
            <p class="mayami-epk-description"><?php echo esc_html($epk_payload['description']); ?></p>
        <?php endif; ?>

        <div class="mayami-epk-visual">
            <img src="<?php echo esc_url($epk_payload['imageUrl']); ?>" alt="<?php echo esc_attr($epk_payload['imageAlt']); ?>">

            <?php foreach ($epk_zones as $index => $zone): ?>
                <?php
                $href = mayami_get_epk_zone_href($zone);
                $label = !empty($zone['label']) ? $zone['label'] : sprintf('Zone EPK %d', $index + 1);
                $style = sprintf(
                    'left:%1$.4F%%;top:%2$.4F%%;width:%3$.4F%%;height:%4$.4F%%;',
                    (float) $zone['x'],
                    (float) $zone['y'],
                    (float) $zone['width'],
                    (float) $zone['height']
                );
                ?>
                <a href="<?php echo esc_url($href); ?>" class="mayami-epk-hotspot" style="<?php echo esc_attr($style); ?>" aria-label="<?php echo esc_attr($label); ?>">
                    <span class="screen-reader-text"><?php echo esc_html($label); ?></span>
                    <?php if ($is_preview): ?>
                        <span class="mayami-epk-hotspot-label"><?php echo esc_html($label); ?></span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>