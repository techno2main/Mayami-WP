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
$slider_uid = 'hero-slider-' . wp_unique_id();
$has_tiktok_slide = false;

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
<div id="<?php echo esc_attr($slider_uid); ?>" class="hero-slider-root">
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
                    $slide_duration_seconds = isset($slide['slide_duration']) ? max(1, intval($slide['slide_duration'])) : 5;
                    $slide_delay_ms = $slide_duration_seconds * 1000;
                    
                    if ($slide_type === 'video'):
                        $video_url = isset($slide['video_url']) ? $slide['video_url'] : '';
                        $video_id = extract_youtube_id($video_url);
                        $player_dom_id = $slider_uid . '-yt-' . $index;
                        ?>
                        <div class="hero-slide <?php echo $is_active ? 'active' : ''; ?>" data-index="<?php echo $index; ?>" data-delay="<?php echo esc_attr($slide_delay_ms); ?>" data-type="video" data-video-id="<?php echo esc_attr($video_id); ?>">
                            <div id="<?php echo esc_attr($player_dom_id); ?>" class="hero-youtube-player absolute inset-0 h-full w-full"></div>
                        </div>
                    <?php elseif ($slide_type === 'tiktok'):
                        $tiktok_url = isset($slide['tiktok_url']) ? $slide['tiktok_url'] : '';
                        $tiktok_cite = !empty($tiktok_url) ? $tiktok_url : 'https://www.tiktok.com/@ellenemasri/video/7645173351501008141';
                        $tiktok_video_url = isset($slide['tiktok_video_url']) ? $slide['tiktok_video_url'] : '';
                        $has_tiktok_slide = true;
                        ?>
                        <div class="hero-slide <?php echo $is_active ? 'active' : ''; ?>" data-index="<?php echo $index; ?>" data-delay="<?php echo esc_attr($slide_delay_ms); ?>" data-type="tiktok">
                            <div class="absolute inset-0 overflow-hidden bg-black">
                                <?php if (!empty($tiktok_video_url)): ?>
                                    <video
                                        class="hero-tiktok-video"
                                        src="<?php echo esc_url($tiktok_video_url); ?>"
                                        poster="<?php echo esc_url(isset($slide['slide_image']) ? $slide['slide_image'] : ''); ?>"
                                        playsinline
                                        preload="metadata"
                                        controlslist="nodownload noplaybackrate noremoteplayback"
                                        disablepictureinpicture
                                    ></video>
                                    <button
                                        type="button"
                                        class="hero-tiktok-audio-btn"
                                        aria-label="Activer le son"
                                        aria-pressed="false"
                                    >
                                        🔇
                                    </button>
                                <?php else: ?>
                                    <blockquote class="tiktok-embed hero-tiktok-embed m-0" cite="<?php echo esc_url($tiktok_cite); ?>" data-video-id="7645173351501008141" data-embed-from="oembed">
                                        <section class="h-full w-full">
                                            <a target="_blank" rel="noreferrer" title="@ellenemasri" href="https://www.tiktok.com/@ellenemasri?refer=embed">@ellenemasri</a>
                                            IT’S OUT!!! 🌴🔥 “Mayami, My Miami” with legendary @richardbona is officially available everywhere! ❤️ Dreams do come true! If you love the song, use it in your Reels and Stories and tag me!!! I’ll be reposting my favorites! 🎶✨ Stream it now and let me know what you think!
                                            <a title="MayamiMyMiami" target="_blank" rel="noreferrer" href="https://www.tiktok.com/tag/MayamiMyMiami?refer=embed">#MayamiMyMiami</a>
                                            <a title="RichardBona" target="_blank" rel="noreferrer" href="https://www.tiktok.com/tag/RichardBona?refer=embed">#RichardBona</a>
                                            <a title="ElleneMasri" target="_blank" rel="noreferrer" href="https://www.tiktok.com/tag/ElleneMasri?refer=embed">#ElleneMasri</a>
                                            <a title="OutNow" target="_blank" rel="noreferrer" href="https://www.tiktok.com/tag/OutNow?refer=embed">#OutNow</a>
                                            <a title="Miami" target="_blank" rel="noreferrer" href="https://www.tiktok.com/tag/Miami?refer=embed">#Miami</a>
                                            <a target="_blank" rel="noreferrer" title="♬ Mayami, My Miami - Richard Bona &amp; Ellene Masri" href="https://www.tiktok.com/music/Mayami-My-Miami-7642043888069052432?refer=embed">♬ Mayami, My Miami - Richard Bona &amp; Ellene Masri</a>
                                        </section>
                                    </blockquote>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: 
                        $image_url = isset($slide['slide_image']) ? $slide['slide_image'] : '';
                        $alt_text = isset($slide['alt_text']) ? $slide['alt_text'] : 'Image';
                        ?>
                        <div class="hero-slide <?php echo $is_active ? 'active' : ''; ?>" data-index="<?php echo $index; ?>" data-delay="<?php echo esc_attr($slide_delay_ms); ?>">
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

                <?php if ($show_navigation): ?>
                <button
                    type="button"
                    class="slider-arrow slider-arrow-prev"
                    aria-label="Slide précédent">
                    <span aria-hidden="true">&#x2039;</span>
                </button>
                <button
                    type="button"
                    class="slider-arrow slider-arrow-next"
                    aria-label="Slide suivant">
                    <span aria-hidden="true">&#x203A;</span>
                </button>
                <?php endif; ?>
            </div>

            <?php if ($has_tiktok_slide): ?>
                <script async src="https://www.tiktok.com/embed.js"></script>
            <?php endif; ?>

            <!-- Bottom bar -->
            <div class="flex items-center justify-between gap-3 border-t-2 border-ink bg-cream px-4 py-3">
                <a
                    href="#stream"
                    class="inline-flex h-8 w-8 items-center justify-center rounded-full border-2 border-ink bg-white text-sm text-ink transition hover:-translate-y-0.5"
                    aria-label="Aller a la section Stream">
                    <span aria-hidden="true">&#9654;</span>
                </a>
                <span class="whitespace-nowrap font-poster text-[10px] uppercase tracking-[0.25em] text-ink">Mayami, My Miami - 2026</span>
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
.hero-slide[data-type="video"],
.hero-slide[data-type="tiktok"] {
    background: #000;
}
.hero-tiktok-embed {
    position: absolute;
    inset: 0;
    width: 100% !important;
    height: 100% !important;
    min-width: 100% !important;
    max-width: none !important;
    margin: 0 !important;
    overflow: hidden;
}
.hero-tiktok-video {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    background: #000;
}
.hero-tiktok-audio-btn {
    position: absolute;
    right: 12px;
    bottom: 12px;
    z-index: 25;
    display: inline-flex;
    height: 38px;
    width: 38px;
    align-items: center;
    justify-content: center;
    border: 2px solid var(--ink);
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.92);
    color: var(--ink);
    font-size: 18px;
    line-height: 1;
    cursor: pointer;
    transition: transform .15s ease, background .15s ease;
}
.hero-tiktok-audio-btn:hover {
    transform: translateY(-1px);
    background: #fff;
}
.hero-tiktok-embed section {
    width: 100%;
    height: 100%;
}
.hero-tiktok-embed iframe {
    width: 100% !important;
    height: 100% !important;
    border: 0 !important;
    display: block !important;
}
.hero-youtube-player iframe {
    width: 100% !important;
    height: 100% !important;
    border: 0 !important;
    display: block !important;
}
.slider-arrow {
    position: absolute;
    top: 50%;
    z-index: 20;
    display: flex;
    height: 48px;
    width: 48px;
    transform: translateY(-50%);
    align-items: center;
    justify-content: center;
    border: 2px solid var(--ink);
    border-radius: 999px;
    background: #fff;
    color: var(--ink);
    font-size: 32px;
    line-height: 1;
    box-shadow: 3px 3px 0 var(--ink);
    cursor: pointer;
    opacity: 0.92;
    transition: transform .15s ease, opacity .15s ease;
}
.slider-arrow:hover {
    transform: translateY(-50%) scale(1.06);
    opacity: 1;
}
.slider-arrow-prev {
    left: 14px;
}
.slider-arrow-next {
    right: 14px;
}

@media (min-width: 1024px) {
    .hero-slider-root .slider-arrow {
        opacity: 0;
        pointer-events: none;
    }

    .hero-slider-root:hover .slider-arrow,
    .hero-slider-root:focus-within .slider-arrow {
        opacity: 0.92;
        pointer-events: auto;
    }
}
</style>

<script>
(function() {
    const root = document.getElementById('<?php echo esc_js($slider_uid); ?>');
    if (!root || root.dataset.sliderInit === '1') {
        return;
    }
    root.dataset.sliderInit = '1';

    const slides = root.querySelectorAll('.hero-slide');
    const dots = root.querySelectorAll('.slider-dot');
    const prevButton = root.querySelector('.slider-arrow-prev');
    const nextButton = root.querySelector('.slider-arrow-next');
    let currentIndex = 0;
    let autoPlayTimer = null;
    const DEFAULT_AUTOPLAY_DELAY = 5000;
    const youtubePlayers = {};

    function getSlideDelay(index) {
        const slide = slides[index];
        if (!slide) {
            return DEFAULT_AUTOPLAY_DELAY;
        }

        const rawDelay = parseInt(slide.dataset.delay || '', 10);
        if (Number.isNaN(rawDelay) || rawDelay < 1000) {
            return DEFAULT_AUTOPLAY_DELAY;
        }

        return rawDelay;
    }

    function stopAutoPlay() {
        if (autoPlayTimer) {
            clearTimeout(autoPlayTimer);
            autoPlayTimer = null;
        }
    }

    function startAutoPlay() {
        if (slides.length <= 1 || !shouldUseTimedAutoplay(currentIndex)) {
            stopAutoPlay();
            return;
        }

        stopAutoPlay();
        autoPlayTimer = setTimeout(() => {
            goToSlide((currentIndex + 1) % slides.length);
            startAutoPlay();
        }, getSlideDelay(currentIndex));
    }

    function goToSlide(index) {
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

        syncActiveVideoPlayback(index);

        if (shouldUseTimedAutoplay(index)) {
            startAutoPlay();
        } else {
            stopAutoPlay();
        }
    }

    function ensureYouTubeApi(callback) {
        if (window.YT && typeof window.YT.Player === 'function') {
            callback();
            return;
        }

        window.__mayamiYouTubeReadyCallbacks = window.__mayamiYouTubeReadyCallbacks || [];
        window.__mayamiYouTubeReadyCallbacks.push(callback);

        if (window.__mayamiYouTubeApiLoading) {
            return;
        }

        window.__mayamiYouTubeApiLoading = true;
        const previousCallback = window.onYouTubeIframeAPIReady;

        window.onYouTubeIframeAPIReady = function() {
            if (typeof previousCallback === 'function') {
                previousCallback();
            }

            const queue = window.__mayamiYouTubeReadyCallbacks || [];
            window.__mayamiYouTubeReadyCallbacks = [];
            queue.forEach(fn => {
                if (typeof fn === 'function') {
                    fn();
                }
            });
        };

        const script = document.createElement('script');
        script.src = 'https://www.youtube.com/iframe_api';
        script.async = true;
        document.head.appendChild(script);
    }

    function initYouTubePlayers() {
        if (!(window.YT && typeof window.YT.Player === 'function')) {
            return;
        }

        slides.forEach((slide, index) => {
            if (slide.dataset.type !== 'video' || youtubePlayers[index]) {
                return;
            }

            const holder = slide.querySelector('.hero-youtube-player');
            const videoId = slide.dataset.videoId;
            if (!holder || !videoId) {
                return;
            }

            youtubePlayers[index] = new window.YT.Player(holder, {
                videoId: videoId,
                playerVars: {
                    autoplay: 0,
                    rel: 0,
                    controls: 1,
                    playsinline: 1,
                    modestbranding: 1,
                },
                events: {
                    onStateChange: function(event) {
                        if (!window.YT || event.data !== window.YT.PlayerState.ENDED) {
                            return;
                        }

                        if (index !== currentIndex || slides.length <= 1) {
                            return;
                        }

                        goToSlide((currentIndex + 1) % slides.length);
                    },
                },
            });
        });
    }

    function syncYouTubePlayback(index) {
        Object.keys(youtubePlayers).forEach((key) => {
            const player = youtubePlayers[key];
            const playerIndex = parseInt(key, 10);
            if (!player || Number.isNaN(playerIndex)) {
                return;
            }

            try {
                if (playerIndex === index) {
                    player.seekTo(0, true);
                    player.playVideo();
                } else {
                    player.stopVideo();
                }
            } catch (e) {
                // Ignore player API timing issues.
            }
        });
    }

    function syncActiveVideoPlayback(index) {
        syncYouTubePlayback(index);

        slides.forEach((slide, i) => {
            const video = slide.querySelector('video.hero-tiktok-video');
            if (!video) {
                return;
            }

            if (i === index) {
                video.currentTime = 0;
                video.muted = false;
                const playPromise = video.play();
                if (playPromise && typeof playPromise.catch === 'function') {
                    playPromise.catch(() => {
                        video.muted = true;
                        updateAudioButtonState(video);
                        const retryPromise = video.play();
                        if (retryPromise && typeof retryPromise.catch === 'function') {
                            retryPromise.catch(() => {});
                        }
                    });
                }
                updateAudioButtonState(video);
            } else {
                video.pause();
                video.currentTime = 0;
                video.muted = true;
                updateAudioButtonState(video);
            }
        });
    }

    function updateAudioButtonState(video) {
        const slide = video.closest('.hero-slide');
        if (!slide) {
            return;
        }

        const button = slide.querySelector('.hero-tiktok-audio-btn');
        if (!button) {
            return;
        }

        if (video.muted) {
            button.textContent = '🔇';
            button.setAttribute('aria-label', 'Activer le son');
            button.setAttribute('aria-pressed', 'false');
        } else {
            button.textContent = '🔊';
            button.setAttribute('aria-label', 'Couper le son');
            button.setAttribute('aria-pressed', 'true');
        }
    }

    function initTikTokAudioControls() {
        root.querySelectorAll('video.hero-tiktok-video').forEach(video => {
            updateAudioButtonState(video);

            const slide = video.closest('.hero-slide');
            if (!slide) {
                return;
            }

            const button = slide.querySelector('.hero-tiktok-audio-btn');
            if (!button || button.dataset.bound === '1') {
                return;
            }

            button.dataset.bound = '1';
            button.addEventListener('click', () => {
                video.muted = !video.muted;
                updateAudioButtonState(video);

                if (!video.paused) {
                    const playPromise = video.play();
                    if (playPromise && typeof playPromise.catch === 'function') {
                        playPromise.catch(() => {});
                    }
                }
            });

            if (video.dataset.endedBound !== '1') {
                video.dataset.endedBound = '1';
                video.addEventListener('ended', () => {
                    const slide = video.closest('.hero-slide');
                    if (!slide || slides.length <= 1) {
                        return;
                    }

                    const endedIndex = parseInt(slide.dataset.index || '', 10);
                    if (Number.isNaN(endedIndex) || endedIndex !== currentIndex) {
                        return;
                    }

                    goToSlide((currentIndex + 1) % slides.length);
                });
            }
        });
    }

    function isTimedTikTokEmbedSlide(index) {
        const slide = slides[index];
        if (!slide) {
            return false;
        }

        return slide.dataset.type === 'tiktok' && !slide.querySelector('video.hero-tiktok-video');
    }

    function shouldUseTimedAutoplay(index) {
        const slide = slides[index];
        if (!slide) {
            return true;
        }

        if (slide.dataset.type === 'video') {
            return false;
        }

        if (slide.dataset.type === 'tiktok') {
            return isTimedTikTokEmbedSlide(index);
        }

        return true;
    }

    // Dots
    if (dots.length > 0 && slides.length > 1) {
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                goToSlide(index);
            });
        });
    }

    syncActiveVideoPlayback(currentIndex);
    initTikTokAudioControls();

    if (root.querySelector('.hero-slide[data-type="video"]')) {
        ensureYouTubeApi(() => {
            initYouTubePlayers();
            syncYouTubePlayback(currentIndex);
        });
    }

    // Arrows
    if (slides.length > 1) {
        if (prevButton) {
            prevButton.addEventListener('click', () => {
                goToSlide((currentIndex - 1 + slides.length) % slides.length);
            });
        }

        if (nextButton) {
            nextButton.addEventListener('click', () => {
                goToSlide((currentIndex + 1) % slides.length);
            });
        }

        startAutoPlay();
    }
})();
</script>
