/* ==========================================================
   UI GLOBAL - MODALES / TOAST / CATALOGOS
   ========================================================== */

(function () {
  'use strict';

  /* ===============================
     HELPERS
     =============================== */

  function qs(sel, ctx = document) {
    return ctx.querySelector(sel);
  }

  function qsa(sel, ctx = document) {
    return Array.from(ctx.querySelectorAll(sel));
  }

  function showToast(msg, type = 'ok', timeout = 2600) {
    let toast = qs('#toast');
    if (!toast) return;

    toast.textContent = msg;
    toast.className = '';
    toast.classList.add(type === 'err' ? 'err' : 'ok', 'show');

    clearTimeout(showToast._t);
    showToast._t = setTimeout(() => {
      toast.classList.remove('show');
    }, timeout);
  }

  function openModal(modal) {
    if (!modal) return;
    modal.classList.add('is-open');
    document.body.classList.add('modal-open');
  }

  function closeModal(modal) {
    if (!modal) return;
    modal.classList.remove('is-open');
    document.body.classList.remove('modal-open');
  }

  function resetForm(form) {
    form.reset();
    qsa('[readonly]', form).forEach(el => el.removeAttribute('readonly'));
  }

  /* ===============================
     UPPERCASE AUTOMÁTICO
     =============================== */
  document.addEventListener('blur', (e) => {
    const el = e.target;
    if (el && el.dataset.uppercase === '1') {
      el.value = el.value.toUpperCase();
    }
  }, true);

  /* ===============================
     MODAL GENERIC CLOSE
     =============================== */
  document.addEventListener('click', (e) => {
    if (e.target.matches('[data-modal-overlay],[data-modal-close],[data-modal-cancel]')) {
      const modal = e.target.closest('.modal');
      closeModal(modal);
    }
  });

  /* ===============================
     CATALOGOS - MODAL ADD / EDIT
     =============================== */

  const catModal = qs('#catModal');
  const catForm  = qs('#catForm');

  if (catModal && catForm) {

    const modalTitle = qs('[data-modal-title]', catModal);
    const modalSub   = qs('[data-modal-subtitle]', catModal);

    const pkInput = qsa('[data-field-name]', catForm)
      .find(i => i.name && i.name.endsWith('Id'));

    function setModeNew(title) {
      resetForm(catForm);

      catForm.querySelector('input[name="id"]').value = '';

      if (pkInput) {
        pkInput.removeAttribute('readonly');
      }

      modalTitle.textContent = title + ' · Nuevo';
      modalSub.textContent = 'Complete los datos y guarde.';

      // foco al primer input editable
      const firstInput = qsa('input:not([type="hidden"]):not([readonly])', catForm)[0];
      if (firstInput) setTimeout(() => firstInput.focus(), 60);
    }

    function setModeEdit(title, data) {
      resetForm(catForm);

      catForm.querySelector('input[name="id"]').value = data.id || '';

      Object.keys(data).forEach(k => {
        const input = catForm.querySelector(`[name="${k}"]`);
        if (input) input.value = data[k];
      });

      // PK manual → readonly
      if (pkInput) {
        pkInput.setAttribute('readonly', 'readonly');
      }

      modalTitle.textContent = title + ' · Editar';
      modalSub.textContent = 'Modifique los datos y guarde los cambios.';

      // foco al primer campo de descripción
      const descInput = qsa('[data-uppercase="1"]', catForm)[0];
      if (descInput) setTimeout(() => descInput.focus(), 60);
    }

    /* ===== BOTÓN AGREGAR ===== */
    document.addEventListener('click', (e) => {
      const btn = e.target.closest('[data-cat-add]');
      if (!btn) return;

      const title = catModal.dataset.catTitle || 'Registro';
      setModeNew(title);
      openModal(catModal);
    });

    /* ===== BOTÓN EDITAR ===== */
    document.addEventListener('click', (e) => {
      const btn = e.target.closest('[data-cat-edit]');
      if (!btn) return;

      e.preventDefault();

      let row;
      try {
        row = JSON.parse(btn.dataset.row || '{}');
      } catch {
        row = {};
      }

      const title = catModal.dataset.catTitle || 'Registro';
      setModeEdit(title, row);
      openModal(catModal);
    });
  }

  /* ===============================
     CONFIRMACIÓN DELETE (FORM)
     =============================== */
  document.addEventListener('submit', (e) => {
    const form = e.target.closest('.js-del-form');
    if (!form) return;

    if (!confirm('¿Eliminar este registro?')) {
      e.preventDefault();
    }
  });

  /* ===============================
     SIDEBAR COLLAPSE (si existe)
     =============================== */
  document.addEventListener('click', (e) => {
    if (e.target.closest('[data-toggle-sidebar]')) {
      document.body.classList.toggle('sidebar-collapsed');
    }
    if (e.target.closest('[data-close-sidebar]')) {
      document.body.classList.remove('sidebar-collapsed');
    }
  });

})();
