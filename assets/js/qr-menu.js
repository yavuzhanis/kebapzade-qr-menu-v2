/**
 * Kebapzade Cappadocia QR Menü V2 - Interactive Accordion & Lightbox Experience
 */

document.addEventListener('DOMContentLoaded', () => {
  // --- DOM Elements ---
  const searchInput = document.querySelector('[data-menu-search]');
  const searchClearBtn = document.querySelector('[data-search-clear]');
  const resultsCounter = document.querySelector('[data-results-counter]');
  const noResultsCard = document.querySelector('[data-no-results]');
  const resetSearchBtn = document.querySelector('[data-reset-search]');
  const filterChips = document.querySelectorAll('.filter-chip');
  const catNavPills = document.querySelectorAll('[data-cat-link]');
  const catScrollTrack = document.querySelector('.category-nav-scroll');
  const categorySections = document.querySelectorAll('[data-category]');
  const backToTopBtn = document.querySelector('[data-back-top]');
  const toast = document.getElementById('toast');

  // Lightbox Elements
  const imageLightbox = document.getElementById('imageLightbox');
  const closeLightbox = document.getElementById('closeLightbox');
  const lightboxImg = document.getElementById('lightboxImg');
  const lightboxTitle = document.getElementById('lightboxTitle');
  const lightboxPrice = document.getElementById('lightboxPrice');

  // State
  let currentFilter = 'all';

  // Helper: Turkish lowercase normalize
  const normalize = (val) => (val || '').toLocaleLowerCase('tr-TR').normalize('NFD').replace(/[\u0300-\u036f]/g, '');

  // Helper: Toast message
  const showToast = (text) => {
    if (!toast) return;
    toast.textContent = text;
    toast.classList.add('show');
    clearTimeout(window._toastTimer);
    window._toastTimer = setTimeout(() => toast.classList.remove('show'), 2800);
  };

  // --- 0. THEME SWITCHER (LIGHT MODE BY DEFAULT) ---
  const lang = document.documentElement.getAttribute('lang') || 'tr';
  const metaThemeColor = document.querySelector('meta[name="theme-color"]');

  function getStoredTheme() {
    return localStorage.getItem('kebapzade_theme') || 'light';
  }

  function applyTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    if (metaThemeColor) {
      metaThemeColor.setAttribute('content', theme === 'dark' ? '#120503' : '#f8f4ee');
    }

    // Update labels for next state
    const nextThemeLabel = theme === 'dark'
      ? (lang === 'en' ? 'Light Mode' : 'Aydınlık Mod')
      : (lang === 'en' ? 'Dark Mode' : 'Karanlık Mod');

    document.querySelectorAll('[data-theme-label]').forEach((el) => {
      el.textContent = nextThemeLabel;
    });

    const nextThemeTitle = theme === 'dark'
      ? (lang === 'en' ? 'Switch to Light Mode' : 'Aydınlık Moda Geç')
      : (lang === 'en' ? 'Switch to Dark Mode' : 'Karanlık Moda Geç');

    document.querySelectorAll('[data-theme-toggle]').forEach((btn) => {
      btn.setAttribute('title', nextThemeTitle);
      btn.setAttribute('aria-label', nextThemeTitle);
    });
  }

  // Initial sync on page load
  applyTheme(getStoredTheme());

  // Listen for clicks on all theme toggle triggers (header, floating, footer)
  document.querySelectorAll('[data-theme-toggle]').forEach((btn) => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const current = getStoredTheme();
      const next = current === 'dark' ? 'light' : 'dark';
      localStorage.setItem('kebapzade_theme', next);
      applyTheme(next);

      const notifyMsg = next === 'dark'
        ? (lang === 'en' ? 'Dark mode activated' : 'Karanlık mod aktif')
        : (lang === 'en' ? 'Light mode activated' : 'Aydınlık mod aktif');
      showToast(notifyMsg);
    });
  });

  // --- 1. ACCORDION CATEGORY TOGGLES ---
  categorySections.forEach((section) => {
    const toggleBtn = section.querySelector('[data-category-toggle]');
    const panel = section.querySelector('[data-dishes-panel]');

    if (toggleBtn && panel) {
      toggleBtn.addEventListener('click', () => {
        const isExpanded = toggleBtn.getAttribute('aria-expanded') === 'true';
        setCategoryState(section, !isExpanded);
      });
    }
  });

  function setCategoryState(section, expand) {
    const toggleBtn = section.querySelector('[data-category-toggle]');
    const panel = section.querySelector('[data-dishes-panel]');
    if (!toggleBtn || !panel) return;

    if (expand) {
      toggleBtn.setAttribute('aria-expanded', 'true');
      panel.hidden = false;
    } else {
      toggleBtn.setAttribute('aria-expanded', 'false');
      panel.hidden = true;
    }
  }

  // --- 2. CATEGORY PILL NAVIGATION ---
  catNavPills.forEach((pill) => {
    pill.addEventListener('click', (e) => {
      e.preventDefault();
      const targetId = pill.dataset.catLink;
      const targetSection = document.getElementById(targetId);
      if (targetSection) {
        catNavPills.forEach((p) => p.classList.remove('active'));
        pill.classList.add('active');

        // Always expand the category when clicked from pills
        setCategoryState(targetSection, true);

        // Center pill in horizontal bar
        pill.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });

        // Scroll to target with sticky dock offset
        const yOffset = -150;
        const y = targetSection.getBoundingClientRect().top + window.pageYOffset + yOffset;
        window.scrollTo({ top: y, behavior: 'smooth' });
      }
    });
  });

  // --- 3. FILTER & LIVE SEARCH ENGINE ---
  function applyFilters() {
    const term = normalize(searchInput ? searchInput.value.trim() : '');
    let totalVisible = 0;

    if (searchClearBtn) {
      searchClearBtn.hidden = !term;
    }

    categorySections.forEach((cat) => {
      const dishes = cat.querySelectorAll('[data-menu-item]');
      let catVisibleCount = 0;

      dishes.forEach((dish) => {
        const dishSearch = normalize(dish.dataset.search || '');
        const matchesTerm = !term || dishSearch.includes(term);

        let matchesChip = true;
        if (currentFilter === 'featured') {
          matchesChip = dish.dataset.featured === '1';
        } else if (currentFilter === 'spicy') {
          matchesChip = dish.dataset.spicy === '1';
        } else if (currentFilter === 'veg') {
          matchesChip = dish.dataset.veg === '1';
        }

        const isVisible = matchesTerm && matchesChip;
        dish.hidden = !isVisible;
        if (isVisible) {
          catVisibleCount++;
          totalVisible++;
        }
      });

      const hasVisibleDishes = catVisibleCount > 0;
      cat.hidden = !hasVisibleDishes;

      // Auto expand matching categories when searching or filtering
      if (term || currentFilter !== 'all') {
        if (hasVisibleDishes) {
          setCategoryState(cat, true);
        }
      }
    });

    if (noResultsCard) {
      noResultsCard.hidden = totalVisible > 0;
    }

    if (resultsCounter) {
      if (term || currentFilter !== 'all') {
        resultsCounter.hidden = false;
        resultsCounter.innerHTML = `<span><b>${totalVisible}</b> lezzet listeleniyor</span>`;
      } else {
        resultsCounter.hidden = true;
      }
    }
  }

  if (searchInput) {
    searchInput.addEventListener('input', applyFilters);
  }

  if (searchClearBtn) {
    searchClearBtn.addEventListener('click', () => {
      searchInput.value = '';
      applyFilters();
      searchInput.focus();
    });
  }

  if (resetSearchBtn) {
    resetSearchBtn.addEventListener('click', () => {
      if (searchInput) searchInput.value = '';
      currentFilter = 'all';
      filterChips.forEach((c) => c.classList.toggle('active', c.dataset.filter === 'all'));
      applyFilters();
    });
  }

  filterChips.forEach((chip) => {
    chip.addEventListener('click', () => {
      filterChips.forEach((c) => c.classList.remove('active'));
      chip.classList.add('active');
      currentFilter = chip.dataset.filter || 'all';
      applyFilters();
    });
  });

  // --- 4. IMAGE LIGHTBOX (TAP PHOTO TO ZOOM) ---
  document.querySelectorAll('[data-lightbox-trigger]').forEach((trigger) => {
    trigger.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      const img = trigger.dataset.lightboxImg;
      const title = trigger.dataset.lightboxTitle || '';
      const price = trigger.dataset.lightboxPrice || '';

      if (imageLightbox && lightboxImg) {
        lightboxImg.src = img;
        lightboxImg.alt = title;
        if (lightboxTitle) lightboxTitle.textContent = title;
        if (lightboxPrice) lightboxPrice.textContent = price;

        imageLightbox.classList.add('is-open');
        imageLightbox.removeAttribute('hidden');
        imageLightbox.style.display = 'flex';
        document.body.style.overflow = 'hidden';
      }
    });
  });

  function closeImageLightbox() {
    if (imageLightbox) {
      imageLightbox.classList.remove('is-open');
      imageLightbox.setAttribute('hidden', '');
      imageLightbox.style.display = 'none';
      document.body.style.overflow = '';
      if (lightboxImg) lightboxImg.src = '';
    }
  }

  if (closeLightbox) {
    closeLightbox.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      closeImageLightbox();
    });
  }

  if (imageLightbox) {
    imageLightbox.addEventListener('click', (e) => {
      if (e.target === imageLightbox) {
        closeImageLightbox();
      }
    });
  }

  // Close on ESC
  window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      closeImageLightbox();
    }
  });

  // --- 5. WI-FI SINGLE-TAP COPY ---
  document.querySelectorAll('.wifi-inline-btn, [data-wifi-pass]').forEach((btn) => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      const pass = btn.getAttribute('data-wifi-pass');
      const msg = btn.getAttribute('data-copied-msg') || 'Wi-Fi şifresi kopyalandı!';

      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(pass).then(() => showToast(msg)).catch(() => copyFallback(pass, msg));
      } else {
        copyFallback(pass, msg);
      }
    });
  });

  function copyFallback(text, msg) {
    const el = document.createElement('textarea');
    el.value = text;
    el.setAttribute('readonly', '');
    el.style.position = 'absolute';
    el.style.left = '-9999px';
    document.body.appendChild(el);
    el.select();
    try {
      document.execCommand('copy');
      showToast(msg);
    } catch (e) {}
    document.body.removeChild(el);
  }

  // --- 6. BACK TO TOP ---
  window.addEventListener(
    'scroll',
    () => {
      if (backToTopBtn) {
        backToTopBtn.classList.toggle('visible', window.scrollY > 380);
      }
    },
    { passive: true }
  );

  if (backToTopBtn) {
    backToTopBtn.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }
});
