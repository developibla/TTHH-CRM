/* ==========================================================
   UI GLOBAL PRO (TTHH)
   - Toast
   - Modal open/close
   - Confirm delete modal (global)
   - Uppercase blur (data-uppercase="1")
   - Catálogos: modal Nuevo/Editar + AJAX save + AJAX delete + upsert row
   ========================================================== */

(function () {
  'use strict';

  function qs(sel, root) { return (root || document).querySelector(sel); }
  function qsa(sel, root) { return Array.from((root || document).querySelectorAll(sel)); }

  function ensureToast() {
    let t = qs('#toast');
    if (!t) {
      t = document.createElement('div');
      t.id = 'toast';
      document.body.appendChild(t);
    }
    return t;
  }

  function showToast(message, ok = true) {
    const t = ensureToast();
    t.textContent = message || '';
    t.className = '';
    t.classList.add('show', ok ? 'ok' : 'err');
    window.clearTimeout(showToast._to);
    showToast._to = window.setTimeout(() => t.classList.remove('show'), 2200);
  }

  function lockBody(lock) {
    document.body.classList.toggle('modal-open', !!lock);
  }

  function openModalById(id) {
    const m = qs(id);
    if (!m) return;
    m.classList.add('is-open');
    m.setAttribute('aria-hidden', 'false');
    lockBody(true);
  }

  function closeModal(modalEl) {
    if (!modalEl) return;
    modalEl.classList.remove('is-open');
    modalEl.setAttribute('aria-hidden', 'true');
    // si no hay ningún modal abierto, desbloquear
    const anyOpen = qsa('.modal.is-open').length > 0;
    lockBody(anyOpen);
  }

  // =========================
  // Confirm Modal (global)
  // =========================
  function ensureConfirmModal() {
    let cm = qs('#confirmModal');
    if (cm) return cm;

    cm = document.createElement('div');
    cm.className = 'modal';
    cm.id = 'confirmModal';
    cm.setAttribute('aria-hidden', 'true');
    cm.innerHTML = `
      <div class="modal-overlay" data-confirm-overlay></div>
      <div class="modal-dialog sm" role="dialog" aria-modal="true">
        <div class="modal-head">
          <div>
            <div class="modal-title" data-confirm-title>Confirmar</div>
            <div class="modal-sub" style="max-width:380px;" data-confirm-msg>¿Está seguro?</div>
          </div>
          <button class="btn-icon" type="button" data-confirm-no title="Cerrar">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
              <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
            </svg>
          </button>
        </div>
        <div class="modal-body">
          <div style="color:var(--muted);font-size:13px;">Esta acción no se puede deshacer.</div>
        </div>
        <div class="modal-actions">
          <button class="btn btn-outline" type="button" data-confirm-no>Cancelar</button>
          <button class="btn btn-danger" type="button" data-confirm-yes>Eliminar</button>
        </div>
      </div>
    `;
    document.body.appendChild(cm);
    return cm;
  }

  function openConfirm(message, onYes) {
    const cm = ensureConfirmModal();
    const msg = qs('[data-confirm-msg]', cm);
    if (msg) msg.textContent = message || '¿Está seguro?';

    openModalById('#confirmModal');

    // limpiar handler anterior
    if (openConfirm._yesHandler) {
      const yesBtnOld = qs('[data-confirm-yes]', cm);
      if (yesBtnOld) yesBtnOld.removeEventListener('click', openConfirm._yesHandler);
    }

    const yesBtn = qs('[data-confirm-yes]', cm);
    openConfirm._yesHandler = function () {
      try { onYes && onYes(); } finally { closeModal(cm); }
    };
    if (yesBtn) yesBtn.addEventListener('click', openConfirm._yesHandler);

    const closeAll = () => closeModal(cm);

    const overlay = qs('[data-confirm-overlay]', cm);
    if (overlay) overlay.onclick = closeAll;
    qsa('[data-confirm-no]', cm).forEach(btn => btn.onclick = closeAll);

    // ESC
    openConfirm._escHandler = function (ev) {
      if (ev.key === 'Escape') {
        closeAll();
        document.removeEventListener('keydown', openConfirm._escHandler);
      }
    };
    document.addEventListener('keydown', openConfirm._escHandler);
  }

  // =========================
  // Uppercase blur global
  // =========================
  document.addEventListener('blur', function (ev) {
    const el = ev.target;
    if (!(el instanceof HTMLInputElement)) return;
    if (el.getAttribute('data-uppercase') === '1') {
      el.value = (el.value || '').toUpperCase();
    }
  }, true);

  // =========================
  // Generic modal close wiring
  // - overlay: [data-modal-overlay]
  // - close: [data-modal-close]
  // - cancel: [data-modal-cancel]
  // =========================
  document.addEventListener('click', function (ev) {
    const overlay = ev.target.closest('[data-modal-overlay]');
    if (overlay) {
      const modal = overlay.closest('.modal');
      closeModal(modal);
      return;
    }

    const closeBtn = ev.target.closest('[data-modal-close], [data-modal-cancel]');
    if (closeBtn) {
      const modal = closeBtn.closest('.modal');
      closeModal(modal);
      return;
    }
  });

  document.addEventListener('keydown', function (ev) {
    if (ev.key !== 'Escape') return;
    const top = qsa('.modal.is-open').slice(-1)[0];
    if (top) closeModal(top);
  });

  // =========================
  // CATÁLOGOS (convención)
  // - Botón nuevo: [data-cat-add]
  // - Edit: [data-cat-edit] con data-row='{"id":..,"Campo":..}'
  // - Modal: #catModal con data-cat-title
  // - Form:  #catForm con input hidden _ajax=1
  // - Tabla: .table (la primera en la página) o [data-cat-grid]
  // - Delete forms: .js-del-form (post catalogos_post)
  // =========================

  function getCatalogContext() {
    const modal = qs('#catModal');
    const form = qs('#catForm');
    if (!modal || !form) return null;

    // tabla principal (primera tabla .table dentro de la card)
    const grid = qs('table.table');
    return { modal, form, grid };
  }

  function setCatalogModalMode(mode, row) {
    const ctx = getCatalogContext();
    if (!ctx) return;
    const { modal, form } = ctx;

    const titleEl = qs('[data-modal-title]', modal);
    const subEl = qs('[data-modal-subtitle]', modal);

    const catTitle = modal.getAttribute('data-cat-title') || 'Catálogo';
    const idInput = qs('input[name="id"]', form);
    const activoSel = qs('select[name="Activo"]', form);

    if (mode === 'edit') {
      if (titleEl) titleEl.textContent = catTitle + ' · Editar';
      if (subEl) subEl.textContent = 'Actualice los datos y guarde.';
      if (idInput) idInput.value = row && row.id ? String(row.id) : '';
      if (activoSel) activoSel.value = String((row && row.Activo != null) ? row.Activo : 1);

      qsa('[data-field-name]', form).forEach(inp => {
        const fn = inp.getAttribute('data-field-name');
        if (!fn) return;
        inp.value = (row && row[fn] != null) ? String(row[fn]) : '';
      });

      const first = qs('[data-field-name]', form);
      if (first) setTimeout(() => first.focus(), 80);
    } else {
      if (titleEl) titleEl.textContent = catTitle + ' · Nuevo';
      if (subEl) subEl.textContent = 'Complete los datos y guarde.';
      form.reset();
      if (idInput) idInput.value = '';
      if (activoSel) activoSel.value = '1';

      const first = qs('[data-field-name]', form);
      if (first) setTimeout(() => first.focus(), 80);
    }

    openModalById('#catModal');
  }

  function updateAllCsrf(newCsrf) {
    if (!newCsrf) return;
    qsa('input[name="_csrf"]').forEach(i => {
      if (i instanceof HTMLInputElement) i.value = newCsrf;
    });
  }

  function safeJsonFromAttr(el, attr) {
    const s = el.getAttribute(attr) || '{}';
    try { return JSON.parse(s); } catch { return {}; }
  }

  function upsertCatalogRow(grid, row) {
    if (!grid || !row || !row.id) return;
    const id = String(row.id);

    let tr = qs('tr[data-row-id="' + CSS.escape(id) + '"]', grid);
    const isNew = !tr;

    // lista de campos en orden: se infiere del modal (inputs con data-field-name)
    const ctx = getCatalogContext();
    const fields = ctx ? qsa('#catForm [data-field-name]').map(i => i.getAttribute('data-field-name')).filter(Boolean) : [];

    if (!tr) {
      const tbody = qs('tbody', grid);
      if (!tbody) return location.reload();

      tr = document.createElement('tr');
      tr.setAttribute('data-row-id', id);

      // columnas: acciones, id, fields..., activo
      const tds = [];

      // acciones
      tds.push(`
        <td class="col-actions">
          <div class="table-actions">
            <a href="#" class="btn btn-ico-sm btn-outline" title="Editar"
              data-cat-edit="1"
              data-row="">
              <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
                <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10z"/>
                <path d="M11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5z"/>
              </svg>
            </a>

            <form class="js-del-form" method="post" action="index.php?r=catalogos_post" style="display:inline;">
              <input type="hidden" name="_csrf" value="">
              <input type="hidden" name="t" value="">
              <input type="hidden" name="delete_id" value="">
              <button class="btn btn-ico-sm btn-danger" type="submit" title="Eliminar">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
                  <path d="M5.5 5.5A.5.5 0 0 1 6 6v7a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5.5a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0V6zm3 .5a.5.5 0 0 0-1 0v7a.5.5 0 0 0 1 0V6z"/>
                  <path d="M14.5 3a1 1 0 0 1-1 1H13l-1 11a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2L3 4h-.5a1 1 0 0 1 0-2H5.5l1-1h3l1 1H14.5a1 1 0 0 1 1 1z"/>
                </svg>
              </button>
            </form>
          </div>
        </td>
      `);

      // id
      tds.push(`<td data-col="id"></td>`);

      // campos
      fields.forEach(fn => {
        tds.push(`<td data-col="${fn}"></td>`);
      });

      // activo
      tds.push(`<td data-col="Activo"></td>`);

      tr.innerHTML = tds.join('');
      tbody.prepend(tr);

      // si había “Sin registros.”, sacarlo
      qsa('tr', tbody).forEach(r => {
        const td = qs('td[colspan="99"]', r);
        if (td) r.remove();
      });
    }

    // set cells
    const idCell = qs('[data-col="id"]', tr);
    if (idCell) idCell.textContent = id;

    fields.forEach(fn => {
      const cell = qs('[data-col="' + CSS.escape(fn) + '"]', tr);
      if (!cell) return;
      const v = row[fn] != null ? String(row[fn]) : '';
      cell.textContent = v;
    });

    const actCell = qs('[data-col="Activo"]', tr);
    if (actCell) actCell.textContent = (Number(row.Activo ?? 1) === 1) ? 'Sí' : 'No';

    // actualizar acciones (data-row, delete_id, csrf, t)
    const editA = qs('[data-cat-edit]', tr);
    if (editA) {
      const rowJson = JSON.stringify(row).replace(/</g, '\\u003c').replace(/>/g, '\\u003e').replace(/&/g, '\\u0026');
      editA.setAttribute('data-row', rowJson);
    }

    const delForm = qs('form.js-del-form', tr);
    if (delForm) {
      const csrf = (qs('#catForm input[name="_csrf"]') || {}).value || '';
      const tKey = (qs('#catForm input[name="t"]') || {}).value || (new URLSearchParams(location.search)).get('t') || '';
      const csrfInput = qs('input[name="_csrf"]', delForm);
      const tInput = qs('input[name="t"]', delForm);
      const idInput = qs('input[name="delete_id"]', delForm);
      if (csrfInput) csrfInput.value = csrf;
      if (tInput) tInput.value = tKey;
      if (idInput) idInput.value = id;
    }

    // pequeño “flash” visual opcional al actualizar
    if (!isNew) {
      tr.style.transition = 'background .25s';
      tr.style.background = 'rgba(167, 243, 208, .25)';
      setTimeout(() => { tr.style.background = ''; }, 400);
    }
  }

  // Click handlers: Add/Edit
  document.addEventListener('click', function (ev) {
    const addBtn = ev.target.closest('[data-cat-add]');
    if (addBtn) {
      ev.preventDefault();
      setCatalogModalMode('new', null);
      return;
    }

    const editBtn = ev.target.closest('[data-cat-edit]');
    if (editBtn) {
      ev.preventDefault();
      const row = safeJsonFromAttr(editBtn, 'data-row');
      setCatalogModalMode('edit', row);
      return;
    }
  });

  // AJAX submit catForm
  document.addEventListener('submit', async function (ev) {
    const form = ev.target;
    if (!(form instanceof HTMLFormElement)) return;
    if (form.id !== 'catForm') return;

    ev.preventDefault();

    // uppercase seguridad antes de enviar
    qsa('[data-uppercase="1"]', form).forEach(inp => {
      if (inp instanceof HTMLInputElement) inp.value = (inp.value || '').toUpperCase();
    });

    try {
      const fd = new FormData(form);
      const res = await fetch(form.action, { method: 'POST', body: fd });
      const json = await res.json();

      if (json && json.csrf) updateAllCsrf(json.csrf);

      if (!json || !json.ok) {
        showToast((json && json.message) ? json.message : 'Error', false);
        return;
      }

      showToast(json.message || 'OK', true);

      // update grid
      const ctx = getCatalogContext();
      if (ctx && ctx.grid && json.row && json.row.id) {
        upsertCatalogRow(ctx.grid, json.row);
      }

      // close modal
      closeModal(qs('#catModal'));
    } catch (e) {
      showToast('No se pudo guardar (AJAX).', false);
    }
  });

  // AJAX delete with confirm (forms .js-del-form)
  document.addEventListener('submit', function (ev) {
    const f = ev.target;
    if (!(f instanceof HTMLFormElement)) return;
    if (!f.classList.contains('js-del-form')) return;

    ev.preventDefault();

    openConfirm('¿Eliminar este registro?', async function () {
      try {
        const fd = new FormData(f);
        fd.append('_ajax', '1');

        const res = await fetch(f.action, { method: 'POST', body: fd });
        const json = await res.json();

        if (json && json.csrf) updateAllCsrf(json.csrf);

        if (!json || !json.ok) {
          showToast((json && json.message) ? json.message : 'Error', false);
          return;
        }

        const delId = String(json.deleted_id || fd.get('delete_id') || '');
        const tr = qs('tr[data-row-id="' + CSS.escape(delId) + '"]');
        if (tr) tr.remove();

        showToast(json.message || 'Eliminado', true);
      } catch (e) {
        showToast('No se pudo eliminar (AJAX).', false);
      }
    });
  });

})();
