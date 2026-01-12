/* public/assets/js/app.js */
(function () {
  "use strict";

  function qs(sel, root) { return (root || document).querySelector(sel); }
  function qsa(sel, root) { return Array.from((root || document).querySelectorAll(sel)); }

  // =========================
  // SIDEBAR: toggle colapsar + overlay mobile
  // =========================
  function initSidebarShell() {
    const btnToggle = qs('[data-toggle-sidebar]');
    const btnClose = qs('[data-close-sidebar]');
    const overlay = qs('[data-sidebar-overlay]');
    const sidebar = qs('.sidebar');

    function openMobile() {
      document.body.classList.add('sidebar-open');
      document.body.classList.remove('sidebar-collapsed'); // en mobile no colapsamos, abrimos
    }
    function closeMobile() {
      document.body.classList.remove('sidebar-open');
    }
    function toggleDesktopCollapse() {
      document.body.classList.toggle('sidebar-collapsed');
      // si colapsa, cerramos paneles (estético)
      if (document.body.classList.contains('sidebar-collapsed')) {
        qsa('.sidebar .accordion').forEach(b => b.classList.remove('active'));
        qsa('.sidebar .panel').forEach(p => p.style.display = 'none');
      }
    }

    if (btnToggle) {
      btnToggle.addEventListener('click', function () {
        // en pantallas chicas abrimos overlay
        if (window.matchMedia('(max-width: 980px)').matches) openMobile();
        else toggleDesktopCollapse();
      });
    }
    if (btnClose) btnClose.addEventListener('click', closeMobile);
    if (overlay) overlay.addEventListener('click', closeMobile);

    // si se agranda la pantalla, cerramos overlay
    window.addEventListener('resize', function () {
      if (!window.matchMedia('(max-width: 980px)').matches) closeMobile();
    });

    // si no hay sidebar, no hacemos nada
    if (!sidebar) return;
  }

  // =========================
  // ACCORDION: 1 abierto a la vez
  // =========================
  function initAccordion() {
    const sidebar = qs('.sidebar');
    if (!sidebar) return;

    sidebar.addEventListener('click', function (ev) {
      const btn = ev.target.closest('.accordion');
      if (!btn) return;

      // si está colapsado en desktop, no abrir paneles
      if (document.body.classList.contains('sidebar-collapsed')) return;

      const all = qsa('.sidebar .accordion');
      const panels = qsa('.sidebar .panel');

      // cerrar otros
      all.forEach(b => {
        if (b !== btn) b.classList.remove('active');
      });
      panels.forEach(p => {
        // panel asociado es el nextElementSibling del botón
        const owner = p.previousElementSibling;
        if (owner !== btn) p.style.display = 'none';
      });

      // toggle actual
      btn.classList.toggle('active');
      const panel = btn.nextElementSibling;
      if (panel && panel.classList.contains('panel')) {
        panel.style.display = (panel.style.display === 'block') ? 'none' : 'block';
      }
    });
  }

  // =========================
  // INPUT: UPPERCASE ON BLUR (global)
  // =========================
  function initUppercase() {
    document.addEventListener('blur', function (ev) {
      const el = ev.target;
      if (!el) return;
      if (el.matches && el.matches('[data-uppercase="1"]')) {
        el.value = (el.value || '').toString().toUpperCase();
      }
    }, true);
  }

  // =========================
  // BOOT
  // =========================
  document.addEventListener('DOMContentLoaded', function () {
    initSidebarShell();
    initAccordion();
    initUppercase();
  });
})();
