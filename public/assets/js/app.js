// public/assets/js/app.js
(function () {
  "use strict";

  // ===== Uppercase on blur =====
  function toUpperOnBlur(el) {
    el.addEventListener("blur", function () {
      if (typeof el.value === "string" && el.value.length) {
        el.value = el.value.toUpperCase();
      }
    });
  }

  function initUppercase(root) {
    (root || document)
      .querySelectorAll('input[data-uppercase="1"], textarea[data-uppercase="1"]')
      .forEach(toUpperOnBlur);
  }

  // ===== Toast =====
  function toast(msg, type) {
    let el = document.getElementById("toast");
    if (!el) return alert(msg);

    el.textContent = msg;
    el.classList.remove("ok", "err");
    el.classList.add(type === "err" ? "err" : "ok");
    el.classList.add("show");

    clearTimeout(el._t);
    el._t = setTimeout(() => el.classList.remove("show"), 2600);
  }

  // ===== Modal helpers =====
  function openModal(modal) {
    if (!modal) return;
    modal.classList.add("is-open");
    modal.setAttribute("aria-hidden", "false");
    document.body.classList.add("modal-open");
  }

  function closeModal(modal) {
    if (!modal) return;
    modal.classList.remove("is-open");
    modal.setAttribute("aria-hidden", "true");
    document.body.classList.remove("modal-open");
  }

  function firstEditableField(modal) {
    return modal.querySelector(
      'input:not([type="hidden"]):not([type="time"]):not([disabled]), textarea:not([disabled])'
    );
  }

  // ===== Sidebar collapse / overlay =====
  function initSidebar() {
    const body = document.body;
    const btn = document.querySelector("[data-toggle-sidebar]");
    const overlay = document.querySelector("[data-sidebar-overlay]");
    const closeBtn = document.querySelector("[data-close-sidebar]"); // opcional

    if (!btn) return;

    const KEY = "tthh_sidebar_collapsed";

    function setCollapsed(on) {
      body.classList.toggle("sidebar-collapsed", !!on);
      try { localStorage.setItem(KEY, on ? "1" : "0"); } catch (_) {}
    }

    function isMobile() {
      return window.matchMedia("(max-width: 980px)").matches;
    }

    function openMobileSidebar() {
      body.classList.add("sidebar-open");
    }

    function closeMobileSidebar() {
      body.classList.remove("sidebar-open");
    }

    // cargar estado guardado (solo desktop)
    try {
      const saved = localStorage.getItem(KEY);
      if (saved === "1") setCollapsed(true);
    } catch (_) {}

    // click botón toggle
    btn.addEventListener("click", function () {
      if (isMobile()) {
        // mobile: overlay drawer
        if (body.classList.contains("sidebar-open")) closeMobileSidebar();
        else openMobileSidebar();
        return;
      }
      // desktop: collapse
      setCollapsed(!body.classList.contains("sidebar-collapsed"));
    });

    // overlay click (mobile)
    if (overlay) {
      overlay.addEventListener("click", closeMobileSidebar);
    }
    if (closeBtn) {
      closeBtn.addEventListener("click", closeMobileSidebar);
    }

    // ESC cierra sidebar en mobile
    document.addEventListener("keydown", function (ev) {
      if (ev.key === "Escape") {
        if (body.classList.contains("sidebar-open")) closeMobileSidebar();
      }
    });

    // cuando se agranda de mobile -> desktop, limpiar modo open
    window.addEventListener("resize", function () {
      if (!isMobile()) {
        body.classList.remove("sidebar-open");
      }
    });
  }

  // ===== Confirm modal (for delete) =====
  function initConfirmModal() {
    const modal = document.getElementById("confirmModal");
    if (!modal) return { ask: () => Promise.resolve(false) };

    const overlay = modal.querySelector("[data-confirm-overlay]");
    const btnNo = modal.querySelector("[data-confirm-no]");
    const btnYes = modal.querySelector("[data-confirm-yes]");
    const titleEl = modal.querySelector("[data-confirm-title]");
    const msgEl = modal.querySelector("[data-confirm-msg]");

    let resolver = null;

    function doClose(result) {
      closeModal(modal);
      if (resolver) {
        resolver(result);
        resolver = null;
      }
    }

    if (overlay) overlay.addEventListener("click", () => doClose(false));
    if (btnNo) btnNo.addEventListener("click", () => doClose(false));
    if (btnYes) btnYes.addEventListener("click", () => doClose(true));

    document.addEventListener("keydown", function (ev) {
      if (ev.key === "Escape" && modal.classList.contains("is-open")) doClose(false);
    });

    function ask({ title, message }) {
      if (titleEl) titleEl.textContent = title || "Confirmar";
      if (msgEl) msgEl.textContent = message || "¿Está seguro?";
      openModal(modal);
      if (btnYes) setTimeout(() => btnYes.focus(), 60);

      return new Promise((resolve) => {
        resolver = resolve;
      });
    }

    return { ask };
  }

  // ===== Catalogos Modal + AJAX (save + delete) =====
  function initCatalogosModal() {
    const modal = document.getElementById("catModal");
    if (!modal) return;

    const confirm = initConfirmModal();

    const form = modal.querySelector("form");
    const titleEl = modal.querySelector("[data-modal-title]");
    const subEl = modal.querySelector("[data-modal-subtitle]");
    const btnAdd = document.querySelector("[data-cat-add]");
    const btnClose = modal.querySelector("[data-modal-close]");
    const overlay = modal.querySelector("[data-modal-overlay]");
    const btnCancel = modal.querySelector("[data-modal-cancel]");

    const catTitle = modal.getAttribute("data-cat-title") || "Catálogo";

    function setTitle(mode) {
      if (titleEl) titleEl.textContent = `${catTitle} · ${mode}`;
      if (subEl) subEl.textContent = "Complete los datos y guarde.";
    }

    function setFormNew() {
      setTitle("Nuevo");
      form.querySelectorAll("input, textarea").forEach((el) => {
        const n = el.getAttribute("name") || "";
        if (n === "_csrf" || n === "t") return;
        el.value = "";
      });
      form.querySelectorAll("select").forEach((el) => {
        if (el.name === "Activo") el.value = "1";
      });
      const id = form.querySelector('input[name="id"]');
      if (id) id.value = "";
    }

    function setFormEdit(rowObj) {
      setTitle("Editar");
      const id = form.querySelector('input[name="id"]');
      if (id) id.value = rowObj.id ?? "";

      form.querySelectorAll("[data-field-name]").forEach((inp) => {
        const field = inp.getAttribute("data-field-name");
        inp.value = rowObj[field] ?? "";
      });

      const activoSel = form.querySelector('select[name="Activo"]');
      if (activoSel) activoSel.value = String(rowObj.Activo ?? 1);
    }

    // open new
    if (btnAdd) {
      btnAdd.addEventListener("click", function () {
        setFormNew();
        openModal(modal);
        initUppercase(modal);
        const f = firstEditableField(modal);
        if (f) setTimeout(() => f.focus(), 60);
      });
    }

    // open edit
    document.addEventListener("click", function (ev) {
      const btn = ev.target.closest("[data-cat-edit]");
      if (!btn) return;

      ev.preventDefault();

      const raw = btn.getAttribute("data-row") || "{}";
      let rowObj = {};
      try { rowObj = JSON.parse(raw); } catch (_) { rowObj = {}; }

      setFormEdit(rowObj);
      openModal(modal);
      initUppercase(modal);

      const f = firstEditableField(modal);
      if (f) setTimeout(() => f.focus(), 60);
      if (f && typeof f.select === "function") setTimeout(() => f.select(), 90);
    });

    // close modal
    function doClose() { closeModal(modal); }
    if (btnClose) btnClose.addEventListener("click", doClose);
    if (btnCancel) btnCancel.addEventListener("click", doClose);
    if (overlay) overlay.addEventListener("click", doClose);

    document.addEventListener("keydown", function (ev) {
      if (ev.key === "Escape" && modal.classList.contains("is-open")) doClose();
    });

    // AJAX SAVE
    form.addEventListener("submit", async function (ev) {
      ev.preventDefault();

      const fd = new FormData(form);
      fd.append("_ajax", "1");

      try {
        const res = await fetch(form.getAttribute("action"), {
          method: "POST",
          body: fd,
          headers: { "X-Requested-With": "fetch" },
        });

        const data = await res.json();

        if (!data.ok) {
          toast(data.message || "No se pudo guardar.", "err");
          return;
        }

        const row = data.row || {};
        const id = row.id;

        if (!id) {
          toast("Guardado, pero no se recibió ID.", "err");
          return;
        }

        const tr = document.querySelector(`tr[data-row-id="${id}"]`);
        if (tr) {
          Object.keys(row).forEach((k) => {
            const td = tr.querySelector(`[data-col="${k}"]`);
            if (td) {
              if (k === "Activo") td.textContent = String(row[k]) === "1" ? "Sí" : "No";
              else td.textContent = row[k] ?? "";
            }
          });

          const editBtn = tr.querySelector("[data-cat-edit]");
          if (editBtn) editBtn.setAttribute("data-row", JSON.stringify(row));
        } else {
          const tbody = document.querySelector("table.table tbody");
          if (tbody) {
            const emptyRow = tbody.querySelector("tr td[colspan]");
            if (emptyRow) tbody.innerHTML = "";

            const newTr = document.createElement("tr");
            newTr.setAttribute("data-row-id", id);

            let html = "";
            html += `<td data-col="id">${escapeHtml(String(row.id ?? ""))}</td>`;

            const fields = data.fields || [];
            fields.forEach((f) => {
              html += `<td data-col="${escapeHtml(f)}">${escapeHtml(String(row[f] ?? ""))}</td>`;
            });

            html += `<td data-col="Activo">${String(row.Activo ?? 1) === "1" ? "Sí" : "No"}</td>`;

            const tKey = data.t || "";
            const csrf = data.csrf || "";

            html += `
              <td>
                <div class="table-actions">
                  <a href="#" class="btn btn-ico btn-outline" title="Editar"
                     data-cat-edit="1"
                     data-row='${escapeAttr(JSON.stringify(row))}'>
                    ${iconPencil()}
                  </a>

                  <form class="js-del-form" method="post" action="index.php?r=catalogos_post" style="display:inline;">
                    <input type="hidden" name="_csrf" value="${escapeAttr(csrf)}">
                    <input type="hidden" name="t" value="${escapeAttr(tKey)}">
                    <input type="hidden" name="delete_id" value="${escapeAttr(String(id))}">
                    <button class="btn btn-ico btn-danger" type="submit" title="Eliminar">
                      ${iconTrash()}
                    </button>
                  </form>
                </div>
              </td>
            `;

            newTr.innerHTML = html;
            tbody.prepend(newTr);
          }
        }

        closeModal(modal);
        toast(data.message || "Guardado correctamente.", "ok");
      } catch (e) {
        toast("Error de comunicación al guardar.", "err");
      }
    });

    // AJAX DELETE
    document.addEventListener("submit", async function (ev) {
      const delForm = ev.target.closest("form.js-del-form");
      if (!delForm) return;

      ev.preventDefault();

      const delId = (delForm.querySelector('input[name="delete_id"]') || {}).value || "";
      if (!delId) {
        toast("No se recibió el ID a eliminar.", "err");
        return;
      }

      const ok = await confirm.ask({
        title: "Eliminar registro",
        message: "¿Seguro que desea eliminar este registro? Esta acción no se puede deshacer.",
      });

      if (!ok) return;

      const fd = new FormData(delForm);
      fd.append("_ajax", "1");

      try {
        const res = await fetch(delForm.getAttribute("action"), {
          method: "POST",
          body: fd,
          headers: { "X-Requested-With": "fetch" },
        });

        const data = await res.json();
        if (!data.ok) {
          toast(data.message || "No se pudo eliminar.", "err");
          return;
        }

        const tr = document.querySelector(`tr[data-row-id="${delId}"]`);
        if (tr) tr.remove();

        toast(data.message || "Eliminado correctamente.", "ok");

        const tbody = document.querySelector("table.table tbody");
        if (tbody && tbody.querySelectorAll("tr").length === 0) {
          tbody.innerHTML = `<tr><td colspan="99" style="padding:12px;color:var(--muted);">Sin registros.</td></tr>`;
        }
      } catch (e) {
        toast("Error de comunicación al eliminar.", "err");
      }
    });

    function escapeHtml(s) {
      return String(s)
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
    }
    function escapeAttr(s) { return escapeHtml(s); }

    function iconPencil() {
      return `
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
        <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10z"/>
        <path d="M11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5z"/>
      </svg>`;
    }

    function iconTrash() {
      return `
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
        <path d="M5.5 5.5A.5.5 0 0 1 6 6v7a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5.5a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0V6zm3 .5a.5.5 0 0 0-1 0v7a.5.5 0 0 0 1 0V6z"/>
        <path d="M14.5 3a1 1 0 0 1-1 1H13l-1 11a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2L3 4h-.5a1 1 0 0 1 0-2H5.5l1-1h3l1 1H14.5a1 1 0 0 1 1 1z"/>
      </svg>`;
    }
  }

  document.addEventListener("DOMContentLoaded", function () {
    initUppercase(document);
    initSidebar();
    initCatalogosModal();
  });
})();
