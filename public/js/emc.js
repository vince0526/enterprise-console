// Activity dropdown + nav behavior (stable, flicker-free)
(function(){
  const ACTIVITY_PIN_KEY = 'emc.activity.dropdown.pin';

  function initActivityDropdown() {
    const toggle = document.getElementById('activity-toggle');
    const menu = document.getElementById('activity-menu');
    const pinButton = document.querySelector('.activity-dropdown__pin');
    if (!toggle || !menu) return;

    const isPinned = localStorage.getItem(ACTIVITY_PIN_KEY) === '1';
    if (isPinned && pinButton) {
      pinButton.classList.add('pinned');
      menu.classList.add('show');
      toggle.setAttribute('aria-expanded', 'true');
    }

    toggle.addEventListener('click', function(e) {
      e.stopPropagation();
      const isOpen = menu.classList.contains('show');
      if (isOpen) closeDropdown(); else openDropdown();
    });

    if (pinButton) {
      pinButton.addEventListener('click', function(e) {
        e.stopPropagation();
        const nowPinned = this.classList.toggle('pinned');
        localStorage.setItem(ACTIVITY_PIN_KEY, nowPinned ? '1' : '0');
        if (!nowPinned) closeDropdown();
      });
    }

    document.addEventListener('click', function(e) {
      const pinned = pinButton && pinButton.classList.contains('pinned');
      if (!pinned && !toggle.contains(e.target) && !menu.contains(e.target)) {
        closeDropdown();
      }
    });

    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        const pinned = pinButton && pinButton.classList.contains('pinned');
        if (!pinned) { closeDropdown(); toggle.focus(); }
      }
    });

    function openDropdown() {
      menu.classList.add('show');
      toggle.setAttribute('aria-expanded', 'true');
    }
    function closeDropdown() {
      menu.classList.remove('show');
      toggle.setAttribute('aria-expanded', 'false');
    }
  }

  function initPin(){
    const btn = document.querySelector('.activity .pin');
    if(!btn) return;
    const pinned = localStorage.getItem('emc.activity.pin') === '1';
    if(pinned) btn.classList.add('active');
    btn.addEventListener('click', ()=>{
      const isActive = btn.classList.toggle('active');
      localStorage.setItem('emc.activity.pin', isActive ? '1' : '0');
    });
  }

  function initSidebarToggle(){ /* reserved for future */ }

  // Top navigation behavior
  function initTopNavBehavior(){
    const viewport = document.getElementById('top-nav-viewport');
    if(!viewport) return;
    const list = viewport.querySelector('.nav__list');
    const prevBtn = document.querySelector('.nav__scroll--prev');
    const nextBtn = document.querySelector('.nav__scroll--next');
    const indicator = document.getElementById('nav-indicator');
    const nav = viewport.closest('.nav');

    // rAF scheduler
    let rafId = null;
    const schedule = (fn) => { if(rafId) cancelAnimationFrame(rafId); rafId = requestAnimationFrame(fn); };

    // Debounced mode switching
    let modeTimer = null;
  const setNavModeDebounced = () => { clearTimeout(modeTimer); modeTimer = setTimeout(()=> schedule(setNavMode), 160); };

    const updateButtons = () => {
      if(!list) return;
      const maxScroll = Math.max(0, list.scrollWidth - list.clientWidth);
      const hasOverflow = maxScroll > 1;
      if(nav) nav.classList.toggle('nav--overflow', hasOverflow);
      const isWrap = nav?.classList.contains('nav--wrap');
      if(prevBtn) prevBtn.style.display = (!isWrap && hasOverflow && list.scrollLeft > 0) ? 'inline-flex' : 'none';
      if(nextBtn) nextBtn.style.display = (!isWrap && hasOverflow && list.scrollLeft < maxScroll) ? 'inline-flex' : 'none';
    };

    const smoothScroll = (dir) => {
      const amount = Math.max(200, list.clientWidth * 0.6);
      list.scrollBy({ left: amount * dir, behavior: 'smooth' });
      setTimeout(updateButtons, 350);
    };
    prevBtn?.addEventListener('click', ()=> smoothScroll(-1));
    nextBtn?.addEventListener('click', ()=> smoothScroll(1));
    list?.addEventListener('scroll', ()=> schedule(updateButtons));
    window.addEventListener('resize', ()=> schedule(updateButtons));

    // Indicator
    const moveIndicator = (el)=>{
      if(!indicator || !el) return;
      if(nav && (nav.classList.contains('nav--wrap') || nav.classList.contains('nav--resizing'))) return;
      // Use client rects to align precisely within viewport even during partial scroll
      const r = el.getBoundingClientRect();
      const pr = viewport.getBoundingClientRect();
      const width = Math.min(r.width, pr.width);
      const x = r.left - pr.left;
      indicator.style.width = `${width}px`;
      indicator.style.transform = `translate3d(${x}px,0,0)`;
    };
    const scheduleIndicator = () => schedule(()=> moveIndicator(list?.querySelector('.nav__link--active') || document.activeElement));
    window.addEventListener('resize', scheduleIndicator);
    list?.addEventListener('scroll', scheduleIndicator);

    // Mode switching with stronger hysteresis
    let lastMode = null;
    const setNavMode = () => {
      if(nav && nav.classList.contains('nav--resizing')) return; // freeze mode during active resize
      if(!nav || !list) return;
      const aTags = Array.from(list.querySelectorAll('.nav__link'));
      if(aTags.length === 0) return;

      // measure
      let total = 0; let minItem = 0;
      aTags.forEach(a => {
        const el = a.parentElement || a;
        const rect = el.getBoundingClientRect();
        const style = window.getComputedStyle(el);
        const margin = parseFloat(style.marginLeft) + parseFloat(style.marginRight);
        const w = rect.width + margin;
        total += w;
        minItem = minItem ? Math.min(minItem, w) : w;
      });
      const available = viewport.clientWidth;
  const ENTER_EQUAL_MARGIN = 24; // require extra room to enter equal mode
      const shouldEqual = total <= (available - ENTER_EQUAL_MARGIN);
      const minPerItem = Math.max(160, Math.round(minItem || 160));
      const count = aTags.length;
  // Wrap disabled: we keep a single row to eliminate vertical jitter
  const canWrap = false;

      let targetMode = 'compact';
      if (shouldEqual) {
        targetMode = 'equal';
      } else if (canWrap) {
        targetMode = 'wrap';
      }

      const isEqual = nav.classList.contains('nav--equal');
      const isCompact = nav.classList.contains('nav--compact');
      const isWrap = nav.classList.contains('nav--wrap');

      // Hysteresis: if we're currently in wrap, don't exit until we really can't fit two rows
      // If currently in wrap (legacy), force exit to compact/equal to keep single row
      if (isWrap && !shouldEqual) targetMode = 'compact';

      const applyMode = (mode) => {
        nav.classList.remove('nav--equal','nav--compact','nav--wrap');
        nav.classList.add(`nav--${mode}`);
        if(mode !== 'compact') list.scrollLeft = 0;
        if(indicator) indicator.style.display = (mode === 'wrap') ? 'none' : '';
      };

  if(targetMode === 'equal' && !isEqual) applyMode('equal');
  else if(targetMode === 'compact' && !isCompact) applyMode('compact');

      lastMode = targetMode;
      updateButtons();
      const active = list?.querySelector('.nav__link--active') || aTags[0];
      if(active) moveIndicator(active);
    };

  const ro = new ResizeObserver(()=> setNavModeDebounced());
  ro.observe(viewport); // observe only viewport to avoid noisy list mutations

    // Resize state to quiet indicator during drag
    let resizeStateTimer = null;
    const onResizeStart = () => {
      if(!nav) return;
      nav.classList.add('nav--resizing');
      // temporarily disable link transitions to prevent paint jitter
      list?.classList.add('is-transitionless');
      clearTimeout(resizeStateTimer);
      resizeStateTimer = setTimeout(()=>{
        nav.classList.remove('nav--resizing');
        list?.classList.remove('is-transitionless');
        setNavModeDebounced();
        scheduleIndicator();
      }, 240);
    };
    window.addEventListener('resize', onResizeStart);

    // Initial
    setNavMode();
    updateButtons();
    const first = list?.querySelector('.nav__link--active') || list?.querySelector('.nav__link');
    if(first) moveIndicator(first);
  }

  document.addEventListener('DOMContentLoaded', function(){
    initActivityDropdown();
    initPin();
    initSidebarToggle();
    initTopNavBehavior();
  });

  // expose for manual re-init (optional)
  window.initTopNavBehavior = initTopNavBehavior;
})();
