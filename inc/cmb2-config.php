<?php
/**
 * CMB2 Configuration - Page unique avec navigation sticky
 */

if (!defined('ABSPATH')) exit;

add_action('cmb2_admin_init', 'mayami_register_options');
add_action('admin_init', 'mayami_initialize_default_content');
add_action('admin_init', 'mayami_sync_platform_links_once', 20);
add_action('admin_init', 'mayami_sync_hero_top_artist_once', 21);
add_action('admin_init', 'mayami_sync_marquee_play_link_once', 22);
add_action('admin_init', 'mayami_sync_stream_platforms_once', 23);
add_action('admin_init', 'mayami_sync_follow_youtube_link_once', 24);
add_action('admin_init', 'mayami_sync_marquee_items_once', 25);
add_action('admin_init', 'mayami_sync_social_links_once', 26);
add_action('admin_init', 'mayami_sync_sticky_links_once', 27);
add_action('admin_init', 'mayami_sync_stream_values_to_front_once', 28);
add_action('admin_head', 'mayami_sticky_save_button');

/**
 * Add sticky save button at the top of CMB2 admin page
 */
function mayami_sticky_save_button() {
    $screen = get_current_screen();
    if ($screen && strpos($screen->id, 'mayami_landing_options') !== false) {
        ?>
        <style>
            .wrap > h1,
            .wrap > h1.wp-heading-inline {
                display: none !important;
            }
            #mayami-save-button-sticky {
                background: #fff !important;
                color: #6b21a8 !important;
                border: 2px solid #fff !important;
                padding: 8px 20px !important;
                font-size: 13px !important;
                font-weight: 700 !important;
                border-radius: 6px !important;
                cursor: pointer !important;
                transition: all 0.2s !important;
                text-transform: uppercase !important;
                letter-spacing: 0.5px !important;
                margin-left: auto !important;
            }
            #mayami-save-button-sticky:hover {
                background: #f0f0f1 !important;
                transform: translateY(-1px) !important;
                box-shadow: 0 3px 8px rgba(0,0,0,0.2) !important;
            }
        </style>
        <script>
            jQuery(document).ready(function($) {
                // Attendre que la navbar soit créée par CMB2
                setTimeout(function() {
                    var $navbar = $('.cmb-tabs-nav, .cmb2-wrap > nav, [class*="cmb"] nav, .cmb-tabs');
                    
                    if (!$navbar.length) {
                        // Chercher toute div qui contient les boutons de navigation
                        $navbar = $('div').filter(function() {
                            return $(this).css('background-color') === 'rgb(107, 33, 168)' || 
                                   $(this).css('background-color').includes('107, 33, 168');
                        });
                    }
                    
                    if ($navbar.length) {
                        var $saveButton = $('<button type="button" id="mayami-save-button-sticky">💾 Enregistrer</button>');
                        
                        $navbar.append($saveButton);
                        
                        $saveButton.on('click', function(e) {
                            e.preventDefault();
                            var $realButton = $('.cmb-form input[type="submit"], .cmb2-wrap input[type="submit"]').first();
                            if ($realButton.length) {
                                $realButton.trigger('click');
                            }
                        });
                    } else {
                        console.log('Navbar violette non trouvée');
                    }
                }, 500);
            });
        </script>
        <?php
    }
}

/**
 * Initialize default content for groups (slider, marquee, release rows)
 */
function mayami_initialize_default_content() {
    $option_key = 'mayami_landing_options';
    
    // Check if already initialized (or if reset is requested)
    $reset_requested = isset($_GET['mayami_reset']) && $_GET['mayami_reset'] === '1';
    
    if (!$reset_requested && get_option('mayami_content_initialized')) {
        return;
    }
    
    $theme_url = get_template_directory_uri();
    
    // Default slider content
    $default_slider = array(
        array(
            'slide_admin_title' => 'Slide 1',
            'slide_type' => 'image',
            'slide_image' => $theme_url . '/assets/mayami-artist.jpg',
            'alt_text' => 'Ellene Leya Masri — portrait 1',
            'slide_duration' => '5',
        ),
        array(
            'slide_admin_title' => 'Slide 2',
            'slide_type' => 'image',
            'slide_image' => $theme_url . '/assets/mayami-cover.jpg',
            'alt_text' => 'Ellene Leya Masri — portrait 2',
            'slide_duration' => '5',
        ),
        array(
            'slide_admin_title' => 'Slide 3',
            'slide_type' => 'video',
            'video_url' => 'https://www.youtube.com/watch?v=WiB_UoexqVo&pp=0gcJCQoLAYcqIYzv',
            'alt_text' => 'Mayami official video',
            'slide_duration' => '5',
        ),
    );
    
    // Default release rows
    $default_release_rows = array(
        array('key' => 'Artists', 'value' => 'Richard Bona & Ellene Masri'),
        array('key' => 'Title', 'value' => 'Mayami, My Miami'),
        array('key' => 'Release date', 'value' => 'May 29th'),
        array('key' => 'Location', 'value' => 'Miami, USA'),
        array('key' => 'Video', 'value' => 'Coming soon'),
    );
    
    // Get current options
    $options = get_option($option_key, array());
    
    // Initialize groups if empty
    if (empty($options['hero_slider'])) {
        $options['hero_slider'] = $default_slider;
    }
    
    if (empty($options['release_rows'])) {
        $options['release_rows'] = $default_release_rows;
    }

    if (empty($options['marquee_play_link']) && !empty($options['link_spotify'])) {
        $options['marquee_play_link'] = $options['link_spotify'];
    }

    if (!isset($options['marquee_show_music_icon'])) {
        $options['marquee_show_music_icon'] = 'on';
    }

    if (!isset($options['marquee_logo_hidden'])) {
        $options['marquee_logo_hidden'] = '';
    }

    // Set default images
    if (empty($options['video_cover_image'])) {
        $options['video_cover_image'] = $theme_url . '/assets/mayami-cover.jpg';
    }
    
    if (empty($options['release_cover_image'])) {
        $options['release_cover_image'] = $theme_url . '/assets/mayami-cover.jpg';
    }
    
    if (empty($options['cta_texture_image'])) {
        $options['cta_texture_image'] = $theme_url . '/assets/mayami-texture.jpg';
    }
    
    // Save options
    update_option($option_key, $options);
    
    // Mark as initialized
    update_option('mayami_content_initialized', true);
}

/**
 * One-time sync of platform links stored in options.
 *
 * Why: previously saved values and legacy keys can keep old URLs in the admin UI,
 * even when defaults are updated in code.
 */
function mayami_sync_platform_links_once() {
    $sync_flag = 'mayami_platform_links_synced_20260529';
    if (get_option($sync_flag)) {
        return;
    }

    $option_key = 'mayami_landing_options';
    $options = get_option($option_key, array());
    if (!is_array($options)) {
        update_option($sync_flag, true);
        return;
    }

    $new_links = array(
        'link_spotify' => 'https://open.spotify.com/intl-fr/track/3rzrziofCOwRrI1r99IUbQ?si=a2cd3f4cbe364a94',
        'link_apple_music' => 'https://music.apple.com/fr/song/mayami-my-miami/6771742499',
        'link_youtube_music' => 'https://youtu.be/EH_QcQ92hSk?si=gpybhKJbZrDN1Ew5',
        'link_deezer' => 'https://link.deezer.com/s/33p3MydevJFz4yqu2aEam',
        'link_amazon_music' => 'https://music.amazon.com/tracks/B0H2FR3WHQ?marketplaceId=ATVPDKIKX0DER&musicTerritory=US&ref=dm_sh_gPJPR79AtgfLS0EFarS9Xwi57',
    );

    $legacy_key_map = array(
        'link_apple' => 'link_apple_music',
        'link_amazon' => 'link_amazon_music',
    );

    $legacy_values = array(
        'link_spotify' => array(
            'https://open.spotify.com/intl-fr/artist/2c6x9IL7EvoUU6XQ642S8c',
        ),
        'link_apple_music' => array(
            'https://music.apple.com/fr/album/music/1722440356',
        ),
        'link_youtube_music' => array(
            'https://www.youtube.com/user/ellenemasriOFFICIAL',
            'https://www.youtube.com/embed?listType=user_uploads&list=ellenemasriOFFICIAL',
        ),
        'link_deezer' => array(
            'https://www.deezer.com/fr/artist/5316718',
            'https://api.ffm.to/sl/e/c/mayami?',
        ),
        'link_amazon_music' => array(
            'https://music.amazon.fr/artists/B00GBFZTHW/ellene-masri',
        ),
    );

    $changed = false;

    // Migrate legacy keys to current keys if needed.
    foreach ($legacy_key_map as $legacy_key => $current_key) {
        if (empty($options[$current_key]) && !empty($options[$legacy_key])) {
            $options[$current_key] = $options[$legacy_key];
            $changed = true;
        }
    }

    // Replace empty/outdated values with the new canonical URLs.
    foreach ($new_links as $key => $new_value) {
        $current = isset($options[$key]) ? trim((string) $options[$key]) : '';
        if ($current === '') {
            $options[$key] = $new_value;
            $changed = true;
            continue;
        }

        if (!isset($legacy_values[$key])) {
            continue;
        }

        $is_legacy = false;
        foreach ($legacy_values[$key] as $legacy_value) {
            if (stripos($current, $legacy_value) !== false) {
                $is_legacy = true;
                break;
            }
        }

        if ($is_legacy) {
            $options[$key] = $new_value;
            $changed = true;
        }
    }

    if ($changed) {
        update_option($option_key, $options);
    }

    update_option($sync_flag, true);
}

/**
 * One-time sync of stream platforms stored in options.
 */
function mayami_sync_stream_platforms_once() {
    $sync_flag = 'mayami_stream_platforms_synced_20260529';
    if (get_option($sync_flag)) {
        return;
    }

    $option_key = 'mayami_landing_options';
    $options = get_option($option_key, array());
    if (!is_array($options)) {
        update_option($sync_flag, true);
        return;
    }

    if (!empty($options['stream_platforms']) && is_array($options['stream_platforms'])) {
        update_option($sync_flag, true);
        return;
    }

    $options['stream_platforms'] = array(
        array(
            'is_active' => 'on',
            'label'     => 'Spotify',
            'href'      => !empty($options['link_spotify']) ? $options['link_spotify'] : 'https://open.spotify.com/intl-fr/track/3rzrziofCOwRrI1r99IUbQ?si=a2cd3f4cbe364a94',
        ),
        array(
            'is_active' => 'on',
            'label'     => 'Apple Music',
            'href'      => !empty($options['link_apple_music']) ? $options['link_apple_music'] : 'https://music.apple.com/fr/song/mayami-my-miami/6771742499',
        ),
        array(
            'is_active' => 'on',
            'label'     => 'YouTube Music',
            'href'      => !empty($options['link_youtube_music']) ? $options['link_youtube_music'] : 'https://youtu.be/EH_QcQ92hSk?si=gpybhKJbZrDN1Ew5',
        ),
        array(
            'is_active' => 'on',
            'label'     => 'Deezer',
            'href'      => !empty($options['link_deezer']) ? $options['link_deezer'] : 'https://link.deezer.com/s/33p3MydevJFz4yqu2aEam',
        ),
        array(
            'is_active' => 'on',
            'label'     => 'Amazon Music',
            'href'      => !empty($options['link_amazon_music']) ? $options['link_amazon_music'] : 'https://music.amazon.com/tracks/B0H2FR3WHQ?marketplaceId=ATVPDKIKX0DER&musicTerritory=US&ref=dm_sh_gPJPR79AtgfLS0EFarS9Xwi57',
        ),
        array(
            'is_active' => 'on',
            'label'     => 'SoundCloud',
            'href'      => !empty($options['link_soundcloud']) ? $options['link_soundcloud'] : 'https://soundcloud.com/ellenemasri',
        ),
    );

    update_option($option_key, $options);
    update_option($sync_flag, true);
}

/**
 * One-time sync to ensure admin values exactly match current working front stream values.
 */
function mayami_sync_stream_values_to_front_once() {
    $sync_flag = 'mayami_stream_values_aligned_with_front_20260530';
    if (get_option($sync_flag)) {
        return;
    }

    $option_key = 'mayami_landing_options';
    $options = get_option($option_key, array());
    if (!is_array($options)) {
        update_option($sync_flag, true);
        return;
    }

    $target_links = array(
        'Spotify' => 'https://open.spotify.com/intl-fr/track/3rzrziofCOwRrI1r99IUbQ?si=a2cd3f4cbe364a94',
        'Apple Music' => 'https://music.apple.com/fr/song/mayami-my-miami/6771742499',
        'YouTube Music' => 'https://youtu.be/EH_QcQ92hSk?si=gpybhKJbZrDN1Ew5',
        'Deezer' => 'https://www.deezer.com/track/4034160411',
        'Amazon Music' => 'https://music.amazon.com/tracks/B0H2FR3WHQ?marketplaceId=ATVPDKIKX0DER&musicTerritory=US&ref=dm_sh_gPJPR79AtgfLS0EFarS9Xwi57',
        'SoundCloud' => 'https://soundcloud.com/ellenemasri',
    );

    $target_option_links = array(
        'link_spotify' => $target_links['Spotify'],
        'link_apple_music' => $target_links['Apple Music'],
        'link_youtube_music' => $target_links['YouTube Music'],
        'link_deezer' => $target_links['Deezer'],
        'link_amazon_music' => $target_links['Amazon Music'],
        'link_soundcloud' => $target_links['SoundCloud'],
    );

    $changed = false;

    foreach ($target_option_links as $option_id => $target_value) {
        $current = isset($options[$option_id]) ? trim((string) $options[$option_id]) : '';
        if ($current !== $target_value) {
            $options[$option_id] = $target_value;
            $changed = true;
        }
    }

    $existing_platforms = isset($options['stream_platforms']) && is_array($options['stream_platforms'])
        ? $options['stream_platforms']
        : array();

    $platforms_by_label = array();
    foreach ($existing_platforms as $platform) {
        if (!is_array($platform)) {
            continue;
        }

        $label = isset($platform['label']) ? trim((string) $platform['label']) : '';
        if ($label !== '') {
            $platforms_by_label[$label] = $platform;
        }
    }

    $aligned_platforms = array();
    foreach ($target_links as $label => $href) {
        $platform = isset($platforms_by_label[$label]) ? $platforms_by_label[$label] : array();
        $platform['label'] = $label;
        $platform['href'] = $href;
        if (!isset($platform['is_active']) || $platform['is_active'] === '') {
            $platform['is_active'] = 'on';
        }
        $aligned_platforms[] = $platform;
    }

    if ($existing_platforms !== $aligned_platforms) {
        $options['stream_platforms'] = $aligned_platforms;
        $changed = true;
    }

    if ($changed) {
        update_option($option_key, $options);
    }

    update_option($sync_flag, true);
}

/**
 * One-time sync for follow YouTube link.
 */
function mayami_sync_follow_youtube_link_once() {
    $sync_flag = 'mayami_follow_youtube_link_synced_20260529';
    if (get_option($sync_flag)) {
        return;
    }

    $option_key = 'mayami_landing_options';
    $options = get_option($option_key, array());
    if (!is_array($options)) {
        update_option($sync_flag, true);
        return;
    }

    $target_url = 'http://www.youtube.com/@ELLENEMASRI';
    $current = isset($options['link_youtube_video']) ? trim((string) $options['link_youtube_video']) : '';

    if ($current === '' || stripos($current, 'watch?v=WiB_UoexqVo') !== false) {
        $options['link_youtube_video'] = $target_url;
        update_option($option_key, $options);
    }

    update_option($sync_flag, true);
}

/**
 * One-time sync for hero top artist to align admin value with current front expectation.
 */
function mayami_sync_hero_top_artist_once() {
    $sync_flag = 'mayami_hero_top_artist_synced_20260529';
    if (get_option($sync_flag)) {
        return;
    }

    $option_key = 'mayami_landing_options';
    $options = get_option($option_key, array());
    if (!is_array($options)) {
        update_option($sync_flag, true);
        return;
    }

    $target_value = 'Richard Bona & Ellen Masri';
    $legacy_values = array(
        '',
        'Ellene Masri',
        'Ellene Leya Masri',
        'Richard Bona & Ellene Masri',
    );

    $current = isset($options['hero_top_artist']) ? trim((string) $options['hero_top_artist']) : '';
    if (in_array($current, $legacy_values, true)) {
        $options['hero_top_artist'] = $target_value;
        update_option($option_key, $options);
    }

    update_option($sync_flag, true);
}

/**
 * One-time sync for marquee play icon link based on Spotify link.
 */
function mayami_sync_marquee_play_link_once() {
    $sync_flag = 'mayami_marquee_play_link_synced_20260529';
    if (get_option($sync_flag)) {
        return;
    }

    $option_key = 'mayami_landing_options';
    $options = get_option($option_key, array());
    if (!is_array($options)) {
        update_option($sync_flag, true);
        return;
    }

    $changed = false;

    $spotify_link = isset($options['link_spotify']) ? trim((string) $options['link_spotify']) : '';
    $marquee_play_link = isset($options['marquee_play_link']) ? trim((string) $options['marquee_play_link']) : '';

    if ($marquee_play_link === '' && $spotify_link !== '') {
        $options['marquee_play_link'] = $spotify_link;
        $changed = true;
    }

    if (!isset($options['marquee_show_music_icon'])) {
        $options['marquee_show_music_icon'] = 'on';
        $changed = true;
    }

    if ($changed) {
        update_option($option_key, $options);
    }

    update_option($sync_flag, true);
}

/**
 * Ensure TOP-BAR has required management items and clean legacy icon-toggle items.
 */
function mayami_sync_marquee_items_once() {
     $sync_flag = 'mayami_marquee_items_synced_20260530_v5';
    if (get_option($sync_flag)) {
        return;
    }

    $option_key = 'mayami_landing_options';
    $options = get_option($option_key, array());
    if (!is_array($options)) {
        update_option($sync_flag, true);
        return;
    }

    $items = isset($options['marquee_items']) && is_array($options['marquee_items']) ? $options['marquee_items'] : array();
    $clean_items = array();

    $changed = false;

    if (array_key_exists('marquee_show_stream_icons', $options)) {
        unset($options['marquee_show_stream_icons']);
        $changed = true;
    }

    if (array_key_exists('marquee_stream_icons_new_tab', $options)) {
        unset($options['marquee_stream_icons_new_tab']);
        $changed = true;
    }

    foreach ($items as $item) {
        if (!is_array($item)) {
            continue;
        }

        $label = strtolower(trim(remove_accents((string) ($item['label'] ?? ''))));
        if ($label === 'afficher les icones' || $label === 'icones stream' || $label === 'icone plateformes') {
            $changed = true;
            continue;
        }

        if (!empty($item['external'])) {
            $item['external'] = '';
            $changed = true;
        }

        $clean_items[] = $item;
    }

    if ($changed) {
        $options['marquee_items'] = $clean_items;
        update_option($option_key, $options);
    }

    update_option($sync_flag, true);
}

/**
 * One-time migration of Social links from legacy link_* keys.
 */
function mayami_sync_social_links_once() {
    $sync_flag = 'mayami_social_links_synced_20260530';
    if (get_option($sync_flag)) {
        return;
    }

    $option_key = 'mayami_landing_options';
    $options = get_option($option_key, array());
    if (!is_array($options)) {
        update_option($sync_flag, true);
        return;
    }

    $map = array(
        'social_tiktok_link' => 'link_tiktok',
        'social_instagram_link' => 'link_instagram',
        'social_youtube_link' => 'link_youtube_video',
    );

    $changed = false;
    foreach ($map as $new_key => $legacy_key) {
        $new_value = isset($options[$new_key]) ? trim((string) $options[$new_key]) : '';
        $legacy_value = isset($options[$legacy_key]) ? trim((string) $options[$legacy_key]) : '';

        if ($new_value === '' && $legacy_value !== '') {
            $options[$new_key] = $legacy_value;
            $changed = true;
        }
    }

    if ($changed) {
        update_option($option_key, $options);
    }

    update_option($sync_flag, true);
}

/**
 * One-time migration of Sticky links from legacy keys.
 */
function mayami_sync_sticky_links_once() {
    $sync_flag = 'mayami_sticky_links_synced_20260530';
    if (get_option($sync_flag)) {
        return;
    }

    $option_key = 'mayami_landing_options';
    $options = get_option($option_key, array());
    if (!is_array($options)) {
        update_option($sync_flag, true);
        return;
    }

    $sticky_tiktok = isset($options['sticky_tiktok_link']) ? trim((string) $options['sticky_tiktok_link']) : '';
    if ($sticky_tiktok === '') {
        $social_tiktok = isset($options['social_tiktok_link']) ? trim((string) $options['social_tiktok_link']) : '';
        $legacy_tiktok = isset($options['link_tiktok']) ? trim((string) $options['link_tiktok']) : '';

        if ($social_tiktok !== '') {
            $options['sticky_tiktok_link'] = $social_tiktok;
            update_option($option_key, $options);
        } elseif ($legacy_tiktok !== '') {
            $options['sticky_tiktok_link'] = $legacy_tiktok;
            update_option($option_key, $options);
        }
    }

    update_option($sync_flag, true);
}


function mayami_register_options() {
    
    $option_key = 'mayami_landing_options';
    
    // PAGE UNIQUE
    $cmb = new_cmb2_box(array(
        'id'           => 'mayami_main_page',
        'title'        => 'Mayami Landing Settings',
        'object_types' => array('options-page'),
        'option_key'   => $option_key,
        'icon_url'     => 'dashicons-admin-site-alt3',
        'menu_title'   => 'Mayami Landing',
        'position'     => 2,
    ));
    
    // ========== SECTION: HERO ==========
    $cmb->add_field(array(
        'name' => 'Hero',
        'type' => 'title',
        'id'   => 'section_hero_title',
    ));

    $cmb->add_field(array(
        'name'    => 'Top Artist',
        'id'      => 'hero_top_artist',
        'type'    => 'text',
        'default' => 'Richard Bona & Ellen Masri',
    ));

    $cmb->add_field(array(
        'name'    => 'Top CTA Label',
        'id'      => 'hero_top_cta_label',
        'type'    => 'text',
        'default' => 'Out tomorrow',
    ));

    $cmb->add_field(array(
        'name' => 'Top CTA Link',
        'id'   => 'hero_top_cta_href',
        'type' => 'text_url',
    ));

    $cmb->add_field(array(
        'name'    => 'Badge Text',
        'id'      => 'hero_badge_text',
        'type'    => 'text',
        'default' => 'New Single · Out Tomorrow',
    ));

    $cmb->add_field(array(
        'name'    => 'Subtitle',
        'id'      => 'hero_subtitle',
        'type'    => 'text',
        'default' => 'Mayami, My Miami',
    ));

    $cmb->add_field(array(
        'name' => 'Main Title (SEO)',
        'id'   => 'hero_main_title',
        'type' => 'text',
    ));

    $cmb->add_field(array(
        'name' => 'Background Image',
        'id'   => 'hero_background_image',
        'type' => 'file',
    ));

    $cmb->add_field(array(
        'name' => 'Main Logo Image',
        'id'   => 'hero_logo_image',
        'type' => 'file',
    ));

    $cmb->add_field(array(
        'name' => 'Main Logo Alt Text',
        'id'   => 'hero_logo_alt',
        'type' => 'text',
    ));

    $cmb->add_field(array(
        'name'    => 'Description',
        'id'      => 'hero_description',
        'type'    => 'textarea_small',
        'default' => 'A sunset-soaked love letter to the city. Stream it, watch it, share it — and follow the journey from the painted walls of Miami.',
    ));

    $cmb->add_field(array(
        'name'    => 'Stream Button - Label',
        'id'      => 'hero_stream_label',
        'type'    => 'text',
        'default' => '◉ Stream',
    ));

    $cmb->add_field(array(
        'name'    => 'Stream Button - Link',
        'id'      => 'hero_stream_href',
        'type'    => 'text_url',
        'default' => 'https://ffm.to/mayami',
    ));

    $cmb->add_field(array(
        'name'    => 'Watch Button - Label',
        'id'      => 'hero_watch_label',
        'type'    => 'text',
        'default' => '▶ Watch',
    ));

    $cmb->add_field(array(
        'name'    => 'Watch Button - Link',
        'id'      => 'hero_watch_href',
        'type'    => 'text',
        'default' => '#video',
    ));

    // ========== SECTION: SLIDER ==========
    $cmb->add_field(array(
        'name' => 'Slider',
        'type' => 'title',
        'id'   => 'section_slider_title',
    ));
    $slider_group = $cmb->add_field(array(
        'id'      => 'hero_slider',
        'type'    => 'group',
        'options' => array(
            'group_title'   => 'Slide {#}',
            'add_button'    => '+ Ajouter un slide',
            'remove_button' => 'Supprimer',
            'sortable'      => true,
        ),
    ));

    $cmb->add_group_field($slider_group, array(
        'name' => 'Nom du slide',
        'id'   => 'slide_admin_title',
        'type' => 'text',
    ));

    $cmb->add_group_field($slider_group, array(
        'name'    => 'Type',
        'id'      => 'slide_type',
        'type'    => 'select',
        'options' => array(
            'image' => 'Image',
            'video' => 'Vidéo YouTube',
            'tiktok' => 'Vidéo TikTok',
        ),
    ));

    $cmb->add_group_field($slider_group, array(
        'name' => 'Image',
        'id'   => 'slide_image',
        'type' => 'file',
    ));

    $cmb->add_group_field($slider_group, array(
        'name' => 'URL YouTube',
        'id'   => 'video_url',
        'type' => 'text_url',
    ));

    $cmb->add_group_field($slider_group, array(
        'name'    => 'URL TikTok',
        'id'      => 'tiktok_url',
        'type'    => 'text_url',
        'desc'    => 'Colle l’URL du post TikTok, par exemple https://www.tiktok.com/@ellenemasri/video/7645173351501008141',
        'visible' => array('slide_type', '=', 'tiktok'),
    ));

    $cmb->add_group_field($slider_group, array(
        'name'    => 'Vidéo MP4 TikTok (médiathèque)',
        'id'      => 'tiktok_video_url',
        'type'    => 'file',
        'desc'    => 'Choisis un fichier MP4 déjà uploadé dans la médiathèque pour un rendu plein écran sans chrome ni vidéos similaires. L’embed officiel reste en fallback si ce champ est vide.',
        'visible' => array('slide_type', '=', 'tiktok'),
    ));

    $cmb->add_group_field($slider_group, array(
        'name' => 'Texte Alt',
        'id'   => 'alt_text',
        'type' => 'text',
    ));

    $cmb->add_group_field($slider_group, array(
        'name'       => 'Durée du slide (secondes)',
        'id'         => 'slide_duration',
        'type'       => 'text_small',
        'default'    => '5',
        'attributes' => array(
            'type' => 'number',
            'min'  => '1',
            'step' => '1',
        ),
        'desc'       => 'Durée avant passage au slide suivant.',
    ));

    // ========== SECTION: STREAM ==========
    $cmb->add_field(array(
        'name' => 'Stream',
        'type' => 'title',
        'id'   => 'section_stream_title',
    ));

    $cmb->add_field(array(
        'name'    => 'Kicker',
        'id'      => 'stream_kicker',
        'type'    => 'text',
        'default' => '01 / Listen',
    ));

    $cmb->add_field(array(
        'name'    => 'Title Prefix',
        'id'      => 'stream_title_prefix',
        'type'    => 'text',
        'default' => 'Stream',
    ));

    $cmb->add_field(array(
        'name'    => 'Title Logo',
        'id'      => 'stream_title_highlight',
        'type'    => 'file',
        'desc'    => 'Logo image affiche a droite de "Stream" dans la section front.',
    ));

    $cmb->add_field(array(
        'name'    => 'Availability Text',
        'id'      => 'stream_availability_text',
        'type'    => 'text',
        'default' => 'Available everywhere',
    ));

    $cmb->add_field(array(
        'name'    => 'Card Label',
        'id'      => 'stream_card_label',
        'type'    => 'text',
        'default' => 'Listen on',
    ));

    $stream_platforms = $cmb->add_field(array(
        'id'      => 'stream_platforms',
        'type'    => 'group',
        'options' => array(
            'group_title'   => 'Plateforme {#}',
            'add_button'    => '+ Ajouter une plateforme',
            'remove_button' => 'Supprimer',
            'sortable'      => true,
        ),
    ));

    $cmb->add_group_field($stream_platforms, array(
        'name'    => 'Active',
        'id'      => 'is_active',
        'type'    => 'checkbox',
        'default' => 'on',
    ));

    $cmb->add_group_field($stream_platforms, array(
        'name' => 'Nom',
        'id'   => 'label',
        'type' => 'text',
    ));

    $cmb->add_group_field($stream_platforms, array(
        'name' => 'Lien',
        'id'   => 'href',
        'type' => 'text_url',
    ));

    // ========== SECTION: SOCIAL ==========
    $cmb->add_field(array(
        'name' => 'Social',
        'type' => 'title',
        'id'   => 'section_social_title',
    ));

    $cmb->add_field(array(
        'name'    => 'Kicker',
        'id'      => 'social_kicker',
        'type'    => 'text',
        'default' => '02 / Follow',
    ));

    $cmb->add_field(array(
        'name'    => 'Title Left',
        'id'      => 'social_title_left',
        'type'    => 'text',
        'default' => 'Join the',
    ));

    $cmb->add_field(array(
        'name'    => 'Title Right',
        'id'      => 'social_title_right',
        'type'    => 'text',
        'default' => 'journey',
    ));

    $cmb->add_field(array(
        'name'    => 'Description',
        'id'      => 'social_description',
        'type'    => 'textarea_small',
        'default' => 'Snippets, behind-the-scenes, dance challenges — drop into the Miami diary.',
    ));

    $cmb->add_field(array(
        'name' => 'TikTok Link',
        'id'   => 'social_tiktok_link',
        'type' => 'text_url',
    ));

    $cmb->add_field(array(
        'name' => 'Instagram Link',
        'id'   => 'social_instagram_link',
        'type' => 'text_url',
    ));

    $cmb->add_field(array(
        'name' => 'YouTube Link',
        'id'   => 'social_youtube_link',
        'type' => 'text_url',
    ));

    // ========== SECTION: VIDEO ==========
    $cmb->add_field(array(
        'name' => 'Video',
        'type' => 'title',
        'id'   => 'section_video_title',
    ));

    $cmb->add_field(array(
        'name'    => 'Kicker',
        'id'      => 'video_kicker',
        'type'    => 'text',
        'default' => '03 / Watch',
    ));

    $cmb->add_field(array(
        'name'    => 'Title',
        'id'      => 'video_title',
        'type'    => 'text',
        'default' => 'Official Video',
    ));

    $cmb->add_field(array(
        'name'    => 'Description',
        'id'      => 'video_description',
        'type'    => 'textarea_small',
        'default' => 'A love letter to Miami — shot on sunset walls, neon boulevards and the Atlantic shoreline.',
    ));

    $cmb->add_field(array(
        'name'    => 'Status Text',
        'id'      => 'video_status',
        'type'    => 'text',
        'default' => 'Coming soon',
    ));

    $cmb->add_field(array(
        'name'    => 'Watch Button Label',
        'id'      => 'video_watch_label',
        'type'    => 'text',
        'default' => 'Watch on YouTube',
    ));

    $cmb->add_field(array(
        'name'    => 'Watch Button Link',
        'id'      => 'video_watch_href',
        'type'    => 'text_url',
        'default' => 'https://www.youtube.com/watch?v=WiB_UoexqVo&pp=0gcJCQoLAYcqIYzv',
    ));

    $cmb->add_field(array(
        'name'    => 'Disable Watch Link',
        'id'      => 'video_watch_disable_link',
        'type'    => 'checkbox',
        'default' => '',
    ));

    $cmb->add_field(array(
        'name' => 'Cover Image',
        'id'   => 'video_cover_image',
        'type' => 'file',
    ));

    // ========== SECTION: RELEASE INFO ==========
    $cmb->add_field(array(
        'name' => 'Release',
        'type' => 'title',
        'id'   => 'section_release_title',
    ));

    $cmb->add_field(array(
        'name'    => 'Kicker',
        'id'      => 'release_kicker',
        'type'    => 'text',
        'default' => '04 / Release Info',
    ));

    $cmb->add_field(array(
        'name'    => 'Title Left',
        'id'      => 'release_title_left',
        'type'    => 'text',
        'default' => 'The',
    ));

    $cmb->add_field(array(
        'name'    => 'Title Highlight',
        'id'      => 'release_title_highlight',
        'type'    => 'text',
        'default' => 'credits',
    ));

    $cmb->add_field(array(
        'name' => 'Cover Image',
        'id'   => 'release_cover_image',
        'type' => 'file',
    ));

    $release_rows = $cmb->add_field(array(
        'id'      => 'release_rows',
        'type'    => 'group',
        'options' => array(
            'group_title'   => 'Info {#}',
            'add_button'    => '+ Ajouter',
            'remove_button' => 'Supprimer',
            'sortable'      => true,
        ),
    ));

    $cmb->add_group_field($release_rows, array(
        'name' => 'Label',
        'id'   => 'key',
        'type' => 'text',
    ));

    $cmb->add_group_field($release_rows, array(
        'name' => 'Valeur',
        'id'   => 'value',
        'type' => 'text',
    ));

    // ========== SECTION: CTA ==========
    $cmb->add_field(array(
        'name' => 'CTA',
        'type' => 'title',
        'id'   => 'section_cta_title',
    ));

    $cmb->add_field(array(
        'name'    => 'Kicker',
        'id'      => 'cta_kicker',
        'type'    => 'text',
        'default' => "05 / Don't sleep on it",
    ));

    $cmb->add_field(array(
        'name'    => 'Title Left',
        'id'      => 'cta_title_left',
        'type'    => 'text',
        'default' => 'Press',
    ));

    $cmb->add_field(array(
        'name'    => 'Title Right',
        'id'      => 'cta_title_right',
        'type'    => 'text',
        'default' => 'play.',
    ));

    $cmb->add_field(array(
        'name'    => 'Description',
        'id'      => 'cta_description',
        'type'    => 'textarea_small',
        'default' => 'Stream the single. Watch the video. Tag and ride the wave.',
    ));

    $cmb->add_field(array(
        'name'    => 'Hashtag',
        'id'      => 'cta_hashtag',
        'type'    => 'text',
        'default' => '#MayamiMyMiami',
    ));

    $cmb->add_field(array(
        'name'    => 'Stream Button Link',
        'id'      => 'cta_stream_link',
        'type'    => 'text',
        'default' => '#stream',
    ));

    $cmb->add_field(array(
        'name'    => 'Video Button Link',
        'id'      => 'cta_video_link',
        'type'    => 'text',
        'default' => '#video',
    ));

    $cmb->add_field(array(
        'name'    => 'TikTok Button Link',
        'id'      => 'cta_tiktok_link',
        'type'    => 'text_url',
        'default' => 'https://www.tiktok.com/@ellenemasri',
    ));

    $cmb->add_field(array(
        'name'    => 'Instagram Button Link',
        'id'      => 'cta_instagram_link',
        'type'    => 'text_url',
        'default' => 'https://www.instagram.com/ellenemasri/',
    ));

    $cmb->add_field(array(
        'name' => 'Texture Image',
        'id'   => 'cta_texture_image',
        'type' => 'file',
    ));

    // ========== SECTION: FOOTER & STICKY BAR ==========
    $cmb->add_field(array(
        'name' => 'Footer',
        'type' => 'title',
        'id'   => 'section_footer_title',
    ));

    $cmb->add_field(array(
        'name'    => 'Footer Line 1',
        'id'      => 'footer_line1',
        'type'    => 'text',
        'default' => '© Ellene Leya Masri · Miami, USA',
    ));

    $cmb->add_field(array(
        'name'    => 'Footer Line 2',
        'id'      => 'footer_line2',
        'type'    => 'text',
        'default' => 'Mayami, My Miami — a release campaign.',
    ));

    $cmb->add_field(array(
        'name'    => 'Sticky Bar (Mobile) - Stream',
        'id'      => 'sticky_stream_label',
        'type'    => 'text',
        'default' => '▶ Stream',
    ));

    $cmb->add_field(array(
        'name'    => 'Sticky Bar (Mobile) - Video',
        'id'      => 'sticky_video_label',
        'type'    => 'text',
        'default' => '◉ Video',
    ));

    $cmb->add_field(array(
        'name'    => 'Sticky Bar (Mobile) - TikTok',
        'id'      => 'sticky_tiktok_label',
        'type'    => 'text',
        'default' => 'TikTok',
    ));

    $cmb->add_field(array(
        'name' => 'Sticky Bar (Mobile) - TikTok Link',
        'id'   => 'sticky_tiktok_link',
        'type' => 'text_url',
    ));

    // ========== SECTION: MARQUEE ==========
    $cmb->add_field(array(
        'name' => 'Top-Bar',
        'type' => 'title',
        'id'   => 'section_marquee_title',
    ));
    $marquee_group = $cmb->add_field(array(
        'id'      => 'marquee_items',
        'type'    => 'group',
        'options' => array(
            'group_title'   => 'Item {#}',
            'add_button'    => '+ Ajouter',
            'remove_button' => 'Supprimer',
            'sortable'      => true,
        ),
    ));

    $cmb->add_group_field($marquee_group, array(
        'name' => 'Label',
        'id'   => 'label',
        'type' => 'text',
    ));

    $cmb->add_group_field($marquee_group, array(
        'name' => 'Lien',
        'id'   => 'href',
        'type' => 'text_url',
    ));

    $cmb->add_group_field($marquee_group, array(
        'name' => 'Masquer',
        'id'   => 'is_hidden',
        'type' => 'checkbox',
    ));

    $cmb->add_field(array(
        'name' => 'Visuel TOP-BAR',
        'id'   => 'marquee_logo_png',
        'type' => 'file',
        'text' => array(
            'add_upload_file_text' => 'Modifier',
        ),
    ));

    $cmb->add_field(array(
        'name' => 'Masquer',
        'id'   => 'marquee_logo_hidden',
        'type' => 'checkbox',
    ));
}
