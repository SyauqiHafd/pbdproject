<?php
require_once __DIR__ . '/bootstrap.php';

$user = (new AuthController())->user();
header('Location: ' . ($user ? AuthController::homeFor($user) : 'login.php'));
exit;
