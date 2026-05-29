<?php
/**
 * Template part - Footer Section
 * 
 * @package Mayami
 */

$footer_line1 = cmb2_get_option('mayami_landing_options', 'footer_line1') ?: '© Ellene Leya Masri · Miami, USA';
$footer_line2 = cmb2_get_option('mayami_landing_options', 'footer_line2') ?: 'Mayami, My Miami — a release campaign.';
?>
<footer id="footer" class="mayami-legal-footer bg-ink py-10 text-center text-cream/70">
    <div class="mb-4 flex justify-center">
        <a href="#hero" aria-label="Retour tout en haut" class="inline-flex items-center justify-center text-xl leading-none text-cream/80 transition hover:text-aqua">↑</a>
    </div>
    <p class="font-poster text-xs uppercase tracking-[0.3em]"><?php echo esc_html($footer_line1); ?></p>
    <p class="mt-2 text-xs"><?php echo esc_html($footer_line2); ?></p>
</footer>

<style>
@media (max-width: 767px) {
    .mayami-legal-footer {
        padding-bottom: calc(7.25rem + env(safe-area-inset-bottom));
    }
}
</style>
