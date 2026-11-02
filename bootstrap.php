<?php
/**
 * Bootstrap: autoload class (Core, Model, Controller) + mulai session.
 * Nama file class memakai huruf kecil, mis. class Barang => Model/barang.php
 */
define('BASE_PATH', __DIR__);

spl_autoload_register(function (string $nama_class) {
    foreach (['Core', 'Model', 'Controller'] as $folder) {
        $file = BASE_PATH . '/' . $folder . '/' . strtolower($nama_class) . '.php';
        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});

Session::start();
