<?php
/**
 * Mayami Theme Functions
 * 
 * @package Mayami
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Include CMB2 Configuration
require_once get_template_directory() . '/inc/cmb2-config.php';
require_once get_template_directory() . '/inc/epk.php';

/**
 * Get a landing option value from the active key, with legacy key compatibility.
 *
 * This avoids admin/front mismatches when some environments still store data
 * under the previous CMB2 option key.
 *
 * @param string $field_id Option field id.
 * @param mixed  $default  Default value if field is not found.
 * @return mixed
 */
function mayami_get_landing_option($field_id, $default = '') {
    $primary_options = get_option('mayami_landing_options', array());
    if (is_array($primary_options) && array_key_exists($field_id, $primary_options)) {
        return $primary_options[$field_id];
    }

    $legacy_options = get_option('mayami_options', array());
    if (is_array($legacy_options) && array_key_exists($field_id, $legacy_options)) {
        return $legacy_options[$field_id];
    }

    return $default;
}

/**
 * Raise the upload limit reported by WordPress media screens.
 *
 * This affects the limit shown in the admin UI and the size WordPress
 * uses when checking uploads, while still allowing the server's PHP limits
 * to act as the final fallback.
 */
function mayami_upload_size_limit($size) {
    $desired_limit = 128 * MB_IN_BYTES;
    return max((int) $size, $desired_limit);
}
add_filter('upload_size_limit', 'mayami_upload_size_limit');

/**
 * Theme setup
 */
function mayami_theme_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    
    // Disable Gutenberg editor (not needed for this landing page)
    add_filter('use_block_editor_for_post', '__return_false');
    
    // Disable WordPress emoji scripts (interferes with Unicode icons)
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
}
add_action('after_setup_theme', 'mayami_theme_setup');

/**
 * Output the theme favicon on all contexts.
 */
function mayami_output_theme_favicon() {
    $favicon_svg_path = get_template_directory() . '/assets/favicon.svg';
    $favicon_png_32_path = get_template_directory() . '/assets/favicon-32.png';
    $favicon_png_180_path = get_template_directory() . '/assets/favicon-180.png';
    $favicon_svg_url = get_template_directory_uri() . '/assets/favicon.svg';
    $favicon_png_32_url = get_template_directory_uri() . '/assets/favicon-32.png';
    $favicon_png_180_url = get_template_directory_uri() . '/assets/favicon-180.png';

    if (file_exists($favicon_svg_path)) {
        $favicon_svg_url .= '?v=' . filemtime($favicon_svg_path);
    }

    if (file_exists($favicon_png_32_path)) {
        $favicon_png_32_url .= '?v=' . filemtime($favicon_png_32_path);
    }

    if (file_exists($favicon_png_180_path)) {
        $favicon_png_180_url .= '?v=' . filemtime($favicon_png_180_path);
    }

    echo '<link rel="icon" type="image/svg+xml" href="' . esc_url($favicon_svg_url) . '" />' . "\n";
    echo '<link rel="icon" type="image/png" sizes="32x32" href="' . esc_url($favicon_png_32_url) . '" />' . "\n";
    echo '<link rel="shortcut icon" href="' . esc_url($favicon_png_32_url) . '" />' . "\n";
    echo '<link rel="apple-touch-icon" sizes="180x180" href="' . esc_url($favicon_png_180_url) . '" />' . "\n";
}
add_action('wp_head', 'mayami_output_theme_favicon', 1);
add_action('admin_head', 'mayami_output_theme_favicon', 1);
add_action('login_head', 'mayami_output_theme_favicon', 1);

/**
 * Enqueue scripts and styles
 */
function mayami_enqueue_assets() {
    // Font Awesome 6
    wp_enqueue_style(
        'font-awesome-6',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
        [],
        '6.5.1'
    );
    
    // Compiled Tailwind CSS
    wp_enqueue_style(
        'mayami-tailwind', 
        get_template_directory_uri() . '/style-compiled.css', 
        [], 
        '1.0.0'
    );

    $epk_css_path = get_template_directory() . '/assets/epk.css';
    wp_enqueue_style(
        'mayami-epk',
        get_template_directory_uri() . '/assets/epk.css',
        array('mayami-tailwind'),
        file_exists($epk_css_path) ? (string) filemtime($epk_css_path) : '1.0.0'
    );

    wp_add_inline_style(
        'mayami-tailwind',
        'img, video, iframe { -webkit-user-drag: none; -webkit-touch-callout: none; user-select: none; }'
    );
    
    $stream_player_js_path = get_template_directory() . '/assets/stream-player.js';
    $content_protection_js_path = get_template_directory() . '/assets/content-protection.js';

    // Stream platform player JS
    wp_enqueue_script(
        'mayami-stream-player',
        get_template_directory_uri() . '/assets/stream-player.js',
        [],
        file_exists($stream_player_js_path) ? (string) filemtime($stream_player_js_path) : '1.0.0',
        true
    );

    // Best-effort media protection script
    wp_enqueue_script(
        'mayami-content-protection',
        get_template_directory_uri() . '/assets/content-protection.js',
        [],
        file_exists($content_protection_js_path) ? (string) filemtime($content_protection_js_path) : '1.0.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'mayami_enqueue_assets');

/**
 * Enqueue admin assets for Mayami Landing page
 */
function mayami_enqueue_admin_assets($hook) {
    $is_landing_page = ('toplevel_page_mayami_landing_options' === $hook);
    $is_visual_links_page = (strpos((string) $hook, 'mayami_epk') !== false);

    if (!$is_landing_page && !$is_visual_links_page) {
        return;
    }

    // Required by the Visual Links Builder (selection from WP media library).
    wp_enqueue_media();

    // Keep existing landing admin assets behavior unchanged.
    if (!$is_landing_page) {
        return;
    }

    $admin_css_path = get_template_directory() . '/assets/admin-nav.css';
    $admin_js_path = get_template_directory() . '/assets/admin-nav.js';
    $visual_links_admin_css_path = get_template_directory() . '/assets/admin-visual-links-builder.css';
    $visual_links_admin_js_path = get_template_directory() . '/assets/admin-visual-links-builder.js';

    // Admin navigation CSS
    wp_enqueue_style(
        'mayami-admin-nav',
        get_template_directory_uri() . '/assets/admin-nav.css',
        [],
        file_exists($admin_css_path) ? (string) filemtime($admin_css_path) : '1.0.0'
    );
    
    // Admin navigation JS
    wp_enqueue_script(
        'mayami-admin-nav',
        get_template_directory_uri() . '/assets/admin-nav.js',
        [],
        file_exists($admin_js_path) ? (string) filemtime($admin_js_path) : '1.0.0',
        true
    );

    wp_enqueue_style(
        'mayami-admin-visual-links-builder',
        get_template_directory_uri() . '/assets/admin-visual-links-builder.css',
        array('mayami-admin-nav'),
        file_exists($visual_links_admin_css_path) ? (string) filemtime($visual_links_admin_css_path) : '1.0.0'
    );

    wp_enqueue_script(
        'mayami-admin-visual-links-builder',
        get_template_directory_uri() . '/assets/admin-visual-links-builder.js',
        array('jquery', 'media-editor', 'media-views', 'wp-util'),
        file_exists($visual_links_admin_js_path) ? (string) filemtime($visual_links_admin_js_path) : '1.0.0',
        true
    );
}
add_action('admin_enqueue_scripts', 'mayami_enqueue_admin_assets');

/**
 * Hide default WordPress footer text on Mayami settings page.
 */
function mayami_hide_wp_footer_text_on_landing($text) {
    $screen = get_current_screen();
    if ($screen && (
        $screen->id === 'toplevel_page_mayami_landing_options' ||
        strpos($screen->id, 'mayami_epk') !== false
    )) {
        return '';
    }

    return $text;
}
add_filter('admin_footer_text', 'mayami_hide_wp_footer_text_on_landing', 20);

/**
 * Add a prominent "Modifier les détails" button in the media modal for client accounts.
 * The WP media modal has no visible save button — this makes it obvious.
 */
function mayami_media_modal_edit_button() {
    $screen = get_current_screen();
    if (!$screen || $screen->base !== 'upload') {
        return;
    }
    $current_user = wp_get_current_user();
    if ($current_user && $current_user->user_login === 'admin-my') {
        return;
    }
    ?>
    <script>
    (function() {
        function injectEditButton() {
            var sidebar = document.querySelector('.attachment-details .details');
            if (!sidebar || sidebar.querySelector('.mayami-edit-btn')) return;

            var editLink = sidebar.querySelector('a.edit-attachment');
            if (!editLink) return;

            var btn = document.createElement('a');
            btn.href = editLink.href;
            btn.className = 'button button-primary mayami-edit-btn';
            btn.textContent = '💾 Modifier / Enregistrer';
            btn.style.cssText = 'display:block;text-align:center;margin:12px 0 4px;width:100%;box-sizing:border-box;';
            sidebar.insertBefore(btn, editLink);
        }

        var observer = new MutationObserver(function() {
            injectEditButton();
        });
        observer.observe(document.body, { childList: true, subtree: true });
    })();
    </script>
    <?php
}
add_action('admin_footer', 'mayami_media_modal_edit_button');

/**
 * Simplify WP admin menu for client accounts.
 * Keep full admin menu for the technical owner account.
 */
function mayami_limit_admin_menu_for_client() {
    if (!is_admin()) {
        return;
    }

    if (!current_user_can('manage_options')) {
        return;
    }

    $current_user = wp_get_current_user();
    if (!$current_user || empty($current_user->user_login)) {
        return;
    }

    // Keep full menu for owner account.
    if ($current_user->user_login === 'admin-my') {
        return;
    }

    // Hide unrelated content menus for client-facing admin.
    remove_menu_page('index.php');               // Tableau de bord
    remove_menu_page('edit.php');                 // Articles
    remove_menu_page('edit-comments.php');        // Commentaires
    remove_menu_page('edit.php?post_type=page'); // Pages
    remove_menu_page('themes.php');              // Apparence (thèmes, personnalisation)
    remove_menu_page('plugins.php');             // Extensions
    remove_menu_page('users.php');               // Utilisateurs
    remove_menu_page('tools.php');               // Outils
    remove_menu_page('options-general.php');     // Réglages
}
add_action('admin_menu', 'mayami_limit_admin_menu_for_client', 999);

/**
 * Redirect client accounts to Mayami Landing instead of the dashboard after login.
 */
function mayami_client_login_redirect($redirect_to, $requested_redirect_to, $user) {
    if (is_wp_error($user) || empty($user->user_login)) {
        return $redirect_to;
    }

    if ($user->user_login === 'admin-my') {
        return $redirect_to;
    }

    return admin_url('admin.php?page=mayami_landing_options');
}
add_filter('login_redirect', 'mayami_client_login_redirect', 10, 3);

/**
 * Keep admin bar actions coherent with the reduced client menu.
 */
function mayami_limit_admin_bar_for_client($wp_admin_bar) {
    if (!is_admin_bar_showing()) {
        return;
    }

    if (!current_user_can('manage_options')) {
        return;
    }

    $current_user = wp_get_current_user();
    if (!$current_user || empty($current_user->user_login)) {
        return;
    }

    if ($current_user->user_login === 'admin-my') {
        return;
    }

    // Keep only media creation for client admins.
    // Remove the default "+ New / Créer" parent and its children,
    // then add a single direct shortcut to media upload.
    $wp_admin_bar->remove_node('new-content');
    $wp_admin_bar->remove_node('new-post');
    $wp_admin_bar->remove_node('new-page');
    $wp_admin_bar->remove_node('new-user');
    $wp_admin_bar->remove_node('new-media');

    $wp_admin_bar->add_node([
        'id'    => 'mayami-new-media',
        'title' => 'Ajouter un media',
        'href'  => admin_url('media-new.php'),
        'meta'  => [
            'title' => 'Ajouter un media',
        ],
    ]);

    // Hide comments bubble since comments menu is also hidden.
    $wp_admin_bar->remove_node('comments');

    // Hide Customizer shortcut for client admins.
    $wp_admin_bar->remove_node('customize');
}
add_action('admin_bar_menu', 'mayami_limit_admin_bar_for_client', 999);

/**
 * Redirect the front-end admin bar "Edit" link to Mayami Landing settings.
 */
function mayami_redirect_admin_bar_edit_to_landing($wp_admin_bar) {
    if (!is_admin_bar_showing() || is_admin()) {
        return;
    }

    if (!current_user_can('manage_options') || !is_front_page()) {
        return;
    }

    $current_user = wp_get_current_user();
    if (!$current_user || empty($current_user->user_login)) {
        return;
    }

    if ($current_user->user_login === 'admin-my') {
        return;
    }

    $edit_node = $wp_admin_bar->get_node('edit');
    if (!$edit_node) {
        return;
    }

    $edit_node->href = admin_url('admin.php?page=mayami_landing_options');
    $wp_admin_bar->add_node($edit_node);
}
add_action('admin_bar_menu', 'mayami_redirect_admin_bar_edit_to_landing', 1001);

/**
 * Register an independent Visual Links Builder top-level menu.
 */
function mayami_register_epk_html_menu() {
    add_menu_page(
        'Visual Links Builder',
        'Visual Links Builder',
        'manage_options',
        'mayami_epk_html_builder',
        'mayami_render_epk_html_builder_page',
        'dashicons-format-image',
        31
    );

    add_submenu_page(
        'mayami_epk_html_builder',
        'Nouveau visuel',
        'Nouveau visuel',
        'manage_options',
        'mayami_epk_html_builder_new',
        'mayami_render_epk_new_submenu_page'
    );

    add_submenu_page(
        'mayami_epk_html_builder',
        'Liste des visuels',
        'Liste des visuels',
        'manage_options',
        'mayami_epk_drafts',
        'mayami_render_epk_drafts_page'
    );
}
add_action('admin_menu', 'mayami_register_epk_html_menu', 20);

/**
 * Remove duplicate submenu generated automatically for top-level menu.
 */
function mayami_remove_epk_duplicate_submenu() {
    remove_submenu_page('mayami_epk_html_builder', 'mayami_epk_html_builder');
}
add_action('admin_menu', 'mayami_remove_epk_duplicate_submenu', 999);

/**
 * Render the "Nouveau visuel" submenu.
 */
function mayami_render_epk_new_submenu_page() {
    if (isset($_GET['draft_id'])) {
        unset($_GET['draft_id']);
    }

    mayami_render_epk_html_builder_page();
}

/**
 * Handle draft deletion from admin list.
 */
function mayami_handle_delete_epk_draft() {
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You are not allowed to do this.', 'mayami'), 403);
    }

    check_admin_referer('mayami_delete_epk_draft');

    $draft_id = isset($_POST['draft_id']) ? sanitize_text_field(wp_unslash($_POST['draft_id'])) : '';
    if ($draft_id !== '') {
        $store = mayami_get_epk_drafts_store();
        if (isset($store[$draft_id])) {
            unset($store[$draft_id]);
            mayami_update_epk_drafts_store($store);
        }
    }

    wp_safe_redirect(admin_url('admin.php?page=mayami_epk_drafts'));
    exit;
}
add_action('admin_post_mayami_delete_epk_draft', 'mayami_handle_delete_epk_draft');

/**
 * Return the full EPK drafts store from options.
 *
 * @return array<string, array<string, mixed>>
 */
function mayami_get_epk_drafts_store() {
    $store = get_option('mayami_epk_drafts_store', array());
    return is_array($store) ? $store : array();
}

/**
 * Persist the EPK drafts store in options.
 *
 * @param array<string, array<string, mixed>> $store Draft store.
 * @return bool
 */
function mayami_update_epk_drafts_store($store) {
    if (!is_array($store)) {
        $store = array();
    }

    return update_option('mayami_epk_drafts_store', $store, false);
}

/**
 * Backward compatibility for legacy draft submenu slugs.
 *
 * Older links may still point to page=mayami_epk_draft_<hash>.
 * We map them back to the real draft_id and redirect to the current builder URL.
 */
function mayami_epk_handle_legacy_draft_page_slugs() {
    if (!is_admin() || !current_user_can('manage_options')) {
        return;
    }

    $page = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';
    if ($page === '' || strpos($page, 'mayami_epk_draft_') !== 0) {
        return;
    }

    $store = mayami_get_epk_drafts_store();
    foreach ($store as $draft) {
        $draft_id = isset($draft['id']) ? (string) $draft['id'] : '';
        if ($draft_id === '') {
            continue;
        }

        $legacy_slug = 'mayami_epk_draft_' . substr(md5($draft_id), 0, 12);
        if ($legacy_slug === $page) {
            wp_safe_redirect(admin_url('admin.php?page=mayami_epk_html_builder&draft_id=' . rawurlencode($draft_id)));
            exit;
        }
    }

    wp_safe_redirect(admin_url('admin.php?page=mayami_epk_drafts'));
    exit;
}
add_action('admin_init', 'mayami_epk_handle_legacy_draft_page_slugs');

/**
 * Sanitize and normalize a payload coming from the HTML builder.
 *
 * @param array<string, mixed> $payload Raw payload.
 * @return array<string, mixed>
 */
function mayami_sanitize_epk_html_payload($payload) {
    if (!is_array($payload)) {
        return array(
            'imageUrl' => '',
            'zones' => array(),
        );
    }

    $image_url = '';
    if (!empty($payload['imageUrl'])) {
        $raw_image_url = trim((string) $payload['imageUrl']);

        // Keep data URLs generated by the local HTML builder so draft reopening
        // can restore the exact visual without relying on browser local storage.
        if (stripos($raw_image_url, 'data:image/') === 0) {
            $normalized_data_url = preg_replace('/\s+/', '', $raw_image_url);
            if (is_string($normalized_data_url) && strlen($normalized_data_url) <= (10 * MB_IN_BYTES)) {
                $image_url = $normalized_data_url;
            }
        } else {
            $image_url = esc_url_raw($raw_image_url);
        }
    }

    $zones = array();
    if (!empty($payload['zones']) && is_array($payload['zones'])) {
        foreach ($payload['zones'] as $zone) {
            if (!is_array($zone)) {
                continue;
            }

            $x = isset($zone['x']) ? (float) $zone['x'] : 0;
            $y = isset($zone['y']) ? (float) $zone['y'] : 0;
            $width = isset($zone['width']) ? (float) $zone['width'] : 0;
            $height = isset($zone['height']) ? (float) $zone['height'] : 0;

            if ($width <= 0 || $height <= 0) {
                continue;
            }

            $href_type = (isset($zone['hrefType']) && $zone['hrefType'] === 'anchor') ? 'anchor' : 'url';
            $href_value = isset($zone['hrefValue']) ? trim((string) $zone['hrefValue']) : '';
            if ($href_type === 'url') {
                $href_value = esc_url_raw($href_value);
            } else {
                $href_value = sanitize_text_field($href_value);
            }

            $zones[] = array(
                'id' => sanitize_text_field((string) ($zone['id'] ?? wp_generate_uuid4())),
                'x' => max(0, $x),
                'y' => max(0, $y),
                'width' => max(0, $width),
                'height' => max(0, $height),
                'hrefType' => $href_type,
                'hrefValue' => $href_value,
            );
        }
    }

    $canvas_width = isset($payload['canvasWidth']) ? (float) $payload['canvasWidth'] : 0;
    $canvas_height = isset($payload['canvasHeight']) ? (float) $payload['canvasHeight'] : 0;

    return array(
        'imageUrl' => $image_url,
        'canvasWidth' => max(0, $canvas_width),
        'canvasHeight' => max(0, $canvas_height),
        'zones' => $zones,
    );
}

/**
 * AJAX: save a draft in DB.
 */
function mayami_ajax_save_epk_draft() {
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Accès refusé.'), 403);
    }

    check_ajax_referer('mayami_epk_draft', 'nonce');

    $draft_name = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
    if ($draft_name === '') {
        wp_send_json_error(array('message' => 'Veuillez saisir un nom de visuel.'), 400);
    }

    $payload_raw = isset($_POST['payload']) ? wp_unslash($_POST['payload']) : '';
    $payload = json_decode((string) $payload_raw, true);
    if (!is_array($payload)) {
        wp_send_json_error(array('message' => 'Payload visuel invalide.'), 400);
    }

    $payload = mayami_sanitize_epk_html_payload($payload);
    $store = mayami_get_epk_drafts_store();

    $draft_id = isset($_POST['draft_id']) ? sanitize_text_field(wp_unslash($_POST['draft_id'])) : '';
    if ($draft_id === '') {
        $draft_id = wp_generate_uuid4();
    }

    $store[$draft_id] = array(
        'id' => $draft_id,
        'name' => $draft_name,
        'payload' => $payload,
        'updated_at' => current_time('mysql'),
    );

    mayami_update_epk_drafts_store($store);

    wp_send_json_success(array(
        'id' => $draft_id,
        'name' => $draft_name,
        'updatedAt' => $store[$draft_id]['updated_at'],
    ));
}
add_action('wp_ajax_mayami_save_epk_draft', 'mayami_ajax_save_epk_draft');

/**
 * AJAX: load one draft from DB.
 */
function mayami_ajax_get_epk_draft() {
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Accès refusé.'), 403);
    }

    check_ajax_referer('mayami_epk_draft', 'nonce');

    $draft_id = isset($_GET['draft_id']) ? sanitize_text_field(wp_unslash($_GET['draft_id'])) : '';
    if ($draft_id === '') {
        wp_send_json_error(array('message' => 'Draft introuvable.'), 400);
    }

    $store = mayami_get_epk_drafts_store();
    if (empty($store[$draft_id]) || !is_array($store[$draft_id])) {
        wp_send_json_error(array('message' => 'Visuel non trouvé.'), 404);
    }

    $draft = $store[$draft_id];
    wp_send_json_success(array(
        'id'              => $draft['id'],
        'name'            => $draft['name'],
        'payload'         => $draft['payload'],
        'updatedAt'       => $draft['updated_at'],
        'export_url'      => isset($draft['export_url'])      ? (string) $draft['export_url']      : '',
        'export_filename' => isset($draft['export_filename']) ? (string) $draft['export_filename'] : '',
    ));
}
add_action('wp_ajax_mayami_get_epk_draft', 'mayami_ajax_get_epk_draft');

/**
 * Resolve the required export directory for Visual Links Builder.
 *
 * @return string|WP_Error
 */
function mayami_get_visual_links_export_dir() {
    $export_dir = trailingslashit(get_template_directory()) . 'visual-link-builder/exports-html';
    if (!is_dir($export_dir) && !wp_mkdir_p($export_dir)) {
        return new WP_Error(
            'export_dir_create_failed',
            'Impossible de créer le dossier d\'export requis: visual-link-builder/exports-html. Créez-le manuellement via FTP puis mettez les droits en écriture (755/775).'
        );
    }

    if (!is_writable($export_dir)) {
        return new WP_Error(
            'export_dir_not_writable',
            'Le dossier d\'export requis n\'est pas accessible en écriture: visual-link-builder/exports-html. Vérifiez les permissions (755/775) et le propriétaire.'
        );
    }

    return $export_dir;
}

/**
 * Resolve export target (base folder or safe subfolder) for Visual Links Builder.
 *
 * @param string $requested_subdir Optional subfolder relative to exports-html.
 * @return array|WP_Error
 */
function mayami_get_visual_links_export_target($requested_subdir = '') {
    $base_dir = mayami_get_visual_links_export_dir();
    if (is_wp_error($base_dir)) {
        return $base_dir;
    }

    $base_url = trailingslashit(get_template_directory_uri()) . 'visual-link-builder/exports-html';
    $raw_subdir = trim(str_replace('\\', '/', (string) $requested_subdir), "/ \t\n\r\0\x0B");

    if ($raw_subdir === '') {
        return array(
            'dir' => $base_dir,
            'url' => $base_url,
            'subdir' => '',
        );
    }

    $segments = array_values(array_filter(explode('/', $raw_subdir), static function($segment) {
        return $segment !== '';
    }));

    $safe_segments = array();
    foreach ($segments as $segment) {
        $safe = sanitize_file_name(remove_accents((string) $segment));
        $safe = trim((string) $safe, " .-_\t\n\r\0\x0B");
        if ($safe !== '') {
            $safe_segments[] = $safe;
        }
    }

    if (empty($safe_segments)) {
        return new WP_Error('invalid_export_subdir', 'Sous-dossier d\'export invalide.');
    }

    $safe_subdir = implode('/', $safe_segments);
    $target_dir = trailingslashit($base_dir) . $safe_subdir;

    if (!is_dir($target_dir) && !wp_mkdir_p($target_dir)) {
        return new WP_Error('export_subdir_create_failed', 'Impossible de créer le sous-dossier d\'export: ' . $safe_subdir . '.');
    }

    if (!is_writable($target_dir)) {
        return new WP_Error('export_subdir_not_writable', 'Le sous-dossier d\'export n\'est pas accessible en écriture: ' . $safe_subdir . '.');
    }

    $encoded_segments = array_map('rawurlencode', $safe_segments);
    $target_url = trailingslashit($base_url) . implode('/', $encoded_segments);

    return array(
        'dir' => $target_dir,
        'url' => $target_url,
        'subdir' => $safe_subdir,
    );
}

/**
 * Build a normalized export subdir for one visual and one template bucket.
 *
 * Result format: <visual-slug>/Template-Email or <visual-slug>/Template-HTML
 *
 * @param string $draft_name   Visual name.
 * @param string $export_bucket template-email|template-html.
 * @return string
 */
function mayami_build_visual_links_export_subdir($draft_name = '', $export_bucket = '') {
    $safe_name = sanitize_file_name(remove_accents((string) $draft_name));
    $safe_name = trim((string) $safe_name, " .-_\t\n\r\0\x0B");
    if ($safe_name === '') {
        $safe_name = 'visual-links';
    }

    $bucket_key = sanitize_key((string) $export_bucket);
    if ($bucket_key === 'template-email') {
        return $safe_name . '/Template-Email';
    }
    if ($bucket_key === 'template-html') {
        return $safe_name . '/Template-HTML';
    }

    return $safe_name;
}

/**
 * AJAX: upload one generated image slice for email templates.
 */
function mayami_ajax_upload_visual_links_slice() {
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Accès refusé.'), 403);
    }

    check_ajax_referer('mayami_epk_draft', 'nonce');

    if (empty($_FILES['slice_file']) || !is_array($_FILES['slice_file'])) {
        wp_send_json_error(array('message' => 'Fichier slice manquant.'), 400);
    }

    $file = $_FILES['slice_file'];
    if (!isset($file['error']) || (int) $file['error'] !== UPLOAD_ERR_OK) {
        wp_send_json_error(array('message' => 'Erreur upload slice (code ' . (int) ($file['error'] ?? -1) . ').'), 400);
    }

    $tmp_name = isset($file['tmp_name']) ? (string) $file['tmp_name'] : '';
    if ($tmp_name === '' || !file_exists($tmp_name) || !is_readable($tmp_name)) {
        wp_send_json_error(array('message' => 'Fichier temporaire invalide pour la slice.'), 400);
    }

    // Some server setups make is_uploaded_file() unreliable in admin-ajax contexts.
    if (!is_uploaded_file($tmp_name) && !isset($file['name'])) {
        wp_send_json_error(array('message' => 'Upload slice non reconnu par le serveur.'), 400);
    }

    $requested_filename = isset($_POST['filename']) ? sanitize_file_name(wp_unslash((string) $_POST['filename'])) : '';
    if ($requested_filename === '') {
        $requested_filename = 'slice-' . wp_generate_uuid4() . '.jpg';
    }

    $allowed_exts = array('jpg', 'jpeg', 'jpe', 'png', 'webp');
    $requested_ext = strtolower((string) pathinfo($requested_filename, PATHINFO_EXTENSION));
    if ($requested_ext === '' || !in_array($requested_ext, $allowed_exts, true)) {
        wp_send_json_error(array('message' => 'Extension de slice non autorisée (jpg/png/webp uniquement).'), 400);
    }

    $image_info = @getimagesize($tmp_name);
    if ($image_info === false || empty($image_info['mime'])) {
        wp_send_json_error(array('message' => 'Le fichier slice n\'est pas une image valide.'), 400);
    }

    $mime_to_ext = array(
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    );
    $detected_mime = strtolower((string) $image_info['mime']);
    if (!isset($mime_to_ext[$detected_mime])) {
        wp_send_json_error(array('message' => 'Format image slice non pris en charge (' . $detected_mime . ').'), 400);
    }

    $filename_base = sanitize_file_name(pathinfo($requested_filename, PATHINFO_FILENAME));
    if ($filename_base === '') {
        $filename_base = 'slice-' . time();
    }
    $filename = $filename_base . '.' . $mime_to_ext[$detected_mime];

    $requested_subdir = isset($_POST['export_subdir']) ? sanitize_text_field(wp_unslash((string) $_POST['export_subdir'])) : '';
    $draft_name = isset($_POST['draft_name']) ? sanitize_text_field(wp_unslash((string) $_POST['draft_name'])) : '';
    $export_bucket = isset($_POST['export_bucket']) ? sanitize_key(wp_unslash((string) $_POST['export_bucket'])) : '';
    if ($requested_subdir === '' && $export_bucket !== '') {
        $requested_subdir = mayami_build_visual_links_export_subdir($draft_name, $export_bucket);
    }
    $export_target = mayami_get_visual_links_export_target($requested_subdir);
    if (is_wp_error($export_target)) {
        wp_send_json_error(array('message' => $export_target->get_error_message()), 500);
    }

    $target_path = trailingslashit((string) $export_target['dir']) . $filename;
    $moved = move_uploaded_file($tmp_name, $target_path);
    if (!$moved) {
        $raw_data = file_get_contents($tmp_name);
        if ($raw_data === false || file_put_contents($target_path, $raw_data) === false) {
            wp_send_json_error(array('message' => 'Impossible d\'écrire la slice ' . $filename . '.'), 500);
        }
    }

    $file_url = trailingslashit((string) $export_target['url']) . rawurlencode($filename);

    wp_send_json_success(array(
        'filename' => $filename,
        'path' => $target_path,
        'url' => $file_url,
        'subdir' => (string) $export_target['subdir'],
    ));
}
add_action('wp_ajax_mayami_upload_visual_links_slice', 'mayami_ajax_upload_visual_links_slice');

/**
 * AJAX: export current preview to the dedicated HTML file used for communication.
 */
function mayami_ajax_export_epk_html() {
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Accès refusé.'), 403);
    }

    check_ajax_referer('mayami_epk_draft', 'nonce');

    $html = isset($_POST['html']) ? (string) wp_unslash($_POST['html']) : '';
    if (trim($html) === '') {
        wp_send_json_error(array('message' => 'Contenu HTML vide.'), 400);
    }

    if (strlen($html) > (10 * MB_IN_BYTES)) {
        wp_send_json_error(array('message' => 'Le fichier HTML est trop volumineux.'), 400);
    }

    $draft_name = isset($_POST['draft_name']) ? sanitize_text_field(wp_unslash($_POST['draft_name'])) : '';
    if ($draft_name === '') {
        $draft_name = 'visual-links';
    }
    $safe_name = sanitize_file_name(remove_accents($draft_name));
    $safe_name = trim((string) $safe_name, " .-_\t\n\r\0\x0B");
    if ($safe_name === '') {
        $safe_name = 'visual-links';
    }
    $filename = $safe_name . '.html';

    $requested_subdir = isset($_POST['export_subdir']) ? sanitize_text_field(wp_unslash((string) $_POST['export_subdir'])) : '';
    $export_bucket = isset($_POST['export_bucket']) ? sanitize_key(wp_unslash((string) $_POST['export_bucket'])) : '';
    if ($requested_subdir === '' && $export_bucket !== '') {
        $requested_subdir = mayami_build_visual_links_export_subdir($draft_name, $export_bucket);
    }
    $export_target = mayami_get_visual_links_export_target($requested_subdir);
    if (is_wp_error($export_target)) {
        wp_send_json_error(array('message' => $export_target->get_error_message()), 500);
    }

    $export_path = trailingslashit((string) $export_target['dir']) . $filename;
    $written = file_put_contents($export_path, $html);
    if ($written === false) {
        $last_error = error_get_last();
        $last_error_message = is_array($last_error) && !empty($last_error['message']) ? (string) $last_error['message'] : 'inconnue';
        wp_send_json_error(array('message' => 'Échec de l\'écriture du fichier ' . $filename . ' (' . $last_error_message . ').'), 500);
    }

    $export_url = trailingslashit((string) $export_target['url']) . rawurlencode($filename);

    // Persist export URL into the draft store so it survives page refreshes.
    $draft_id = isset($_POST['draft_id']) ? sanitize_text_field(wp_unslash($_POST['draft_id'])) : '';
    if ($draft_id !== '') {
        $store = mayami_get_epk_drafts_store();
        if (!empty($store[$draft_id]) && is_array($store[$draft_id])) {
            $store[$draft_id]['export_url']      = $export_url;
            $store[$draft_id]['export_filename'] = $filename;
            mayami_update_epk_drafts_store($store);
        }
    }

    wp_send_json_success(array(
        'path'     => $export_path,
        'url'      => $export_url,
        'filename' => $filename,
        'bytes'    => (int) $written,
        'subdir'   => (string) $export_target['subdir'],
    ));
}
add_action('wp_ajax_mayami_export_epk_html', 'mayami_ajax_export_epk_html');
add_action('wp_ajax_mayami_export_visual_links_html', 'mayami_ajax_export_epk_html');

/**
 * Render the standalone HTML Visual Links Builder inside WP admin.
 */
function mayami_render_epk_html_builder_page() {
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You are not allowed to access this page.', 'mayami'), 403);
    }

    $draft_id = isset($_GET['draft_id']) ? sanitize_text_field(wp_unslash($_GET['draft_id'])) : '';
    $html_builder_url = add_query_arg(array(
        'wp_ajax_url' => admin_url('admin-ajax.php'),
        'wp_nonce' => wp_create_nonce('mayami_epk_draft'),
        'epk_draft_id' => $draft_id,
    ), trailingslashit(get_template_directory_uri()) . 'visual-link-builder/visual-links-builder.html');

    $selected_name = '';
    if ($draft_id !== '') {
        $store = mayami_get_epk_drafts_store();
        if (!empty($store[$draft_id]['name'])) {
            $selected_name = (string) $store[$draft_id]['name'];
        }
    }
    ?>
    <div class="wrap mayami-epk-html-page">
        <h1>Visual Links Builder</h1>
        <p>Utilisez ce builder pour ajouter des zones cliquables sur n'importe quel visuel.</p>
        <?php if ($selected_name !== '') : ?>
            <p><strong>Visuel ouvert :</strong> <?php echo esc_html($selected_name); ?></p>
        <?php endif; ?>
        <div style="background:#fff;border:1px solid #dcdcde;border-radius:8px;overflow:hidden;">
            <iframe
                src="<?php echo esc_url($html_builder_url); ?>"
                title="Visual Links Builder"
                style="width:100%;height:calc(100vh - 210px);min-height:760px;border:0;display:block;"
            ></iframe>
        </div>
    </div>
    <?php
}

/**
 * Render the visual drafts list page.
 */
function mayami_render_epk_drafts_page() {
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You are not allowed to access this page.', 'mayami'), 403);
    }

    $store = mayami_get_epk_drafts_store();
    uasort($store, function($a, $b) {
        return strcmp((string) ($b['updated_at'] ?? ''), (string) ($a['updated_at'] ?? ''));
    });
    ?>
    <div class="wrap mayami-epk-drafts-page">
        <h1>Liste des visuels</h1>
        <p>Ouvrez un visuel existant pour reprendre l'edition dans Visual Links Builder.</p>
        <p>
            <a href="<?php echo esc_url(admin_url('admin.php?page=mayami_epk_html_builder_new')); ?>" class="button button-primary">Nouveau visuel</a>
        </p>
        <table class="widefat fixed striped">
            <thead>
                <tr>
                    <th>Nom du visuel</th>
                    <th>Dernière mise à jour</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($store)) : ?>
                    <tr>
                        <td colspan="3">Aucun visuel enregistre pour le moment.</td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($store as $draft) : ?>
                        <tr>
                            <td><?php echo esc_html((string) ($draft['name'] ?? 'Sans nom')); ?></td>
                            <td><?php echo esc_html((string) ($draft['updated_at'] ?? '')); ?></td>
                            <td>
                                <a class="button button-secondary" href="<?php echo esc_url(admin_url('admin.php?page=mayami_epk_html_builder&draft_id=' . rawurlencode((string) ($draft['id'] ?? '')))); ?>">
                                    Ouvrir
                                </a>
                                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline-block;margin-left:8px;">
                                    <?php wp_nonce_field('mayami_delete_epk_draft'); ?>
                                    <input type="hidden" name="action" value="mayami_delete_epk_draft">
                                    <input type="hidden" name="draft_id" value="<?php echo esc_attr((string) ($draft['id'] ?? '')); ?>">
                                    <button
                                        type="submit"
                                        class="button button-link-delete"
                                        onclick="return window.confirm('Supprimer definitivement ce visuel ?');"
                                    >
                                        Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}

// Hidden Statistics page reserved for the owner account only.
function mayami_register_statistics_page() {
    $current_user = wp_get_current_user();
    if (!$current_user || $current_user->user_login !== 'admin-my') {
        return;
    }

    add_submenu_page(
        null,
        'Statistics',
        'Statistics',
        'manage_options',
        'mayami_statistics',
        'mayami_statistics_page'
    );
}
add_action('admin_menu', 'mayami_register_statistics_page');

function mayami_statistics_page() {
    $current_user = wp_get_current_user();
    if (!$current_user || $current_user->user_login !== 'admin-my') {
        wp_die(esc_html__('You are not allowed to access this page.', 'mayami'), 403);
    }

    ?>
    <div class="wrap">
        <h1>📊 Statistics — ellenemasri.pro</h1>
        <p>View your site analytics directly in Google Analytics 4.</p>
        <a href="https://analytics.google.com/analytics/web/#/p539563734/reports/reportinghub"
           target="_blank"
           class="button button-primary" style="font-size:15px;padding:10px 20px;height:auto;margin-top:10px;">
            Open Google Analytics 4 →
        </a>
        <p style="margin-top:20px;color:#666;font-size:13px;">
            Opens in a new tab. Sign in with the Google account linked to this site.
        </p>
    </div>
    <?php
}

/**
 * Force noindex on the public landing while launch is pending.
 *
 * This keeps the site out of search results without relying on plugin settings.
 */
function mayami_force_landing_noindex($robots) {
    if (is_admin()) {
        return $robots;
    }

    if (is_front_page() || is_home()) {
        return array(
            'noindex' => true,
            'nofollow' => true,
            'noarchive' => true,
            'nosnippet' => true,
            'max-snippet' => 0,
            'max-image-preview' => 'none',
            'max-video-preview' => 0,
        );
    }

    return $robots;
}
add_filter('wp_robots', 'mayami_force_landing_noindex');

