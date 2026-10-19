<?php
spl_autoload_register(function (string $nama_class) {
    $file = __DIR__ . "/Model/" . strtolower($nama_class) . ".php";

    if (file_exists($file)) {
        include_once($file);
    }
});