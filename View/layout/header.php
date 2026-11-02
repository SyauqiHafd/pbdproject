<?php

$displayName = ucfirst($user['username']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= Html::e($pageTitle) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Mulish:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
<div class="app">

  <aside class="sidebar">
    <div class="brand">
      <span class="brand-mark">J</span>
      <span class="brand-text"><strong>Jaya Group</strong><small>Pengadaan</small></span>
    </div>

    <nav class="nav">
      <?php foreach ($menus as $group): ?>
        <?php if (!empty($group['heading'])): ?>
          <h6 class="nav-heading"><?= Html::e($group['heading']) ?></h6>
        <?php endif; ?>
        <ul>
          <?php foreach ($group['items'] as $item): ?>
          <li>
            <a href="?menu=<?= Html::e($item['key']) ?>" data-menu="<?= Html::e($item['key']) ?>"
               class="nav-link<?= $item['key'] === $active ? ' active' : '' ?>">
              <?= Icon::svg($item['icon']) ?><span><?= Html::e($item['label']) ?></span>
            </a>
          </li>
          <?php endforeach; ?>
        </ul>
      <?php endforeach; ?>
    </nav>

    <div class="sidebar-user">
      <span class="avatar"><?= Html::e(Str::initial($user['username'])) ?></span>
      <span class="sidebar-user-text">
        <strong><?= Html::e($displayName) ?></strong>
        <small><?= Html::e($user['role_name'] ?: ucfirst($user['role'])) ?></small>
      </span>
    </div>
  </aside>

  <div class="main">
    <header class="topbar">
      <h1 class="welcome">Selamat datang, <?= Html::e($displayName) ?>!</h1>
      <span class="topbar-divider"></span>
      <label class="search">
        <?= Icon::svg('search', 17) ?>
        <input type="text" id="global-search" placeholder="<?= Html::e($searchPlaceholder) ?>" autocomplete="off">
      </label>
      <div class="topbar-right">
        <span class="role-chip"><?= Html::e($roleChip) ?></span>
        <a class="btn btn-dark" href="logout.php"><?= Icon::svg('logout', 16) ?>Logout</a>
      </div>
    </header>

    <main class="content">
