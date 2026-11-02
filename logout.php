<?php
require_once __DIR__ . '/bootstrap.php';

(new AuthController())->logout();
header('Location: login.php?out=1');
exit;
