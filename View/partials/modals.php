<?php
/**
 * Modal tambah/ubah per tabel (dibuat otomatis dari Model::fields()) + modal konfirmasi hapus.
 * Variabel: $modals = [entity => Model]
 */
$csrf = Session::csrfToken();
?>
<?php foreach ($modals as $key => $model): ?>
<dialog class="modal" id="modal-<?= Html::e($key) ?>" data-entity="<?= Html::e($key) ?>" data-title="<?= Html::e($model->label()) ?>">
  <form method="post" action="action.php" class="modal-card" autocomplete="off">
    <input type="hidden" name="csrf" value="<?= Html::e($csrf) ?>">
    <input type="hidden" name="entity" value="<?= Html::e($key) ?>">
    <input type="hidden" name="action" value="create">
    <input type="hidden" name="id" value="">

    <header class="modal-head">
      <h3 class="modal-title">Tambah <?= Html::e($model->label()) ?></h3>
      <button type="button" class="icon-btn" data-close aria-label="Tutup"><?= Icon::svg('x', 18) ?></button>
    </header>

    <div class="modal-body">
      <?php foreach ($model->fields() as $f): ?>
      <div class="field">
        <label for="f-<?= Html::e($key . '-' . $f['name']) ?>"><?= Html::e($f['label']) ?></label>
        <?php $id = 'f-' . $key . '-' . $f['name']; ?>
        <?php if ($f['type'] === 'select'): ?>
          <select id="<?= Html::e($id) ?>" name="<?= Html::e($f['name']) ?>" <?= !empty($f['required']) ? 'required' : '' ?>>
            <?php foreach ($f['options'] as $val => $text): ?>
              <option value="<?= Html::e($val) ?>"><?= Html::e($text) ?></option>
            <?php endforeach; ?>
          </select>
        <?php else: ?>
          <input id="<?= Html::e($id) ?>" name="<?= Html::e($f['name']) ?>"
                 type="<?= Html::e($f['type']) ?>"
                 <?= !empty($f['required']) ? 'required' : '' ?>
                 <?= $f['type'] === 'password' ? 'data-required-create="1" autocomplete="new-password"' : '' ?>
                 <?= ($f['type'] === 'number' && isset($f['min'])) ? 'min="' . Html::e($f['min']) . '"' : '' ?>
                 <?= ($f['type'] === 'number' && isset($f['max'])) ? 'max="' . Html::e($f['max']) . '"' : '' ?>
                 <?= ($f['type'] === 'number' && isset($f['step'])) ? 'step="' . Html::e($f['step']) . '"' : '' ?>
                 <?= ($f['type'] !== 'number' && isset($f['max'])) ? 'maxlength="' . Html::e($f['max']) . '"' : '' ?>>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>

    <footer class="modal-foot">
      <button type="button" class="btn btn-outline" data-close>Batal</button>
      <button type="submit" class="btn btn-dark">Simpan</button>
    </footer>
  </form>
</dialog>
<?php endforeach; ?>

<dialog class="modal modal-sm" id="modal-delete">
  <form method="post" action="action.php" class="modal-card">
    <input type="hidden" name="csrf" value="<?= Html::e($csrf) ?>">
    <input type="hidden" name="entity" value="">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id" value="">

    <header class="modal-head">
      <h3 class="modal-title">Hapus Data</h3>
      <button type="button" class="icon-btn" data-close aria-label="Tutup"><?= Icon::svg('x', 18) ?></button>
    </header>
    <div class="modal-body">
      <p class="confirm-text">Hapus data <strong id="delete-name"></strong>? Tindakan ini tidak dapat dibatalkan.</p>
    </div>
    <footer class="modal-foot">
      <button type="button" class="btn btn-outline" data-close>Batal</button>
      <button type="submit" class="btn btn-danger"><?= Icon::svg('trash', 16) ?>Hapus</button>
    </footer>
  </form>
</dialog>
