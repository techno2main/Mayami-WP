/**
 * Admin Navigation Sticky - Mayami Landing
 */
(function() {
  'use strict';

  const SECTION_IDS = [
    'section_marquee_title',
    'section_hero_title',
    'section_slider_title',
    'section_stream_title',
    'section_social_title',
    'section_video_title',
    'section_release_title',
    'section_cta_title',
    'section_footer_title',
    'section_links_title'
  ];

  let isOverviewMode = true;

  // Attendre que le DOM soit chargé
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  function init() {
    // Vérifier qu'on est sur la page admin Mayami
    if (!document.querySelector('.cmb2-id-section-hero-title')) return;

    removeNativePageHeading();
    setTimeout(removeNativePageHeading, 100);
    setTimeout(removeNativePageHeading, 500);
    setTimeout(removeNativePageHeading, 1200);
    createStickyNav();
    reorderSectionsInDom();
    setupAccordion();
    styleBottomSaveButtons();
    addSmoothScroll();
  }

  function reorderSectionsInDom() {
    const firstSection = SECTION_IDS
      .map(getSectionTitleElement)
      .find(function(el) { return !!el; });

    if (!firstSection || !firstSection.parentNode) {
      return;
    }

    const container = firstSection.parentNode;
    const fragment = document.createDocumentFragment();

    SECTION_IDS.forEach(function(sectionId) {
      const titleEl = getSectionTitleElement(sectionId);
      if (!titleEl) {
        return;
      }

      const blockRows = [titleEl].concat(getSectionContentRows(sectionId));
      blockRows.forEach(function(row) {
        fragment.appendChild(row);
      });
    });

    container.appendChild(fragment);
  }

  function removeNativePageHeading() {
    const wrap = document.querySelector('.wrap');
    if (!wrap) return;

    const mainHeading = wrap.querySelector('h1');
    if (mainHeading) {
      mainHeading.remove();
    }

    const inlineHeading = wrap.querySelector('.wp-heading-inline');
    if (inlineHeading) {
      inlineHeading.remove();
    }
  }

  function createStickyNav() {
    const sections = [
      { id: 'section_marquee_title', label: 'TOP-BAR' },
      { id: 'section_hero_title', label: 'Hero' },
      { id: 'section_slider_title', label: 'Slider' },
      { id: 'section_stream_title', label: 'Stream' },
      { id: 'section_social_title', label: 'Social' },
      { id: 'section_video_title', label: 'Video' },
      { id: 'section_release_title', label: 'Release' },
      { id: 'section_cta_title', label: 'CTA' },
      { id: 'section_footer_title', label: 'Footer' },
      { id: 'section_links_title', label: 'Links' }
    ];

    // Trouver le conteneur du formulaire
    const formContainer = document.querySelector('.cmb2-wrap');
    if (!formContainer) return;

    // Créer la navigation
    const nav = document.createElement('div');
    nav.id = 'mayami-admin-nav';
    nav.className = 'mayami-admin-nav';
    
    const navInner = document.createElement('div');
    navInner.className = 'mayami-admin-nav-inner';

    // Titre
    const title = document.createElement('a');
    title.href = '#';
    title.className = 'mayami-admin-home';
    title.setAttribute('title', 'Mayami Landing Settings');
    title.setAttribute('aria-label', 'Mayami Landing Settings');
    title.innerHTML = '<span class="dashicons dashicons-admin-generic" aria-hidden="true"></span>';
    title.style.cssText = 'display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; color:#fff; text-decoration:none; border-radius:999px; border:2px solid rgba(255,255,255,0.2); background:rgba(255,255,255,0.08); transition:all 0.2s ease;';
    title.addEventListener('click', function(e) {
      e.preventDefault();
      isOverviewMode = true;
      closeAllSections();
      clearActiveButtons();

      const nav = document.getElementById('mayami-admin-nav');
      if (!nav) return;

      const navTop = nav.getBoundingClientRect().top + window.pageYOffset;
      const offset = 20;
      window.scrollTo({
        top: Math.max(navTop - offset, 0),
        behavior: 'smooth'
      });
    });
    title.addEventListener('mouseenter', function() {
      this.style.background = 'rgba(255,255,255,0.16)';
      this.style.borderColor = 'rgba(255,255,255,0.35)';
      this.style.transform = 'translateY(-1px)';
    });
    title.addEventListener('mouseleave', function() {
      this.style.background = 'rgba(255,255,255,0.08)';
      this.style.borderColor = 'rgba(255,255,255,0.2)';
      this.style.transform = 'translateY(0)';
    });
    navInner.appendChild(title);

    // Container des boutons
    const buttonsContainer = document.createElement('div');
    buttonsContainer.className = 'mayami-admin-nav-buttons';

    sections.forEach(section => {
      const sectionEl = document.querySelector('.cmb2-id-' + section.id.replace(/_/g, '-'));
      if (!sectionEl) return;

      const btn = document.createElement('a');
      btn.href = '#' + section.id;
      btn.textContent = section.label;
      btn.className = 'mayami-nav-btn';
      btn.dataset.section = section.id;

      btn.addEventListener('click', function(e) {
        e.preventDefault();
        isOverviewMode = false;
        openSection(section.id);
        scrollToSection(section.id);
        setActiveButton(btn);
      });

      buttonsContainer.appendChild(btn);
    });

    navInner.appendChild(buttonsContainer);
    
    // Bouton Enregistrer à droite
    const saveButton = document.createElement('button');
    saveButton.type = 'button';
    saveButton.className = 'mayami-save-btn';
    saveButton.innerHTML = '💾 Enregistrer';
    saveButton.style.cssText = 'background: #fff; color: #6b21a8; border: 2px solid #fff; padding: 8px 20px; font-size: 13px; font-weight: 700; border-radius: 6px; cursor: pointer; margin-left: auto; text-transform: uppercase; letter-spacing: 0.5px; transition: all 0.2s;';
    
    saveButton.addEventListener('click', function(e) {
      e.preventDefault();
      const realSubmit = document.querySelector('.cmb-form input[type="submit"], .cmb2-wrap input[type="submit"]');
      if (realSubmit) {
        realSubmit.click();
      }
    });
    
    saveButton.addEventListener('mouseenter', function() {
      this.style.background = '#f0f0f1';
      this.style.transform = 'translateY(-1px)';
      this.style.boxShadow = '0 3px 8px rgba(0,0,0,0.2)';
    });
    
    saveButton.addEventListener('mouseleave', function() {
      this.style.background = '#fff';
      this.style.transform = 'translateY(0)';
      this.style.boxShadow = 'none';
    });
    
    navInner.appendChild(saveButton);
    nav.appendChild(navInner);

    // Insérer avant le formulaire
    formContainer.parentNode.insertBefore(nav, formContainer);

    // Observer le scroll pour mettre à jour le bouton actif
    observeSections(sections);
  }

  function setupAccordion() {
    SECTION_IDS.forEach(sectionId => {
      const sectionTitle = getSectionTitleElement(sectionId);
      if (sectionTitle) {
        sectionTitle.setAttribute('data-mayami-section', sectionId);
        makeSectionTitleInteractive(sectionId);
      }
      closeSection(sectionId);
    });
  }

  function makeSectionTitleInteractive(sectionId) {
    const sectionTitle = getSectionTitleElement(sectionId);
    if (!sectionTitle || sectionTitle.dataset.mayamiClickable === '1') {
      return;
    }

    sectionTitle.dataset.mayamiClickable = '1';
    sectionTitle.setAttribute('role', 'button');
    sectionTitle.setAttribute('tabindex', '0');
    sectionTitle.setAttribute('aria-expanded', 'false');
    sectionTitle.classList.add('mayami-section-toggle');
    injectSectionEyeIcon(sectionTitle);

    sectionTitle.addEventListener('click', function(e) {
      if (e.target && e.target.closest('a, button, input, select, textarea')) {
        return;
      }

      toggleSection(sectionId);
    });

    sectionTitle.addEventListener('keydown', function(e) {
      if (e.key !== 'Enter' && e.key !== ' ') {
        return;
      }

      e.preventDefault();
      toggleSection(sectionId);
    });
  }

  function injectSectionEyeIcon(sectionTitle) {
    const iconHost = sectionTitle.querySelector('.cmb-th') || sectionTitle;
    if (!iconHost || iconHost.querySelector('.mayami-eye-indicator')) {
      return;
    }

    const eye = document.createElement('span');
    eye.className = 'mayami-eye-indicator dashicons dashicons-visibility';
    eye.setAttribute('aria-hidden', 'true');
    iconHost.appendChild(eye);
  }

  function styleBottomSaveButtons() {
    const selectors = [
      '.cmb2-wrap input[type="submit"]',
      '.cmb-form input[type="submit"]',
      '.wrap p.submit input[type="submit"]',
      '.wrap .cmb2-wrap .button.button-primary'
    ];

    const buttons = document.querySelectorAll(selectors.join(','));
    buttons.forEach(function(btn) {
      if (btn.classList.contains('mayami-save-btn')) {
        return;
      }

      btn.style.background = '#fff';
      btn.style.color = '#6b21a8';
      btn.style.border = '2px solid #fff';
      btn.style.padding = '8px 20px';
      btn.style.fontSize = '13px';
      btn.style.fontWeight = '700';
      btn.style.borderRadius = '6px';
      btn.style.cursor = 'pointer';
      btn.style.textTransform = 'uppercase';
      btn.style.letterSpacing = '0.5px';
      btn.style.boxShadow = 'none';
      btn.style.transition = 'all 0.2s ease';

      if (btn.tagName === 'INPUT') {
        btn.value = '💾 Enregistrer';
      } else {
        btn.textContent = '💾 Enregistrer';
      }

      if (btn.dataset.mayamiSaveStyled === '1') {
        return;
      }

      btn.dataset.mayamiSaveStyled = '1';
      btn.addEventListener('mouseenter', function() {
        this.style.background = '#f0f0f1';
        this.style.transform = 'translateY(-1px)';
        this.style.boxShadow = '0 3px 8px rgba(0,0,0,0.2)';
      });

      btn.addEventListener('mouseleave', function() {
        this.style.background = '#fff';
        this.style.transform = 'translateY(0)';
        this.style.boxShadow = 'none';
      });
    });
  }

  function toggleSection(sectionId) {
    const sectionTitle = getSectionTitleElement(sectionId);
    if (!sectionTitle) {
      return;
    }

    const isOpen = sectionTitle.classList.contains('mayami-section-open');

    if (isOpen) {
      closeSection(sectionId);
      clearActiveButtons();
      isOverviewMode = true;
      return;
    }

    isOverviewMode = false;
    openSection(sectionId);
    setActiveButtonBySection(sectionId);
  }

  function setActiveButtonBySection(sectionId) {
    const btn = document.querySelector('.mayami-nav-btn[data-section="' + sectionId + '"]');
    if (!btn) {
      clearActiveButtons();
      return;
    }

    setActiveButton(btn);
  }

  function closeAllSections() {
    SECTION_IDS.forEach(sectionId => {
      closeSection(sectionId);
    });
  }

  function getSectionTitleElement(sectionId) {
    return document.querySelector('.cmb2-id-' + sectionId.replace(/_/g, '-'));
  }

  function getSectionContentRows(sectionId) {
    const titleEl = getSectionTitleElement(sectionId);
    if (!titleEl) return [];

    const rows = [];
    let current = titleEl.nextElementSibling;

    while (current) {
      if (isSectionTitleRow(current)) {
        break;
      }

      if (current.classList && current.classList.contains('cmb-row')) {
        rows.push(current);
      }

      current = current.nextElementSibling;
    }

    return rows;
  }

  function isSectionTitleRow(el) {
    if (!el || !el.classList) return false;

    return Array.from(el.classList).some(className => {
      return className.indexOf('cmb2-id-section-') === 0 && className.indexOf('-title') !== -1;
    });
  }

  function closeSection(sectionId) {
    const titleEl = getSectionTitleElement(sectionId);
    const rows = getSectionContentRows(sectionId);

    rows.forEach(row => {
      row.style.display = 'none';
    });

    if (titleEl) {
      titleEl.classList.remove('mayami-section-open');
      titleEl.classList.add('mayami-section-closed');
      titleEl.setAttribute('aria-expanded', 'false');
      applySectionHeaderState(titleEl, false);
    }
  }

  function openSection(sectionId) {
    SECTION_IDS.forEach(id => {
      if (id !== sectionId) {
        closeSection(id);
      }
    });

    const titleEl = getSectionTitleElement(sectionId);
    const rows = getSectionContentRows(sectionId);

    rows.forEach(row => {
      row.style.display = '';
    });

    if (titleEl) {
      titleEl.classList.remove('mayami-section-closed');
      titleEl.classList.add('mayami-section-open');
      titleEl.setAttribute('aria-expanded', 'true');
      applySectionHeaderState(titleEl, true);
    }
  }

  function applySectionHeaderState(titleEl, isOpen) {
    const headerCell = titleEl.querySelector('.cmb-th');
    const heading = titleEl.querySelector('.cmb2-metabox-title') || titleEl.querySelector('h3');
    const eye = titleEl.querySelector('.mayami-eye-indicator');
    const neutralBg = '#f2f2f3';
    const neutralText = '#1f2937';
    const activeBg = 'linear-gradient(135deg, #6a1b78 0%, #410b49 100%)';

    if (headerCell) {
      if (isOpen) {
        headerCell.style.setProperty('background-color', '#5b1b78', 'important');
        headerCell.style.setProperty('background-image', activeBg, 'important');
        headerCell.style.setProperty('background', activeBg, 'important');
        headerCell.style.setProperty('color', '#ffffff', 'important');
        headerCell.style.setProperty('border-left-color', '#13f7bc', 'important');
      } else {
        headerCell.style.setProperty('background-image', 'none', 'important');
        headerCell.style.setProperty('background-color', neutralBg, 'important');
        headerCell.style.setProperty('background', neutralBg, 'important');
        headerCell.style.setProperty('color', neutralText, 'important');
        headerCell.style.setProperty('border-left-color', '#dadde2', 'important');
      }
    }

    if (heading) {
      heading.style.setProperty('color', isOpen ? '#ffffff' : neutralText, 'important');
    }

    if (eye) {
      eye.style.setProperty('color', isOpen ? '#ffffff' : '#6b21a8', 'important');
    }
  }

  function scrollToSection(sectionId) {
    const sectionEl = document.querySelector('.cmb2-id-' + sectionId.replace(/_/g, '-'));
    if (!sectionEl) return;

    const nav = document.getElementById('mayami-admin-nav');
    const navHeight = nav ? nav.offsetHeight : 0;
    const offset = 20; // Padding supplémentaire

    const elementPosition = sectionEl.getBoundingClientRect().top + window.pageYOffset;
    const offsetPosition = elementPosition - navHeight - offset;

    window.scrollTo({
      top: offsetPosition,
      behavior: 'smooth'
    });
  }

  function setActiveButton(activeBtn) {
    const allBtns = document.querySelectorAll('.mayami-nav-btn');
    allBtns.forEach(btn => btn.classList.remove('active'));
    activeBtn.classList.add('active');
  }

  function clearActiveButtons() {
    const allBtns = document.querySelectorAll('.mayami-nav-btn');
    allBtns.forEach(btn => btn.classList.remove('active'));
  }

  function observeSections(sections) {
    const nav = document.getElementById('mayami-admin-nav');
    const navHeight = nav ? nav.offsetHeight : 0;

    const observerOptions = {
      root: null,
      rootMargin: `-${navHeight + 50}px 0px -60% 0px`,
      threshold: 0
    };

    const observer = new IntersectionObserver(entries => {
      if (isOverviewMode) {
        return;
      }

      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const sectionId = entry.target.classList[0].replace('cmb2-id-', '').replace(/-/g, '_');
          const btn = document.querySelector(`.mayami-nav-btn[data-section="${sectionId}"]`);
          if (btn) setActiveButton(btn);
        }
      });
    }, observerOptions);

    sections.forEach(section => {
      const el = document.querySelector('.cmb2-id-' + section.id.replace(/_/g, '-'));
      if (el) observer.observe(el);
    });
  }

  function addSmoothScroll() {
    // Style général pour smooth scroll
    document.documentElement.style.scrollBehavior = 'smooth';
  }
})();
