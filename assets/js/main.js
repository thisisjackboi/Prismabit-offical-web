document.addEventListener('DOMContentLoaded', () => {
  // =================================================================
  // --- INTERACTIVE HEADER LOGO ---
  // =================================================================
  // This splits the "Prismabit" wordmark into individual letters to allow for the rainbow hover effect.
  const wordmark = document.getElementById('header-wordmark');
  if (wordmark) {
    const text = wordmark.textContent.trim();
    wordmark.textContent = ''; // clear
    [...text].forEach((ch, idx) => {
      const span = document.createElement('span');
      span.className = 'rainbow-letter';
      span.textContent = ch;
      span.setAttribute('aria-label', ch);
      span.style.transitionDelay = `${(idx * 8)}ms`;
      wordmark.appendChild(span);
    });
  }

  // Subtle lift effect on the wordmark when hovering the logo.
  const headerLogo = document.getElementById('header-logo');
  if (headerLogo && wordmark) {
    headerLogo.addEventListener('mouseenter', () => {
      wordmark.style.transform = 'translateY(-0.5px)';
    });
    headerLogo.addEventListener('mouseleave', () => {
      wordmark.style.transform = '';
    });
  }

  // =================================================================
  // --- NAVBAR SCROLL BEHAVIOR ---
  // =================================================================
  // Keep navbar fixed and visible. Match background to page at top, then apply glassmorphism on scroll.
  const headerNav = document.getElementById('header-nav');
  if (headerNav) {
    let ticking = false;

    // Ensure navbar is fixed and visible
    headerNav.style.transform = 'translateY(0)';

    // Helper: set solid background to match page primary color
    const setSolidPrimaryBackground = () => {
      const bodyBg = getComputedStyle(document.body).backgroundColor;
      headerNav.style.backgroundColor = bodyBg;
      headerNav.classList.add('backdrop-blur-sm', 'border-white/20');
      headerNav.classList.remove('bg-[#06141B]/10', 'backdrop-blur-xl', 'shadow-md', 'border-[#4A5C6A]/20');
    };

    // Helper: set glassmorphism style
    const setGlassBackground = () => {
      headerNav.style.backgroundColor = '';
      headerNav.classList.add('bg-[#06141B]/10', 'backdrop-blur-xl', 'shadow-md', 'border-[#4A5C6A]/20');
      headerNav.classList.remove('backdrop-blur-sm', 'border-white/20');
    };

    // Initialize with solid background matching page
    setSolidPrimaryBackground();

    const handleScroll = () => {
      const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
      if (scrollTop > 50) {
        setGlassBackground();
      } else {
        setSolidPrimaryBackground();
      }
      ticking = false;
    };

    window.addEventListener('scroll', () => {
      if (!ticking) {
        window.requestAnimationFrame(handleScroll);
        ticking = true;
      }
    
    });
    // Recompute on resize (in case themes/bg change)
    window.addEventListener('resize', () => {
      if ((window.pageYOffset || document.documentElement.scrollTop) <= 50) {
        setSolidPrimaryBackground();
      }
    });
  }
  

  // =================================================================
  // --- REVEAL ON SCROLL ---
  // =================================================================
  // This uses the Intersection Observer API to add/remove a 'visible' class to elements as they enter/leave the viewport.
  const animateStat = (el) => {
    if (el.dataset.animated === 'true') return;
    const target = parseFloat(el.getAttribute('data-target') || '0');
    const suffix = el.getAttribute('data-suffix') || '';
    const durationMs = 1200; // sync with typical fade timing
    const startTs = performance.now();
    el.dataset.animated = 'true';

    const step = (now) => {
      const elapsed = now - startTs;
      const progress = Math.min(elapsed / durationMs, 1);
      const eased = 1 - Math.pow(1 - progress, 3); // easeOutCubic
      const value = Math.round(target * eased);
      el.textContent = `${value}${suffix}`;
      if (progress < 1) requestAnimationFrame(step);
    };
    requestAnimationFrame(step);
  };

  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          // Trigger stat counters inside this revealed block immediately with fade-in
          entry.target.querySelectorAll('.stat-number').forEach(animateStat);
          // Special handling for the floating card section
          if (entry.target.id === 'services-sticky-parent') {
            entry.target.style.transform = 'scale(1)';
            entry.target.style.opacity = '1';
          }
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.05, rootMargin: '0px 0px 50px 0px' });

    document.querySelectorAll('.reveal-on-scroll, .service-card-scroll').forEach(el => observer.observe(el));
  } else {
    document.querySelectorAll('.reveal-on-scroll, .service-card-scroll').forEach(el => el.classList.add('visible'));
  }

  // Also handle elements/stats that might already be in view on initial load
  const checkInitialVisibility = () => {
    document.querySelectorAll('.reveal-on-scroll, .service-card-scroll').forEach(el => {
      const rect = el.getBoundingClientRect();
      if (rect.top < window.innerHeight + 100) {
        el.classList.add('visible');
        el.querySelectorAll('.stat-number').forEach(animateStat);
        if (el.id === 'services-sticky-parent') {
          el.style.transform = 'scale(1)';
          el.style.opacity = '1';
        }
      }
    });
  };
  checkInitialVisibility();
  window.addEventListener('load', checkInitialVisibility);
  

  // =================================================================
  // --- DESKTOP NAV DROPDOWNS (Industries, Tech) ---
  // =================================================================
  // Toggle dropdown menus on click and close on outside click/escape.
  const dropdownToggles = Array.from(document.querySelectorAll('.dropdown-toggle'));
  const closeAllDropdowns = () => {
    document.querySelectorAll('.dropdown-menu').forEach(menu => menu.classList.add('hidden'));
    dropdownToggles.forEach(btn => btn.setAttribute('aria-expanded', 'false'));
  };

  dropdownToggles.forEach(toggle => {
    const wrapper = toggle.parentElement;
    const menu = wrapper ? wrapper.querySelector('.dropdown-menu') : null;
    if (!wrapper || !menu) return;

    toggle.addEventListener('click', (e) => {
      e.preventDefault();
      const isOpen = !menu.classList.contains('hidden');
      closeAllDropdowns();
      if (!isOpen) {
        menu.classList.remove('hidden');
        toggle.setAttribute('aria-expanded', 'true');
      }
    });
  });

  document.addEventListener('click', (e) => {
    const isInsideDropdown = (e.target.closest && e.target.closest('.dropdown-toggle, .dropdown-menu'));
    if (!isInsideDropdown) closeAllDropdowns();
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeAllDropdowns();
  });

  // =================================================================
  // --- WHATSAPP FAB VISIBILITY ---
  // =================================================================
  // The FAB is hidden by default. This makes it visible on all pages.
  // This logic centralizes the visibility trigger for all scenarios.
  const fab = document.querySelector('.whatsapp-fab');
  if (fab) {
    const splashContainer = document.getElementById('splash-container');
    const urlParams = new URLSearchParams(window.location.search);
    const skipSplash = urlParams.get("nosplash") === "true";

    // This logic handles showing the button on all pages *except* for the
    // homepage when the splash screen is running. The visibility on the homepage
    // is handled by the inline script in index.html.
    //
    // The button will be shown if:
    // 1. There is no splash container (i.e., not the homepage).
    // 2. The splash screen is explicitly skipped via URL parameter.
    if (!splashContainer || skipSplash) {
      // Show the button after a brief delay to let the page settle.
      setTimeout(() => {
        fab.classList.remove('hidden');
      }, 300);
    }
  }

  // =================================================================
  // --- DESKTOP NAV DROPDOWNS ---
  // =================================================================
  // Handle dropdown visibility on hover for desktop navigation
  const dropdownGroups = document.querySelectorAll('.group');
  if (dropdownGroups.length > 0) {
    dropdownGroups.forEach(group => {
      const menu = group.querySelector('.dropdown-menu');
      if (menu) {
        group.addEventListener('mouseenter', () => {
          menu.classList.remove('hidden');
          menu.style.opacity = '1';
          menu.style.transform = 'translateY(0)';
        });

        group.addEventListener('mouseleave', () => {
          menu.classList.add('hidden');
          menu.style.opacity = '0';
          menu.style.transform = 'translateY(-10px)';
        });
      }
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', (e) => {
      if (!e.target.closest('.group')) {
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
          menu.classList.add('hidden');
          menu.style.opacity = '0';
          menu.style.transform = 'translateY(-10px)';
        });
      }
    });
  }

  // =================================================================
  // --- MOBILE NAVIGATION MENU TOGGLE ---
  // =================================================================
  // Handles the hamburger / close icon toggle and slide-down mobile drawer
  // for all pages. Works with #mobile-menu-button, #mobile-menu,
  // #menu-open-icon, and #menu-close-icon.
  const mobileMenuBtn = document.getElementById('mobile-menu-button');
  const mobileMenuEl  = document.getElementById('mobile-menu');
  const menuOpenIcon  = document.getElementById('menu-open-icon');
  const menuCloseIcon = document.getElementById('menu-close-icon');

  if (mobileMenuBtn && mobileMenuEl) {
    mobileMenuBtn.addEventListener('click', () => {
      const isOpen = !mobileMenuEl.classList.contains('hidden');
      if (isOpen) {
        // Close
        mobileMenuEl.classList.add('hidden');
        menuOpenIcon?.classList.remove('hidden');
        menuCloseIcon?.classList.add('hidden');
        mobileMenuBtn.setAttribute('aria-expanded', 'false');
      } else {
        // Open
        mobileMenuEl.classList.remove('hidden');
        menuOpenIcon?.classList.add('hidden');
        menuCloseIcon?.classList.remove('hidden');
        mobileMenuBtn.setAttribute('aria-expanded', 'true');
      }
    });

    // Close mobile menu when clicking a link inside it
    mobileMenuEl.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => {
        mobileMenuEl.classList.add('hidden');
        menuOpenIcon?.classList.remove('hidden');
        menuCloseIcon?.classList.add('hidden');
        mobileMenuBtn.setAttribute('aria-expanded', 'false');
      });
    });

    // Close mobile menu when clicking outside
    document.addEventListener('click', (e) => {
      if (!mobileMenuBtn.contains(e.target) && !mobileMenuEl.contains(e.target)) {
        mobileMenuEl.classList.add('hidden');
        menuOpenIcon?.classList.remove('hidden');
        menuCloseIcon?.classList.add('hidden');
        mobileMenuBtn.setAttribute('aria-expanded', 'false');
      }
    });
  }

});