<?php
$chart = $home['chart'];
$panel = $home['panel'];
?>
<h1 class="page-title"><?= Html::e($home['title']) ?></h1>

<div class="stats stats-<?= count($home['stats']) ?>">
  <?php foreach ($home['stats'] as $s): ?>
  <div class="stat stat-<?= Html::e($s['tone']) ?>">
    <span class="stat-icon"><?= Icon::svg($s['icon'], 20) ?></span>
    <div>
      <h5><?= Html::e($s['label']) ?></h5>
      <p><?= Html::e($s['value']) ?></p>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<div class="home-grid">
  <section class="panel chart-panel">
    <header class="panel-head">
      <div>
        <h2><?= Html::e($chart['title']) ?></h2>
        <span class="muted"><?= Html::e($chart['subtitle']) ?></span>
      </div>
      <div class="chart-total"><strong><?= (int) $chart['total'] ?></strong> <span class="muted">data (total)</span>
        <?= Icon::svg('activity', 34) ?></div>
    </header>

    <div class="chart">
      <div class="chart-axis">
        <?php foreach ($chart['ticks'] as $t): ?><span><?= $t ?></span><?php endforeach; ?>
      </div>
      <div class="chart-plot">
        <?php if ($chart['maxPct'] > 0): ?>
        <div class="chart-max" style="--p: <?= $chart['maxPct'] ?>"><span>max</span></div>
        <?php endif; ?>
        <?php foreach ($chart['items'] as $b): ?>
        <div class="chart-col">
          <div class="bar-area">
            <div class="chart-bar<?= $b['is_max'] ? ' is-max' : '' ?>" style="height: <?= $b['height'] ?>%">
              <span class="bubble"><?= $b['value'] > 0 ? (int) $b['value'] : '-' ?></span>
            </div>
          </div>
          <label class="<?= $b['is_max'] ? 'is-max' : '' ?>"><?= Html::e($b['label']) ?></label>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="panel side-panel">
    <header class="panel-head">
      <h2><?= Html::e($panel['title']) ?></h2>
      <a href="?menu=<?= Html::e($panel['more']) ?>" data-menu="<?= Html::e($panel['more']) ?>" class="muted-link">Lihat semua</a>
    </header>
    <ul class="people">
      <?php foreach ($panel['list'] as $p): ?>
      <li>
        <span class="avatar avatar-soft"><?= Html::e(Str::initial($p['title'])) ?></span>
        <div><strong><?= Html::e($p['title']) ?></strong><small><?= Html::e($p['sub']) ?></small></div>
      </li>
      <?php endforeach; ?>
      <?php if (!$panel['list']): ?><li class="muted">Belum ada data.</li><?php endif; ?>
    </ul>
    <?php if ($panel['action']['add']): ?>
      <button type="button" class="btn btn-outline btn-block" data-add="<?= Html::e($panel['action']['add']) ?>" data-menu="<?= Html::e($panel['action']['menu']) ?>">
        <?= Html::e($panel['action']['label']) ?></button>
    <?php else: ?>
      <a href="?menu=<?= Html::e($panel['action']['menu']) ?>" data-menu="<?= Html::e($panel['action']['menu']) ?>" class="btn btn-outline btn-block">
        <?= Html::e($panel['action']['label']) ?></a>
    <?php endif; ?>
  </section>
</div>
