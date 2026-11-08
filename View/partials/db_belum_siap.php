<?php if (!Model::transaksiSiap()): ?>
<p class="alert alert-error">Function, stored procedure, dan trigger transaksi belum terpasang di database. Impor berurutan
  <code>database/01_functions.sql</code>, <code>02_procedures.sql</code>, lalu <code>03_triggers.sql</code> (lihat README).</p>
<?php endif; ?>
