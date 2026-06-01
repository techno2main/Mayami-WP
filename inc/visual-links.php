<?php
/**
 * Visual Links workflow helpers.
 *
 * @package Mayami
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('cmb2_render_mayami_epk_builder', 'mayami_render_epk_builder_field', 10, 5);
add_action('admin_post_mayami_publish_epk_draft', 'mayami_handle_publish_epk_draft');
add_action('admin_post_mayami_unpublish_epk', 'mayami_handle_unpublish_epk');
add_action('admin_notices', 'mayami_render_epk_admin_notice');

function mayami_get_epk_default_payload() {
    return array(
        'kicker' => 'VISUAL LINKS',
        'title' => 'Electronic Press Kit',
        'description' => '',
        'imageUrl' => '',
        'imageAlt' => '',
        'zones' => array(),
        'updatedAt' => '',
        'publishedAt' => '',
    );
}

function mayami_has_epk_payload_data($payload) {
    return is_array($payload) && !empty($payload['imageUrl']);
}

function mayami_normalize_epk_payload($payload) {
    $default_payload = mayami_get_epk_default_payload();

    if (!is_array($payload)) {
        return $default_payload;
    }

    $normalized = array(
        'kicker' => sanitize_text_field($payload['kicker'] ?? $default_payload['kicker']),
        'title' => sanitize_text_field($payload['title'] ?? $default_payload['title']),
        'description' => sanitize_textarea_field($payload['description'] ?? ''),
        'imageUrl' => esc_url_raw($payload['imageUrl'] ?? ''),
        'imageAlt' => sanitize_text_field($payload['imageAlt'] ?? ''),
        'zones' => array(),
        'updatedAt' => sanitize_text_field($payload['updatedAt'] ?? ''),
        'publishedAt' => sanitize_text_field($payload['publishedAt'] ?? ''),
    );

    $zones = $payload['zones'] ?? array();
    if (!is_array($zones)) {
        $zones = array();
    }

    foreach ($zones as $zone) {
        if (!is_array($zone)) {
            continue;
        }

        $href_type = isset($zone['hrefType']) && $zone['hrefType'] === 'anchor' ? 'anchor' : 'url';
        $href_value = trim((string) ($zone['hrefValue'] ?? ''));
        $x = max(0, min(100, (float) ($zone['x'] ?? 0)));
        $y = max(0, min(100, (float) ($zone['y'] ?? 0)));
        $width = max(0, min(100, (float) ($zone['width'] ?? 0)));
        $height = max(0, min(100, (float) ($zone['height'] ?? 0)));

        if ($width <= 0 || $height <= 0) {
            continue;
        }

        if ($href_type === 'anchor') {
            $href_value = ltrim($href_value, '#');
            $href_value = sanitize_title($href_value);
        } else {
            $href_value = esc_url_raw($href_value);
        }

        $normalized['zones'][] = array(
            'id' => sanitize_key($zone['id'] ?? uniqid('zone_', false)),
            'label' => sanitize_text_field($zone['label'] ?? ''),
            'hrefType' => $href_type,
            'hrefValue' => $href_value,
            'x' => round($x, 4),
            'y' => round($y, 4),
            'width' => round($width, 4),
            'height' => round($height, 4),
        );
    }

    return $normalized;
}

function mayami_decode_epk_payload($raw_payload) {
    if (is_array($raw_payload)) {
        return mayami_normalize_epk_payload($raw_payload);
    }

    if (!is_string($raw_payload) || trim($raw_payload) === '') {
        return array();
    }

    $decoded = json_decode($raw_payload, true);
    if (!is_array($decoded)) {
        return array();
    }

    return mayami_normalize_epk_payload($decoded);
}

function mayami_sanitize_epk_payload($value) {
    if (is_string($value)) {
        $value = wp_unslash($value);
    }

    $payload = mayami_decode_epk_payload($value);
    if (!mayami_has_epk_payload_data($payload)) {
        return '';
    }

    return wp_json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function mayami_get_saved_epk_payload($field_id) {
    return mayami_decode_epk_payload(mayami_get_landing_option($field_id, ''));
}

function mayami_is_epk_preview_request() {
    if (is_admin()) {
        return false;
    }

    if (!isset($_GET['mayami_preview']) || wp_unslash($_GET['mayami_preview']) !== 'epk') {
        return false;
    }

    if (!is_user_logged_in() || !current_user_can('manage_options')) {
        return false;
    }

    $nonce = isset($_GET['mayami_preview_nonce']) ? sanitize_text_field(wp_unslash($_GET['mayami_preview_nonce'])) : '';

    return $nonce !== '' && wp_verify_nonce($nonce, 'mayami_epk_preview');
}

function mayami_get_epk_preview_url() {
    return add_query_arg(
        array(
            'mayami_preview' => 'epk',
            'mayami_preview_nonce' => wp_create_nonce('mayami_epk_preview'),
        ),
        home_url('/')
    );
}

function mayami_get_epk_front_payload() {
    if (mayami_is_epk_preview_request()) {
        $draft_payload = mayami_get_saved_epk_payload('epk_draft_payload');
        if (mayami_has_epk_payload_data($draft_payload)) {
            return $draft_payload;
        }
    }

    return mayami_get_saved_epk_payload('epk_published_payload');
}

function mayami_get_epk_zone_href($zone) {
    if (!is_array($zone)) {
        return '';
    }

    $href_type = $zone['hrefType'] ?? 'url';
    $href_value = trim((string) ($zone['hrefValue'] ?? ''));

    if ($href_value === '') {
        return '';
    }

    if ($href_type === 'anchor') {
        return '#' . sanitize_title(ltrim($href_value, '#'));
    }

    return esc_url($href_value);
}

function mayami_format_epk_timestamp($value) {
    if (!is_string($value) || trim($value) === '') {
        return 'Non défini';
    }

    $timestamp = strtotime($value);
    if (!$timestamp) {
        return 'Non défini';
    }

    return wp_date('d/m/Y H:i', $timestamp);
}

function mayami_render_epk_builder_field($field, $escaped_value, $object_id, $object_type, $field_type_object) {
    $draft_payload = mayami_get_saved_epk_payload('epk_draft_payload');
    $published_payload = mayami_get_saved_epk_payload('epk_published_payload');
    $validation_ready = (bool) mayami_get_landing_option('epk_validation_ready', false);
    ?>
    <div
        class="mayami-epk-builder"
        data-action-endpoint="<?php echo esc_url(admin_url('admin-post.php')); ?>"
        data-preview-url="<?php echo esc_url(mayami_get_epk_preview_url()); ?>"
        data-publish-nonce="<?php echo esc_attr(wp_create_nonce('mayami_publish_epk_draft')); ?>"
        data-unpublish-nonce="<?php echo esc_attr(wp_create_nonce('mayami_unpublish_epk')); ?>"
    >
        <div class="mayami-epk-proto-shell">
            <div class="mayami-epk-proto-header">
                <div class="mayami-epk-proto-header-copy">
                    <h3>Créateur de liens sur image - Visual Links</h3>
                    <p>Dessinez des zones cliquables sur votre image puis prévisualisez et publiez la version validée sur la landing.</p>
                </div>
                <div class="mayami-epk-builder__badges">
                    <span class="mayami-epk-badge mayami-epk-badge--draft">Brouillon: <?php echo esc_html(mayami_has_epk_payload_data($draft_payload) ? 'prêt' : 'vide'); ?></span>
                    <span class="mayami-epk-badge mayami-epk-badge--published">Front: <?php echo esc_html(mayami_has_epk_payload_data($published_payload) ? 'publié' : 'hors ligne'); ?></span>
                    <span class="mayami-epk-badge mayami-epk-badge--check">Validation: <?php echo esc_html($validation_ready ? 'ok' : 'requise'); ?></span>
                </div>
            </div>

            <div class="mayami-epk-proto-main">
                <div class="mayami-epk-canvas-area">
                    <div class="mayami-epk-upload-panel">
                        <div class="mayami-epk-native-media-host"></div>
                        <button type="button" class="mayami-epk-btn mayami-epk-btn-secondary mayami-epk-clear-image">Retirer le visuel</button>
                    </div>

                    <div class="mayami-epk-canvas-empty">Sélectionnez une image dans la médiathèque pour commencer.</div>

                    <div class="mayami-epk-canvas-wrapper" hidden>
                        <img class="mayami-epk-canvas-image" src="" alt="">
                        <div class="mayami-epk-canvas-overlay"></div>
                    </div>

                    <div class="mayami-epk-info-box">
                        <strong>Mode d'emploi :</strong>
                        1. Choisissez le visuel dans la médiathèque.<br>
                        2. Cliquez-glissez pour dessiner des zones rectangulaires.<br>
                        3. Ajoutez un lien ou une ancre pour chaque zone.<br>
                        4. Enregistrez, prévisualisez, validez, puis publiez.
                    </div>

                    <div class="mayami-epk-form-grid">
                        <label>
                            <span>Kicker</span>
                            <input type="text" class="regular-text mayami-epk-input" data-epk-field="kicker" placeholder="VISUAL LINKS">
                        </label>
                        <label>
                            <span>Titre</span>
                            <input type="text" class="regular-text mayami-epk-input" data-epk-field="title" placeholder="Visual Links">
                        </label>
                        <label class="mayami-epk-form-grid__full">
                            <span>Description</span>
                            <textarea rows="3" class="large-text mayami-epk-input" data-epk-field="description" placeholder="Présentez rapidement cette section."></textarea>
                        </label>
                        <label class="mayami-epk-form-grid__full">
                            <span>Texte alternatif de l'image</span>
                            <input type="text" class="regular-text mayami-epk-input" data-epk-field="imageAlt" placeholder="Visuel cliquable de Mayami">
                        </label>
                    </div>
                </div>

                <aside class="mayami-epk-sidebar">
                    <div class="mayami-epk-stats">
                        <div class="mayami-epk-stat-box">
                            <div class="mayami-epk-stat-number mayami-epk-zone-count">0</div>
                            <div class="mayami-epk-stat-label">Zones</div>
                        </div>
                        <div class="mayami-epk-stat-box">
                            <div class="mayami-epk-stat-number mayami-epk-linked-count">0</div>
                            <div class="mayami-epk-stat-label">Liées</div>
                        </div>
                    </div>

                    <h2>Zones cliquables</h2>

                    <div class="mayami-epk-zones-list"></div>

                    <div class="mayami-epk-actions">
                        <button type="button" class="mayami-epk-btn mayami-epk-btn-secondary mayami-epk-reset-zones">Tout effacer</button>
                        <a href="<?php echo esc_url(mayami_get_epk_preview_url()); ?>" target="_blank" rel="noreferrer" class="mayami-epk-btn mayami-epk-btn-secondary mayami-epk-preview-link">Prévisualiser la landing</a>
                        <button type="button" class="mayami-epk-btn mayami-epk-btn-primary mayami-epk-publish-button">Publier Visual Links sur le front</button>
                        <button type="button" class="mayami-epk-btn mayami-epk-btn-secondary mayami-epk-unpublish-button">Retirer Visual Links du front</button>
                    </div>

                    <div class="mayami-epk-workflow-box">
                        <p><strong>Dernier brouillon :</strong> <span class="mayami-epk-draft-status"><?php echo esc_html(mayami_format_epk_timestamp($draft_payload['updatedAt'] ?? '')); ?></span></p>
                        <p><strong>Version front :</strong> <span class="mayami-epk-published-status"><?php echo esc_html(mayami_format_epk_timestamp($published_payload['publishedAt'] ?? '')); ?></span></p>
                        <div class="mayami-epk-validation-host"></div>
                        <p class="mayami-epk-workflow-note">Prévisualisation et publication utilisent la dernière version enregistrée. Sauvegardez Mayami Landing avant ces actions.</p>
                    </div>
                </aside>
            </div>
        </div>
    </div>
    <?php
}

function mayami_handle_publish_epk_draft() {
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You are not allowed to do this.', 'mayami'), 403);
    }

    check_admin_referer('mayami_publish_epk_draft');

    $options = get_option('mayami_landing_options', array());
    if (!is_array($options)) {
        $options = array();
    }

    $draft_payload = mayami_decode_epk_payload($options['epk_draft_payload'] ?? '');
    if (!mayami_has_epk_payload_data($draft_payload)) {
        mayami_redirect_epk_notice('draft-missing');
    }

    if (empty($options['epk_validation_ready'])) {
        mayami_redirect_epk_notice('validation-missing');
    }

    $draft_payload['publishedAt'] = wp_date(DATE_ATOM, current_time('timestamp'));
    $options['epk_published_payload'] = wp_json_encode($draft_payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    update_option('mayami_landing_options', $options);

    mayami_redirect_epk_notice('published');
}

function mayami_handle_unpublish_epk() {
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You are not allowed to do this.', 'mayami'), 403);
    }

    check_admin_referer('mayami_unpublish_epk');

    $options = get_option('mayami_landing_options', array());
    if (!is_array($options)) {
        $options = array();
    }

    $options['epk_published_payload'] = '';
    update_option('mayami_landing_options', $options);

    mayami_redirect_epk_notice('unpublished');
}

function mayami_redirect_epk_notice($notice) {
    $url = add_query_arg(
        array(
            'page' => 'mayami_landing_options',
            'mayami_epk_notice' => $notice,
        ),
        admin_url('admin.php')
    );

    wp_safe_redirect($url);
    exit;
}

function mayami_render_epk_admin_notice() {
    if (!is_admin()) {
        return;
    }

    if (!isset($_GET['page']) || wp_unslash($_GET['page']) !== 'mayami_landing_options') {
        return;
    }

    if (!isset($_GET['mayami_epk_notice'])) {
        return;
    }

    $notice = sanitize_key(wp_unslash($_GET['mayami_epk_notice']));
    $message = '';
    $class = 'notice-info';

    switch ($notice) {
        case 'published':
            $class = 'notice-success';
            $message = 'Le brouillon Visual Links a été publié sur le front.';
            break;
        case 'unpublished':
            $class = 'notice-warning';
            $message = 'Visual Links a été retiré du front.';
            break;
        case 'validation-missing':
            $class = 'notice-error';
            $message = 'Cochez d’abord la validation finale Visual Links puis enregistrez la page avant publication.';
            break;
        case 'draft-missing':
            $class = 'notice-error';
            $message = 'Aucun brouillon Visual Links valide à publier.';
            break;
    }

    if ($message === '') {
        return;
    }

    printf('<div class="notice %1$s is-dismissible"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
}
