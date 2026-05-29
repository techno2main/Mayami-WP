(function() {
  function protectMedia(root) {
    var scope = root || document;
    var media = scope.querySelectorAll('img, video');

    media.forEach(function(el) {
      el.setAttribute('draggable', 'false');
      el.setAttribute('data-mayami-protected-media', '1');
      if (el.tagName === 'VIDEO') {
        el.setAttribute('controlslist', 'nodownload noplaybackrate noremoteplayback');
        el.setAttribute('disablepictureinpicture', '');
      }
    });
  }

  function shouldBlockTarget(target) {
    return target && (target.tagName === 'IMG' || target.tagName === 'VIDEO');
  }

  document.addEventListener('contextmenu', function(event) {
    if (shouldBlockTarget(event.target)) {
      event.preventDefault();
    }
  }, true);

  document.addEventListener('dragstart', function(event) {
    if (shouldBlockTarget(event.target)) {
      event.preventDefault();
    }
  }, true);

  document.addEventListener('selectstart', function(event) {
    if (shouldBlockTarget(event.target)) {
      event.preventDefault();
    }
  }, true);

  document.addEventListener('keydown', function(event) {
    var key = String(event.key || '').toLowerCase();
    if ((event.ctrlKey || event.metaKey) && (key === 's' || key === 'u')) {
      event.preventDefault();
    }
  }, true);

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
      protectMedia(document);
    });
  } else {
    protectMedia(document);
  }

  var observer = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
      mutation.addedNodes.forEach(function(node) {
        if (node && node.nodeType === 1) {
          if (node.matches && (node.matches('img, video') || node.querySelector('img, video'))) {
            protectMedia(node);
          }
        }
      });
    });
  });

  observer.observe(document.documentElement, { childList: true, subtree: true });
})();