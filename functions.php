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
    add_theme_support('site-icon');
    
    // Disable Gutenberg editor (not needed for this landing page)
    add_filter('use_block_editor_for_post', '__return_false');
    
    // Disable WordPress emoji scripts (interferes with Unicode icons)
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
}
add_action('after_setup_theme', 'mayami_theme_setup');

/**
 * Output a fallback favicon when WordPress Site Icon is not configured.
 */
function mayami_output_favicon_fallback() {
    if (function_exists('has_site_icon') && has_site_icon()) {
        return;
    }

    $favicon_svg_path = get_template_directory() . '/assets/favicon.svg';
    $favicon_png_path = get_template_directory() . '/assets/mayami-logo.png';
    $favicon_svg_url = get_template_directory_uri() . '/assets/favicon.svg';
    $favicon_png_url = get_template_directory_uri() . '/assets/mayami-logo.png';

    if (file_exists($favicon_svg_path)) {
        $favicon_svg_url .= '?v=' . filemtime($favicon_svg_path);
    }

    if (file_exists($favicon_png_path)) {
        $favicon_png_url .= '?v=' . filemtime($favicon_png_path);
    }

    echo '<link rel="icon" type="image/svg+xml" href="' . esc_url($favicon_svg_url) . '" />' . "\n";
    echo '<link rel="icon" type="image/png" href="' . esc_url($favicon_png_url) . '" sizes="32x32" />' . "\n";
    echo '<link rel="apple-touch-icon" href="' . esc_url($favicon_png_url) . '" />' . "\n";
}
add_action('wp_head', 'mayami_output_favicon_fallback', 1);
add_action('admin_head', 'mayami_output_favicon_fallback', 1);
add_action('login_head', 'mayami_output_favicon_fallback', 1);

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
    
    $stream_player_js_path = get_template_directory() . '/assets/stream-player.js';

    // Stream platform player JS
    wp_enqueue_script(
        'mayami-stream-player',
        get_template_directory_uri() . '/assets/stream-player.js',
        [],
        file_exists($stream_player_js_path) ? (string) filemtime($stream_player_js_path) : '1.0.0',
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

    // Keep only media creation in the + New menu for client admins.
    $wp_admin_bar->remove_node('new-post');
    $wp_admin_bar->remove_node('new-page');
    $wp_admin_bar->remove_node('new-user');

    // Hide comments bubble since comments menu is also hidden.
    $wp_admin_bar->remove_node('comments');
}
add_action('admin_bar_menu', 'mayami_limit_admin_bar_for_client', 999);

