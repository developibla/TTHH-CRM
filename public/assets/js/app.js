(function () {
  const body = document.body;

  const btnToggle = document.querySelector('[data-toggle-sidebar]');
  const btnClose  = document.querySelector('[data-close-sidebar]');
  const overlay   = document.querySelector('[data-sidebar-overlay]');

  // Mobile: open/close drawer
  function openMobile() { body.classList.add('sidebar-open'); }
  function closeMobile() { body.classList.remove('sidebar-open'); }

  // Desktop: collapse/expand sidebar
  function toggleDesktopCollapse() { body.classList.toggle('sidebar-collapsed'); }

  function isMobile() {
    return window.matchMedia('(max-width: 980px)').matches;
  }

  function toggleSidebar() {
    if (isMobile()) {
      body.classList.contains('sidebar-open') ? closeMobile() : openMobile();
    } else {
      toggleDesktopCollapse();
    }
  }

  btnToggle?.addEventListener('click', (e) => {
    e.preventDefault();
    toggleSidebar();
  });

  btnClose?.addEventListener('click', (e) => {
    e.preventDefault();
    closeMobile();
  });

  overlay?.addEventListener('click', closeMobile);

  // Esc cierra drawer en mobile
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeMobile();
  });

  // Si cambia a desktop, quitamos sidebar-open (para que no quede “enganchado”)
  window.addEventListener('resize', () => {
    if (!isMobile()) closeMobile();
  });

  // ===== Accordion: 1 abierto a la vez =====
  const accs = document.querySelectorAll('.sidebar .accordion');
  accs.forEach((btn) => {
    btn.addEventListener('click', () => {
      // En desktop colapsado no abrimos paneles
      if (!isMobile() && body.classList.contains('sidebar-collapsed')) return;

      // cerrar otros
      accs.forEach((b) => {
        if (b !== btn) {
          b.classList.remove('active');
          const p = b.nextElementSibling;
          if (p && p.classList.contains('panel')) p.style.display = 'none';
        }
      });

      // toggle actual
      btn.classList.toggle('active');
      const panel = btn.nextElementSibling;
      if (!panel || !panel.classList.contains('panel')) return;

      panel.style.display = (panel.style.display === 'block') ? 'none' : 'block';
    });
  });
})();
