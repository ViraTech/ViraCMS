<div class="navbar <?= $this->fixed ?> <?= $this->position ?>">
  <div class="navbar-inner">
    <div class="<?= $this->container ? $this->container : 'container' ?>">
      <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>
    <?php if ($this->brand): ?>
      <a class="brand" href="<?= Yii::app()->createUrl('/site/index') ?>">
        <?php if ($this->brandImageUrl): ?>
          <img src="<?= $this->brandImageUrl ?>" alt="<?= CHtml::encode($this->brandName) ?>">
        <?php else: ?>
          <?= $this->brandName ?>
        <?php endif; ?>
      </a>
    <?php endif; ?>
      <div class="nav-collapse collapse">
        <ul class="nav">
        <?php foreach ($this->getMenuItems() as $item): ?>
          <li<?= $item['active'] ? ' class="active"' : '' ?>>
            <a href="<?= $item['url'] ?>"<?= $item['target'] ? ' target="' . $item['target'] . '"' : '' ?>><?= $item['label'] ?></a>
          </li>
          <?php if (!empty($item['items'])): ?>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle dropdown-toggle-icon" data-toggle="dropdown"><i class="icon-angle-down"></i></a>
              <?php
              $this->render('dropdown-menu', array(
                'items' => $item['items'],
                'header' => $item['label'],
                'level' => 1,
              ))
              ?>
          </li>
          <?php endif; ?>
        <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>
</div>
