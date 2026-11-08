    </main>
  </div>
</div>

<?php
if (!empty($modals)) {
    include __DIR__ . '/../partials/modals.php';
}
if (!empty($transaksiModals)) {
    include __DIR__ . '/../partials/transaksi_modals.php';
}
?>

<?php $flashes = Session::pullFlash(); ?>
<?php if ($flashes): ?>
<div class="toasts">
  <?php foreach ($flashes as $f): ?>
    <div class="toast toast-<?= Html::e($f['type']) ?>"><?= Html::e($f['message']) ?></div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<script src="assets/js/app.js"></script>
</body>
</html>
