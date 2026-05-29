/**
 * Admin Navigation Sticky - Mayami Landing
 */
(function() {
  'use strict';

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
    addSmoothScroll();
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
      { id: 'section_hero_title', label: 'Hero' },
      { id: 'section_slider_title', label: 'Slider' },
      { id: 'section_stream_title', label: 'Stream' },
      { id: 'section_social_title', label: 'Social' },
      { id: 'section_video_title', label: 'Video' },
      { id: 'section_release_title', label: 'Release' },
      { id: 'section_cta_title', label: 'CTA' },
      { id: 'section_footer_title', label: 'Footer' },
      { id: 'section_links_title', label: 'Links' },
      { id: 'section_marquee_title', label: 'Marquee' }
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
    const title = document.createElement('h2');
    title.textContent = 'Admin';
    title.style.cssText = 'margin: 0; font-size: 20px; color: #fff; font-weight: 600;';
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

    // Activer le premier bouton par défaut
    const firstBtn = buttonsContainer.querySelector('.mayami-nav-btn');
    if (firstBtn) firstBtn.classList.add('active');

    // Observer le scroll pour mettre à jour le bouton actif
    observeSections(sections);
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

  function observeSections(sections) {
    const nav = document.getElementById('mayami-admin-nav');
    const navHeight = nav ? nav.offsetHeight : 0;

    const observerOptions = {
      root: null,
      rootMargin: `-${navHeight + 50}px 0px -60% 0px`,
      threshold: 0
    };

    const observer = new IntersectionObserver(entries => {
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
