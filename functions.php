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
    // Only load on our options page
    if ('toplevel_page_mayami_landing_options' !== $hook) {
        return;
    }
    
    $admin_css_path = get_template_directory() . '/assets/admin-nav.css';
    $admin_js_path = get_template_directory() . '/assets/admin-nav.js';

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
}
add_action('admin_enqueue_scripts', 'mayami_enqueue_admin_assets');

/**
 * Hide default WordPress footer text on Mayami settings page.
 */
function mayami_hide_wp_footer_text_on_landing($text) {
    $screen = get_current_screen();
    if ($screen && $screen->id === 'toplevel_page_mayami_landing_options') {
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

// Statistics menu - GA4 shortcut for all admin users (including client)
function mayami_add_statistics_menu() {
    add_menu_page(
        'Statistics',
        'Statistics',
        'manage_options',
        'mayami_statistics',
        'mayami_statistics_page',
        'dashicons-chart-bar',
        3
    );
}
add_action('admin_menu', 'mayami_add_statistics_menu');

function mayami_statistics_page() {
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

// Google Tag Manager - snippet <head>
function mayami_gtm_head() {
    ?>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-ND9D6VCZ');</script>
    <!-- End Google Tag Manager -->
    <?php
}
add_action('wp_head', 'mayami_gtm_head', 1);

// Google Tag Manager - snippet <body>
function mayami_gtm_body() {
    ?>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-ND9D6VCZ"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <?php
}
add_action('wp_body_open', 'mayami_gtm_body', 1);

