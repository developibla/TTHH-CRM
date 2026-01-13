<?php
declare(strict_types=1);
?>
  <div class="footer">
    <?= e(date('Y')) ?> · Gestion de Talento Humano - powered by: <a href="https://iblasoluciones.com" target="_blank" rel="noopener noreferrer">IBLA Soluciones</a>

  </div>
</div> <!-- .container -->

<script src="public/assets/js/ui.js"></script>
<script src="public/assets/js/app.js"></script>

<script>
(function(){
  // ===== helpers =====
  const $  = (sel, root=document) => root.querySelector(sel);
  const $$ = (sel, root=document) => Array.from(root.querySelectorAll(sel));

  // ===== Sidebar open/close (mobile) + collapse (desktop) =====
  const btnToggle = $('[data-toggle-sidebar]');
  const btnClose  = $('[data-close-sidebar]');
  const overlay   = $('[data-sidebar-overlay]');

  const isMobile = () => window.matchMedia('(max-width: 980px)').matches;

  function openSidebar(){
    document.body.classList.add('sidebar-open');
  }
  function closeSidebar(){
    document.body.classList.remove('sidebar-open');
  }
  function toggleSidebar(){
    if (isMobile()){
      document.body.classList.contains('sidebar-open') ? closeSidebar() : openSidebar();
    } else {
      document.body.classList.toggle('sidebar-collapsed');
      // en desktop nunca dejamos abierto como drawer
      closeSidebar();
    }
  }

  btnToggle && btnToggle.addEventListener('click', (e)=>{ e.preventDefault(); toggleSidebar(); });
  btnClose  && btnClose.addEventListener('click',  (e)=>{ e.preventDefault(); closeSidebar(); });
  overlay   && overlay.addEventListener('click',   (e)=>{ e.preventDefault(); closeSidebar(); });

  // Esc cierra drawer
  document.addEventListener('keydown', (e)=>{
    if (e.key === 'Escape') closeSidebar();
  });

  // si cambia tamaño, limpiamos estados raros
  window.addEventListener('resize', ()=>{
    if (!isMobile()){
      closeSidebar();
    }
  });

  // ===== Accordion =====
  const root = $('[data-accordion-root]');
  if (!root) return;

  const openKey = root.getAttribute('data-open-key') || '';

  function setPanel(btn, open){
    btn.classList.toggle('active', open);
    btn.setAttribute('aria-expanded', open ? 'true' : 'false');

    const panel = btn.nextElementSibling;
    if (!panel || !panel.classList.contains('panel')) return;
    panel.style.display = open ? 'block' : 'none';
  }

  const buttons = $$('button.accordion[data-acc]', root);

  // estado inicial (abre el openKey)
  buttons.forEach(btn=>{
    const key = btn.getAttribute('data-acc') || '';
    setPanel(btn, key === openKey);
  });

  // click: abre uno y cierra los demás
  buttons.forEach(btn=>{
    btn.addEventListener('click', ()=>{
      const willOpen = !btn.classList.contains('active');
      buttons.forEach(b => setPanel(b, false));
      setPanel(btn, willOpen);
    });
  });

  // si hago click en un link del panel en mobile, cierro el drawer
  $$('div.panel a', root).forEach(a=>{
    a.addEventListener('click', ()=>{
      if (isMobile()) closeSidebar();
    });
  });

})();
</script>

</body>
</html>
