/**
 * Stream Platform Player Toggle
 */
document.addEventListener('DOMContentLoaded', function() {
  const platformLinks = document.querySelectorAll('.platform-card');
  let activePlatform = null;

  const openPlatform = function(platformName, sourceCard) {
    const mobilePlayer = document.getElementById(`player-mobile-${platformName}`);
    const desktopPlayer = document.getElementById(`player-desktop-${platformName}`);

    if (!mobilePlayer || !desktopPlayer) return false;

    if (activePlatform === platformName) {
      mobilePlayer.classList.remove('is-active');
      desktopPlayer.classList.remove('is-active');
      if (sourceCard) {
        sourceCard.setAttribute('aria-expanded', 'false');
      }
      activePlatform = null;
      return true;
    }

    document.querySelectorAll('.platform-player-mobile').forEach(player => {
      player.classList.remove('is-active');
    });
    document.querySelectorAll('.platform-player-desktop').forEach(player => {
      player.classList.remove('is-active');
    });
    platformLinks.forEach(card => {
      card.setAttribute('aria-expanded', 'false');
    });

    mobilePlayer.classList.add('is-active');
    desktopPlayer.classList.add('is-active');
    if (sourceCard) {
      sourceCard.setAttribute('aria-expanded', 'true');
    }
    activePlatform = platformName;

    requestAnimationFrame(() => {
      const isMobile = window.matchMedia('(max-width: 639px)').matches;
      if (isMobile) {
        mobilePlayer.scrollIntoView({ behavior: 'smooth', block: 'start' });
      } else {
        desktopPlayer.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });

    return true;
  };

  platformLinks.forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault();

      const platformName = this.dataset.platform;
      openPlatform(platformName, this);
    });
  });

  // Internal anchors: smooth scroll and keep base URL without hash.
  document.addEventListener('click', function(e) {
    const anchor = e.target.closest('a[href^="#"]');
    if (!anchor) return;

    const href = anchor.getAttribute('href');
    if (!href || href === '#') return;

    // Keep native browser behavior for new tab/window actions.
    if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) {
      return;
    }

    const target = document.querySelector(href);
    if (!target) return;

    e.preventDefault();
    target.scrollIntoView({ behavior: 'smooth', block: 'start' });

    const requestedPlatform = anchor.getAttribute('data-open-platform');
    if (requestedPlatform) {
      const matchingCard = document.querySelector(`.platform-card[data-platform="${requestedPlatform}"]`);
      if (matchingCard) {
        setTimeout(() => {
          openPlatform(requestedPlatform, matchingCard);
        }, 250);
      }
    }

    const cleanUrl = window.location.pathname + window.location.search;
    window.history.replaceState(null, '', cleanUrl);
  });

  // Marquee Spotify: open an inline player without leaving the page.
  const spotifyToggles = document.querySelectorAll('.js-marquee-spotify-toggle');
  const spotifyPanel = document.getElementById('marquee-spotify-player');
  const spotifyIframe = spotifyPanel ? spotifyPanel.querySelector('iframe') : null;

  if (spotifyToggles.length > 0) {
    const toggleSpotifyPanel = function(toggleElement) {
      // Fallback: if no inline player exists, open configured Spotify URL.
      if (!spotifyPanel || !spotifyIframe) {
        const spotifyUrl = toggleElement.getAttribute('data-spotify-url');
        if (spotifyUrl) {
          window.open(spotifyUrl, '_blank', 'noopener,noreferrer');
        }
        return;
      }

      const isActive = spotifyPanel.classList.contains('is-active');

      if (isActive) {
        spotifyPanel.classList.remove('is-active');
        spotifyToggles.forEach(toggle => toggle.setAttribute('aria-expanded', 'false'));
        spotifyIframe.setAttribute('src', '');
        return;
      }

      spotifyPanel.classList.add('is-active');
      spotifyToggles.forEach(toggle => toggle.setAttribute('aria-expanded', 'true'));

      const baseSrc = spotifyIframe.getAttribute('data-src') || '';
      if (baseSrc) {
        const autoplaySrc = `${baseSrc}${baseSrc.includes('?') ? '&' : '?'}autoplay=1`;
        spotifyIframe.setAttribute('src', autoplaySrc);
        spotifyPanel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
      }
    };

    spotifyToggles.forEach(toggle => {
      toggle.addEventListener('click', function() {
        toggleSpotifyPanel(toggle);
      });
    });
  }

  // Mobile marquee burger menu
  const marqueeMenuToggle = document.querySelector('.js-marquee-mobile-toggle');
  const marqueeMobilePanel = document.getElementById('hero-marquee-mobile-panel');

  if (marqueeMenuToggle && marqueeMobilePanel) {
    marqueeMenuToggle.addEventListener('click', function() {
      const isOpen = marqueeMobilePanel.classList.contains('is-open');
      marqueeMobilePanel.classList.toggle('is-open', !isOpen);
      marqueeMobilePanel.setAttribute('aria-hidden', isOpen ? 'true' : 'false');
      marqueeMenuToggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
    });

    marqueeMobilePanel.addEventListener('click', function(e) {
      const clicked = e.target.closest('a,button');
      if (!clicked) return;

      if (clicked.classList.contains('js-marquee-spotify-toggle')) {
        return;
      }

      marqueeMobilePanel.classList.remove('is-open');
      marqueeMobilePanel.setAttribute('aria-hidden', 'true');
      marqueeMenuToggle.setAttribute('aria-expanded', 'false');
    });

    document.addEventListener('click', function(e) {
      if (!marqueeMobilePanel.classList.contains('is-open')) return;

      const inMenu = e.target.closest('#hero-marquee-mobile, #hero-marquee-mobile-panel');
      if (inMenu) return;

      marqueeMobilePanel.classList.remove('is-open');
      marqueeMobilePanel.setAttribute('aria-hidden', 'true');
      marqueeMenuToggle.setAttribute('aria-expanded', 'false');
    });
  }
});
