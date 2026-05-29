<?php
/**
 * Front Page Template - Mayami Landing Page
 * 
 * @package Mayami
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<main class="relative overflow-x-clip">
    <!-- Sticky Marquee Top -->
    <div class="sticky top-0 z-60">
        <?php get_template_part('template-parts/sections/hero-marquee'); ?>
    </div>
    
    <!-- Hero Section -->
    <?php get_template_part('template-parts/sections/hero'); ?>
    
    <!-- Stream Section -->
    <?php get_template_part('template-parts/sections/stream'); ?>
    
    <!-- Social Section -->
    <?php get_template_part('template-parts/sections/social'); ?>
    
    <!-- Video Section -->
    <?php get_template_part('template-parts/sections/video'); ?>
    
    <!-- Release Info Section -->
    <?php get_template_part('template-parts/sections/release-info'); ?>
    
    <!-- CTA Section -->
    <?php get_template_part('template-parts/sections/cta'); ?>
    
    <!-- Footer Section -->
    <?php get_template_part('template-parts/sections/footer-section'); ?>
    
    <!-- Sticky Bar Mobile -->
    <?php get_template_part('template-parts/sections/sticky-bar'); ?>
</main>

<?php wp_footer(); ?>
</body>
</html>
