<?php
declare(strict_types=1);

$q = trim((string)($_GET['q'] ?? ''));

$items = colaboradores_list($q);

// combos
$tipoDoc = combo_list('tipodocumento', 'TipoDocumentoId', 'TipoDocumentoDes');
$estadoCivil = combo_list('estadocivil', 'EstadoCivilId', 'EstadoCivilDes');
$paises = combo_list('pais', 'PaisId', 'PaisDes');

$dptos = combo_list('departamento', 'DptoId', 'DptoDes');
$distritos = combo_list('distrito', 'DistritoId', 'DistritoDes');
$localidades = combo_list('localidad', 'LocalidadId', 'LocalidadDes');

$cargos = combo_list('cargo', 'CargoId', 'Cargo');
$areas = combo_list('area', 'AreaId', 'Area');
$sectores = combo_list('sector', 'SectorId', 'Sector');
$turnos = combo_list('turno', 'TurnoId', 'Turno');
$tipos = combo_list('tipo', 'TipoId', 'Tipo');

$formasPago = combo_list('formapago', 'FormaPagoId', 'FormaPagoDes');

$ok = $_SESSION['flash_ok'] ?? '';
$err = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_ok'], $_SESSION['flash_error']);

require __DIR__ . '/../partials/layout_top.php';

function json_attr(array $arr): string {
  return htmlspecialchars(json_encode($arr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8');
}
?>

<style>
.modal{ position:fixed; inset:0; display:none; z-index:9999; }
.modal.is-open{ display:block; }
.modal-overlay{ position:absolute; inset:0; background:rgba(0,0,0,.45); }
.modal-dialog{
  position:relative;
  width:min(1100px, 96vw);
  margin:4vh auto;
  background:rgba(255,255,255,.98);
  border-radius:16px;
  box-shadow:0 18px 50px rgba(0,0,0,.25);
  border:1px solid rgba(255,255,255,.65);
  overflow:hidden;
}
.modal-head{
  display:flex; align-items:center; justify-content:space-between; gap:12px;
  padding:14px 16px;
  border-bottom:1px solid var(--border);
  background:linear-gradient(180deg, rgba(17,138,85,.08), rgba(255,255,255,0));
}
.modal-title{ font-weight:950; margin:0; font-size:15px; }
.modal-sub{ color:var(--muted); font-size:12px; margin-top:2px; }
.modal-body{ padding:14px 16px; max-height: 74vh; overflow:auto; }
.modal-actions{
  display:flex; justify-content:flex-end; gap:10px;
  padding:12px 16px;
  border-top:1px solid var(--border);
  background:rgba(15,42,28,.02);
}
body.modal-open{ overflow:hidden; }

.page-head{ display:flex; justify-content:space-between; align-items:flex-end; gap:12px; flex-wrap:wrap; }
.tools{ display:flex; gap:10px; align-items:center; flex-wrap:wrap; }
.table-actions{ display:flex; gap:8px; align-items:center; justify-content:flex-start; }

.form-grid{
  display:grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap: 12px 14px;
}
@media (max-width: 980px){
  .form-grid{ grid-template-columns: 1fr; }
}

/* Eliminar elegante */
.btn-danger{
  background: rgba(15, 23, 42, .06) !important;
  border: 1px solid rgba(15, 23, 42, .14) !important;
  color: rgba(15, 23, 42, .70) !important;
}
.btn-danger:hover,
.btn-danger:focus{
  background: rgba(220, 53, 69, .10) !important;
  border-color: rgba(220, 53, 69, .35) !important;
  color: rgba(220, 53, 69, .95) !important;
}

.badge{
  display:inline-flex; align-items:center; gap:6px;
  padding:4px 8px; border-radius:999px;
  border:1px solid var(--border);
  background: rgba(15,42,28,.02);
  font-size:12px; font-weight:900;
}
.small-help{ font-size:12px; color:var(--muted); margin-top:6px; }

/* miniaturas */
.thumb{
  margin-top:8px;
  display:flex;
  align-items:center;
  gap:10px;
}
.thumb img{
  width:54px;
  height:54px;
  border-radius:10px;
  object-fit:cover;
  border:1px solid var(--border);
  background:#fff;
}
.thumb a{
  font-size:12px;
  font-weight:900;
  color: var(--primary-2);
}
</style>

<div class="card">
  <div class="page-head">
    <div>
      <h2 style="margin:0;">Colaboradores</h2>
      <div style="color:var(--muted);font-size:13px;margin-top:4px;">
        Legajos del personal (alta / edición / baja).
      </div>
    </div>

    <div class="tools">
      <form method="get" action="index.php" class="searchbar">
        <input type="hidden" name="r" value="colaboradores">
        <div class="search-group">
          <input class="input search-input"
                name="q"
                placeholder="Buscar (nombre, doc, legajo, email, ruc)..."
                value="<?= e($q) ?>">
          <button class="btn btn-ico btn-outline search-btn" type="submit" title="Buscar">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
              <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85zm-5.242.656a5 5 0 1 1 0-10 5 5 0 0 1 0 10z"/>
            </svg>
          </button>

          <?php if ($q !== ''): ?>
            <a class="btn btn-ico btn-outline" href="index.php?r=colaboradores" title="Limpiar">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
              </svg>
            </a>
          <?php endif; ?>
        </div>
      </form>

      <button class="btn btn-primary" type="button" data-colab-add title="Agregar colaborador">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
          <path d="M8 1a.5.5 0 0 1 .5.5V7.5H14.5a.5.5 0 0 1 0 1H8.5V14.5a.5.5 0 0 1-1 0V8.5H1.5a.5.5 0 0 1 0-1H7.5V1.5A.5.5 0 0 1 8 1z"/>
        </svg>
        Agregar
      </button>
    </div>
  </div>

  <?php if ($ok): ?><div class="alert alert-ok" style="margin-top:12px;"><?= e($ok) ?></div><?php endif; ?>
  <?php if ($err): ?><div class="alert alert-error" style="margin-top:12px;"><?= e($err) ?></div><?php endif; ?>

  <div class="table-wrap" style="margin-top:12px;">
    <table class="table">
      <thead>
        <tr>
          <th style="width:110px;">Acciones</th>
          <th>ID</th>
          <th>Legajo</th>
          <th>Apellidos</th>
          <th>Nombres</th>
          <th>Edad</th>
          <th>Doc</th>
          <th>RUC</th>
          <th>Código Reloj</th>
          <th>IPS</th>
          <th>Salario Base</th>
          <th>Plus Cargo</th>
          <th>Bonif. Fam</th>
          <th>Aguinaldo</th>
          <th>Activo</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$items): ?>
          <tr><td colspan="99" style="padding:12px;color:var(--muted);">Sin registros.</td></tr>
        <?php endif; ?>

        <?php foreach ($items as $row): ?>
          <?php
            $rowObj = [
              'ColaboradorId' => (int)$row['ColaboradorId'],
              'Legajo' => (string)($row['Legajo'] ?? ''),
              'Nombres' => (string)($row['Nombres'] ?? ''),
              'Apellidos' => (string)($row['Apellidos'] ?? ''),

              'TipoDocumentoId' => (string)($row['TipoDocumentoId'] ?? ''),
              'NroDocumento' => (string)($row['NroDocumento'] ?? ''),
              'CodigoReloj' => (string)($row['CodigoReloj'] ?? ''), // ✅ IMPORTANTE
              'RUC' => (string)($row['RUC'] ?? ''),

              'EstadoCivilId' => (string)($row['EstadoCivilId'] ?? ''),
              'FechaNacimiento' => (string)($row['FechaNacimiento'] ?? ''),
              'GrupoSanguineo' => (string)($row['GrupoSanguineo'] ?? ''),
              'VencimientoCI' => (string)($row['VencimientoCI'] ?? ''),

              'Email' => (string)($row['Email'] ?? ''),
              'Telefono' => (string)($row['Telefono'] ?? ''),
              'TelefonoParticular' => (string)($row['TelefonoParticular'] ?? ''),
              'Direccion' => (string)($row['Direccion'] ?? ''),

              'PaisId' => (string)($row['PaisId'] ?? ''),
              'DptoId' => (string)($row['DptoId'] ?? ''),
              'DistritoId' => (string)($row['DistritoId'] ?? ''),
              'LocalidadId' => (string)($row['LocalidadId'] ?? ''),

              'CargoId' => (string)($row['CargoId'] ?? ''),
              'AreaId' => (string)($row['AreaId'] ?? ''),
              'SectorId' => (string)($row['SectorId'] ?? ''),
              'TurnoId' => (string)($row['TurnoId'] ?? ''),
              'TipoId' => (string)($row['TipoId'] ?? ''),

              'FormaPagoId' => (string)($row['FormaPagoId'] ?? ''),
              'FechaIngreso' => (string)($row['FechaIngreso'] ?? ''),
              'FechaEgreso' => (string)($row['FechaEgreso'] ?? ''),

              'SalarioBase' => (string)($row['SalarioBase'] ?? ''),
              'PlusCargo' => (string)($row['PlusCargo'] ?? ''),
              'NroAseguradoIPS' => (string)($row['NroAseguradoIPS'] ?? ''),

              'BonificacionFamiliar' => (int)($row['BonificacionFamiliar'] ?? 0),
              'Aguinaldo' => (int)($row['Aguinaldo'] ?? 1),

              'FotoSelfiePath' => (string)($row['FotoSelfiePath'] ?? ''),
              'FotoCIFrentePath' => (string)($row['FotoCIFrentePath'] ?? ''),
              'FotoCIAtrasPath' => (string)($row['FotoCIAtrasPath'] ?? ''),

              'Observacion' => (string)($row['Observacion'] ?? ''),

              'Activo' => (int)($row['Activo'] ?? 1),
              'Edad' => $row['Edad'] === null ? '' : (string)$row['Edad'],
              'TipoDocumentoDes' => (string)($row['TipoDocumentoDes'] ?? ''),
            ];

            $sb = (string)($row['SalarioBase'] ?? '');
            $pc = (string)($row['PlusCargo'] ?? '');
            $fmtSb = $sb !== '' ? number_format((float)$sb, 0, ',', '.') : '';
            $fmtPc = $pc !== '' ? number_format((float)$pc, 0, ',', '.') : '';
          ?>

          <tr>
            <td>
              <div class="table-actions">
                <a href="#" class="btn btn-ico btn-outline" title="Editar"
                   data-colab-edit="1"
                   data-row='<?= json_attr($rowObj) ?>'>
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10z"/>
                    <path d="M11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5z"/>
                  </svg>
                </a>

                <form method="post" action="index.php?r=colaboradores_post" style="display:inline;">
                  <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                  <input type="hidden" name="delete_id" value="<?= e((string)$row['ColaboradorId']) ?>">
                  <button class="btn btn-ico btn-danger" type="submit" title="Eliminar"
                          onclick="return confirm('¿Eliminar este colaborador?');">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                      <path d="M5.5 5.5A.5.5 0 0 1 6 6v7a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5.5a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0V6zm3 .5a.5.5 0 0 0-1 0v7a.5.5 0 0 0 1 0V6z"/>
                      <path d="M14.5 3a1 1 0 0 1-1 1H13l-1 11a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2L3 4h-.5a1 1 0 0 1 0-2H5.5l1-1h3l1 1H14.5a1 1 0 0 1 1 1z"/>
                    </svg>
                  </button>
                </form>
              </div>
            </td>

            <td><?= e((string)$row['ColaboradorId']) ?></td>
            <td><?= e((string)($row['Legajo'] ?? '')) ?></td>
            <td><?= e((string)$row['Apellidos']) ?></td>
            <td><?= e((string)$row['Nombres']) ?></td>
            <td><?= e((string)($rowObj['Edad'] ?? '')) ?></td>
            <td><?= e((string)($row['TipoDocumentoDes'] ?? '')) ?> <?= e((string)($row['NroDocumento'] ?? '')) ?></td>
            <td><?= e((string)($row['RUC'] ?? '')) ?></td>
            <td><?= e((string)($row['CodigoReloj'] ?? '')) ?></td>
            <td><?= e((string)($row['NroAseguradoIPS'] ?? '')) ?></td>
            <td><?= e($fmtSb) ?></td>
            <td><?= e($fmtPc) ?></td>
            <td><?= ((int)$row['BonificacionFamiliar'] === 1) ? 'Sí' : 'No' ?></td>
            <td><?= ((int)$row['Aguinaldo'] === 1) ? 'Sí' : 'No' ?></td>
            <td><?= ((int)$row['Activo'] === 1) ? 'Sí' : 'No' ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div style="margin-top:10px;font-size:12px;color:var(--muted);">
    Mostrando hasta 500 registros.
  </div>
</div>

<!-- MODAL -->
<div class="modal" id="colabModal" aria-hidden="true">
  <div class="modal-overlay" data-colab-overlay></div>

  <div class="modal-dialog" role="dialog" aria-modal="true">
    <div class="modal-head">
      <div>
        <div class="modal-title" id="colabTitle">Colaboradores · Nuevo</div>
        <div class="modal-sub" id="colabSub">Complete los datos y guarde.</div>
      </div>

      <button class="btn-icon" type="button" data-colab-close title="Cerrar">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
          <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
        </svg>
      </button>
    </div>

    <div class="modal-body">
      <form method="post" action="index.php?r=colaboradores_post" id="colabForm" enctype="multipart/form-data">
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="ColaboradorId" value="">

        <div class="form-grid">
          <div>
            <label class="label">Legajo</label>
            <input class="input" name="Legajo" data-uppercase="1">
          </div>

          <div>
            <label class="label">Apellidos *</label>
            <input class="input" name="Apellidos" required data-uppercase="1" data-focus-first="1">
          </div>

          <div>
            <label class="label">Nombres *</label>
            <input class="input" name="Nombres" required data-uppercase="1">
          </div>

          <div>
            <label class="label">Tipo Doc.</label>
            <select class="input" name="TipoDocumentoId">
              <option value="">—</option>
              <?php foreach ($tipoDoc as $it): ?>
                <option value="<?= e((string)$it['id']) ?>"><?= e((string)$it['des']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="label">Nro Doc.</label>
            <input class="input" name="NroDocumento" data-uppercase="1">
          </div>

          <div>
            <label class="label">RUC</label>
            <input class="input" name="RUC" data-uppercase="1">
          </div>

          <!-- ✅ NUEVO: Código reloj -->
          <div>
            <label class="label">Código Reloj</label>
            <input class="input" name="CodigoReloj" placeholder="Ej: 4" data-uppercase="1">
            <div class="small-help">Debe coincidir con el ID del reloj (CSV).</div>
          </div>

          <div>
            <label class="label">Fecha nacimiento</label>
            <input class="input" type="date" name="FechaNacimiento" data-dob>
            <div class="small-help">Al cargar fecha, se calcula la edad.</div>
          </div>

          <div>
            <label class="label">Edad</label>
            <input class="input" name="Edad" readonly data-age>
          </div>

          <div>
            <label class="label">Grupo sanguíneo</label>
            <input class="input" name="GrupoSanguineo" placeholder="Ej: O+ / A-" data-uppercase="1">
          </div>

          <div>
            <label class="label">Vencimiento CI</label>
            <input class="input" type="date" name="VencimientoCI">
          </div>

          <div>
            <label class="label">Nro Asegurado IPS</label>
            <input class="input" name="NroAseguradoIPS" data-uppercase="1">
          </div>

          <div>
            <label class="label">Salario base (PYG)</label>
            <input class="input" name="SalarioBase" inputmode="numeric" placeholder="Ej: 3500000" data-money>
          </div>

          <div>
            <label class="label">Plus por cargo (PYG)</label>
            <input class="input" name="PlusCargo" inputmode="numeric" placeholder="Ej: 500000" data-money>
          </div>

          <div>
            <label class="label">Bonificación familiar</label>
            <select class="input" name="BonificacionFamiliar">
              <option value="0" selected>No</option>
              <option value="1">Sí</option>
            </select>
          </div>

          <div>
            <label class="label">Aguinaldo</label>
            <select class="input" name="Aguinaldo">
              <option value="1" selected>Sí</option>
              <option value="0">No</option>
            </select>
          </div>

          <div>
            <label class="label">Email</label>
            <input class="input" type="email" name="Email">
          </div>

          <div>
            <label class="label">Teléfono</label>
            <input class="input" name="Telefono">
          </div>

          <div>
            <label class="label">Teléfono particular</label>
            <input class="input" name="TelefonoParticular" inputmode="numeric" placeholder="Solo números">
          </div>

          <div style="grid-column:1/-1;">
            <label class="label">Dirección</label>
            <input class="input" name="Direccion" data-uppercase="1">
          </div>

          <div>
            <label class="label">Estado civil</label>
            <select class="input" name="EstadoCivilId">
              <option value="">—</option>
              <?php foreach ($estadoCivil as $it): ?>
                <option value="<?= e((string)$it['id']) ?>"><?= e((string)$it['des']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="label">País</label>
            <select class="input" name="PaisId">
              <option value="">—</option>
              <?php foreach ($paises as $it): ?>
                <option value="<?= e((string)$it['id']) ?>"><?= e((string)$it['des']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="label">Departamento</label>
            <select class="input" name="DptoId">
              <option value="">—</option>
              <?php foreach ($dptos as $it): ?>
                <option value="<?= e((string)$it['id']) ?>"><?= e((string)$it['des']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="label">Distrito</label>
            <select class="input" name="DistritoId">
              <option value="">—</option>
              <?php foreach ($distritos as $it): ?>
                <option value="<?= e((string)$it['id']) ?>"><?= e((string)$it['des']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="label">Localidad</label>
            <select class="input" name="LocalidadId">
              <option value="">—</option>
              <?php foreach ($localidades as $it): ?>
                <option value="<?= e((string)$it['id']) ?>"><?= e((string)$it['des']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="label">Cargo</label>
            <select class="input" name="CargoId">
              <option value="">—</option>
              <?php foreach ($cargos as $it): ?>
                <option value="<?= e((string)$it['id']) ?>"><?= e((string)$it['des']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="label">Área</label>
            <select class="input" name="AreaId">
              <option value="">—</option>
              <?php foreach ($areas as $it): ?>
                <option value="<?= e((string)$it['id']) ?>"><?= e((string)$it['des']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="label">Sector</label>
            <select class="input" name="SectorId">
              <option value="">—</option>
              <?php foreach ($sectores as $it): ?>
                <option value="<?= e((string)$it['id']) ?>"><?= e((string)$it['des']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="label">Turno</label>
            <select class="input" name="TurnoId">
              <option value="">—</option>
              <?php foreach ($turnos as $it): ?>
                <option value="<?= e((string)$it['id']) ?>"><?= e((string)$it['des']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="label">Tipo</label>
            <select class="input" name="TipoId">
              <option value="">—</option>
              <?php foreach ($tipos as $it): ?>
                <option value="<?= e((string)$it['id']) ?>"><?= e((string)$it['des']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="label">Forma de pago</label>
            <select class="input" name="FormaPagoId">
              <option value="">—</option>
              <?php foreach ($formasPago as $it): ?>
                <option value="<?= e((string)$it['id']) ?>"><?= e((string)$it['des']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="label">Fecha ingreso</label>
            <input class="input" type="date" name="FechaIngreso">
          </div>

          <div>
            <label class="label">Fecha egreso</label>
            <input class="input" type="date" name="FechaEgreso">
          </div>

          <div>
            <label class="label">Activo</label>
            <select class="input" name="Activo">
              <option value="1" selected>Sí</option>
              <option value="0">No</option>
            </select>
          </div>

          <div style="grid-column:1/-1;">
            <div class="badge">Fotos</div>
            <div class="small-help">Opcional. Formatos: JPG/PNG/WEBP (máx 5MB).</div>
          </div>

          <div>
            <label class="label">Selfie</label>
            <input class="input" type="file" name="FotoSelfie" accept="image/*">
            <div class="thumb" data-preview-selfie></div>
          </div>

          <div>
            <label class="label">CI Frente</label>
            <input class="input" type="file" name="FotoCIFrente" accept="image/*">
            <div class="thumb" data-preview-frente></div>
          </div>

          <div>
            <label class="label">CI Atrás</label>
            <input class="input" type="file" name="FotoCIAtras" accept="image/*">
            <div class="thumb" data-preview-atras></div>
          </div>

          <div style="grid-column:1/-1;">
            <label class="label">Observación</label>
            <textarea class="input" name="Observacion" rows="3" style="resize:vertical;"></textarea>
          </div>
        </div>

        <div class="modal-actions">
          <button class="btn btn-outline" type="button" data-colab-cancel>Cancelar</button>
          <button class="btn btn-primary" type="submit" title="Guardar">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
(function(){
  const modal = document.getElementById('colabModal');
  const form  = document.getElementById('colabForm');
  const title = document.getElementById('colabTitle');

  if(!modal || !form) return;

  const open = () => { modal.classList.add('is-open'); document.body.classList.add('modal-open'); };
  const close = () => { modal.classList.remove('is-open'); document.body.classList.remove('modal-open'); };

  const moneyToDigits = (v) => (v || '').toString().replace(/[^\d]/g,'');
  const formatPYG = (v) => {
    const d = moneyToDigits(v);
    if(!d) return '';
    return d.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  };

  const calcAge = (iso) => {
    if(!iso) return '';
    const d = new Date(iso + 'T00:00:00');
    if (isNaN(d.getTime())) return '';
    const today = new Date();
    let age = today.getFullYear() - d.getFullYear();
    const m = today.getMonth() - d.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < d.getDate())) age--;
    return (age >= 0 && age <= 120) ? String(age) : '';
  };

  const pvSelfie = form.querySelector('[data-preview-selfie]');
  const pvFrente = form.querySelector('[data-preview-frente]');
  const pvAtras  = form.querySelector('[data-preview-atras]');

  const setThumb = (container, path) => {
    if(!container) return;
    if(!path){
      container.innerHTML = '';
      return;
    }
    const safe = String(path).replace(/"/g,'&quot;');
    container.innerHTML =
      '<img src="'+ safe +'" alt="Imagen">' +
      '<a href="'+ safe +'" target="_blank" rel="noopener">Ver</a>';
  };

  const resetForm = () => {
    form.reset();
    form.querySelector('input[name="ColaboradorId"]').value = '';
    title.textContent = 'Colaboradores · Nuevo';
    form.querySelector('[data-age]').value = '';
    setThumb(pvSelfie, '');
    setThumb(pvFrente, '');
    setThumb(pvAtras, '');
  };

  // Nuevo
  document.querySelector('[data-colab-add]')?.addEventListener('click', (e)=>{
    e.preventDefault();
    resetForm();
    open();
    const first = form.querySelector('[data-focus-first="1"]');
    if(first) setTimeout(()=>first.focus(), 40);
  });

  // Editar
  document.querySelectorAll('[data-colab-edit]').forEach(a=>{
    a.addEventListener('click', (e)=>{
      e.preventDefault();
      resetForm();

      const row = JSON.parse(a.getAttribute('data-row') || '{}');
      title.textContent = 'Colaboradores · Editar #' + (row.ColaboradorId || '');

      Object.keys(row).forEach(k=>{
        const el = form.querySelector(`[name="${k}"]`);
        if(!el) return;
        el.value = (row[k] ?? '');
      });

      // edad (readonly)
      form.querySelector('[data-age]').value = calcAge(form.querySelector('[data-dob]').value);

      // money visual
      form.querySelectorAll('[data-money]').forEach(inp=>{
        inp.value = formatPYG(inp.value);
      });

      // ✅ miniaturas reales
      setThumb(pvSelfie, row.FotoSelfiePath || '');
      setThumb(pvFrente, row.FotoCIFrentePath || '');
      setThumb(pvAtras,  row.FotoCIAtrasPath || '');

      open();
      const first = form.querySelector('[data-focus-first="1"]');
      if(first) setTimeout(()=>first.focus(), 40);
    });
  });

  // close handlers
  modal.querySelector('[data-colab-overlay]')?.addEventListener('click', close);
  modal.querySelector('[data-colab-close]')?.addEventListener('click', close);
  modal.querySelector('[data-colab-cancel]')?.addEventListener('click', close);

  // uppercase on blur
  form.querySelectorAll('[data-uppercase="1"]').forEach(inp=>{
    inp.addEventListener('blur', ()=>{ inp.value = (inp.value || '').toUpperCase(); });
  });

  // money formatting (visual) => backend limpia a dígitos
  form.querySelectorAll('[data-money]').forEach(inp=>{
    inp.addEventListener('input', ()=>{ inp.value = formatPYG(inp.value); });
    inp.addEventListener('blur', ()=>{ inp.value = formatPYG(inp.value); });
  });

  // edad según fecha nacimiento
  const dob = form.querySelector('[data-dob]');
  const age = form.querySelector('[data-age]');
  if (dob && age) {
    dob.addEventListener('change', ()=>{ age.value = calcAge(dob.value); });
    dob.addEventListener('blur', ()=>{ age.value = calcAge(dob.value); });
  }

  // Esc
  document.addEventListener('keydown', (ev)=>{
    if(ev.key === 'Escape' && modal.classList.contains('is-open')) close();
  });
})();
</script>

<?php require __DIR__ . '/../partials/layout_bottom.php'; ?>
