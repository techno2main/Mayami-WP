<?php
/**
 * Template part - Hero Slider
 * 
 * @package Mayami
 */

$hero_slider = cmb2_get_option('mayami_landing_options', 'hero_slider');
if (empty($hero_slider)) {
    $hero_slider = array(
        array('slide_type' => 'image', 'slide_image' => get_template_directory_uri() . '/assets/mayami-artist.jpg', 'alt_text' => 'Ellene Leya Masri — portrait 1'),
        array('slide_type' => 'image', 'slide_image' => get_template_directory_uri() . '/assets/mayami-cover.jpg', 'alt_text' => 'Ellene Leya Masri — portrait 2'),
        array('slide_type' => 'video', 'video_url' => 'https://www.youtube.com/watch?v=WiB_UoexqVo&pp=0gcJCQoLAYcqIYzv', 'alt_text' => 'Mayami official video'),
    );
}

$slide_count = is_array($hero_slider) ? count($hero_slider) : 0;
$show_navigation = $slide_count > 1;

function extract_youtube_id($url) {
    if (preg_match('/youtu\.be\/([^?&#]+)/', $url, $matches)) {
        return $matches[1];
    }
    if (preg_match('/[?&]v=([^&#]+)/', $url, $matches)) {
        return $matches[1];
    }
    if (preg_match('/embed\/([^?&#]+)/', $url, $matches)) {
        return $matches[1];
    }
    return 'WiB_UoexqVo';
}
?>
<div>
    <div class="relative mx-auto w-full max-w-md">
        <!-- Tape decorations -->
        <span class="tape -top-4 left-10 h-6 w-24"></span>
        <span class="tape -top-4 right-10 h-6 w-24 rotate-3!"></span>
        
        <div class="relative overflow-hidden rounded-3xl border-2 border-ink bg-ink" style="box-shadow: 12px 12px 0 var(--ink)">
            <div class="relative aspect-11/16 w-full">
                <!-- Slides -->
                <?php foreach ($hero_slider as $index => $slide): 
                    $is_active = $index === 0;
                    $slide_type = isset($slide['slide_type']) ? $slide['slide_type'] : 'image';
                    
                    if ($slide_type === 'video'):
                        $video_url = isset($slide['video_url']) ? $slide['video_url'] : '';
                        $video_id = extract_youtube_id($video_url);
                        $thumbnail_url = "https://i.ytimg.com/vi/{$video_id}/hqdefault.jpg";
                        $alt_text = isset($slide['alt_text']) ? $slide['alt_text'] : 'Video';
                        ?>
                        <div class="hero-slide <?php echo $is_active ? 'active' : ''; ?>" data-index="<?php echo $index; ?>" data-type="video" data-video-id="<?php echo esc_attr($video_id); ?>">
                            <img 
                                src="<?php echo esc_url($thumbnail_url); ?>" 
                                alt="<?php echo esc_attr($alt_text); ?>" 
                                width="1320" 
                                height="1920" 
                                class="block h-full w-full object-cover"
                            />
                            <button type="button" class="video-play-btn absolute inset-0 flex items-center justify-center bg-[oklch(0.15_0.08_280/0.35)]" aria-label="Lire la video dans le slider">
                                <span class="flex h-16 w-16 items-center justify-center rounded-full bg-cream text-2xl text-ink shadow-[4px_4px_0_var(--magenta)]">▶</span>
                            </button>
                        </div>
                    <?php else: 
                        $image_url = isset($slide['slide_image']) ? $slide['slide_image'] : '';
                        $alt_text = isset($slide['alt_text']) ? $slide['alt_text'] : 'Image';
                        ?>
                        <div class="hero-slide <?php echo $is_active ? 'active' : ''; ?>" data-index="<?php echo $index; ?>">
                            <img 
                                src="<?php echo esc_url($image_url); ?>" 
                                alt="<?php echo esc_attr($alt_text); ?>" 
                                width="1320" 
                                height="1920" 
                                class="block h-full w-full object-cover"
                            />
                        </div>
                    <?php endif; 
                endforeach; ?>

                <!-- Video iframe container (hidden by default) -->
                <div id="video-iframe-container" class="absolute inset-0 hidden"></div>
            </div>

            <!-- Bottom bar -->
            <div class="flex items-center justify-between gap-3 border-t-2 border-ink bg-cream px-4 py-3 sm:pl-20">
                <span class="whitespace-nowrap font-poster text-[10px] uppercase tracking-[0.25em] text-ink">Richard Bona &amp; Ellene Masri</span>
                <span class="whitespace-nowrap font-poster text-[10px] uppercase tracking-[0.25em] text-ink">Mayami, My Miami</span>
            </div>
        </div>

        <?php if ($show_navigation): ?>
        <!-- Pagination dots -->
        <div class="mt-4 flex justify-center gap-2">
            <?php foreach ($hero_slider as $index => $slide): ?>
                <button 
                    type="button" 
                    class="slider-dot h-2.5 w-2.5 rounded-full border border-ink transition <?php echo $index === 0 ? 'bg-ink' : 'bg-cream'; ?>" 
                    data-index="<?php echo $index; ?>" 
                    aria-label="Aller au slide <?php echo $index + 1; ?>">
                </button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- "New Drop" badge -->
        <span class="absolute -bottom-5 -left-5 hidden h-16 w-16 rotate-12 items-center justify-center rounded-full border-2 border-ink bg-aqua font-poster text-[10px] uppercase text-ink shadow-[4px_4px_0_var(--ink)] sm:flex">
            New<br />Drop
        </span>
    </div>
</div>

<style>
.hero-slide {
    display: none;
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}
.hero-slide.active {
    display: block;
}
</style>

<script>
(function() {
    const slides = document.querySelectorAll('.hero-slide');
    const dots = document.querySelectorAll('.slider-dot');
    const videoContainer = document.getElementById('video-iframe-container');
    let currentIndex = 0;

    function goToSlide(index) {
        // Hide video if playing
        videoContainer.innerHTML = '';
        videoContainer.classList.add('hidden');

        // Update slides
        slides.forEach((slide, i) => {
            slide.classList.toggle('active', i === index);
        });

        // Update dots
        dots.forEach((dot, i) => {
            if (i === index) {
                dot.classList.remove('bg-cream');
                dot.classList.add('bg-ink');
            } else {
                dot.classList.remove('bg-ink');
                dot.classList.add('bg-cream');
            }
        });

        currentIndex = index;
    }

    // Dots
    if (dots.length > 0 && slides.length > 1) {
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => goToSlide(index));
        });
    }

    // Video play buttons
    document.querySelectorAll('.video-play-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const slide = this.closest('.hero-slide');
            const videoId = slide.dataset.videoId;
            const embedUrl = `https://www.youtube-nocookie.com/embed/${encodeURIComponent(videoId)}?autoplay=1&rel=0&playsinline=1`;
            
            videoContainer.innerHTML = `<iframe
                title="Mayami video"
                src="${embedUrl}"
                width="100%"
                height="100%"
                class="absolute inset-0 h-full w-full"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                allowfullscreen
            ></iframe>`;
            videoContainer.classList.remove('hidden');
        });
    });
})();
</script>
