<?php
// Front controller untuk tambah / ubah / hapus data (hanya role admin).
require_once __DIR__ . '/bootstrap.php';

(new CrudController())->handle();
