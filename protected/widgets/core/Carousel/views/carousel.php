<?php $model = $this->getCarousel(); ?>
<?php if ($model): ?>
  <div id="carousel-<?= $this->id ?>" class="carousel slide">
    <ol class="carousel-indicators">
      <li data-target="#<?= $this->id ?>" data-slide-to="0" class="active"></li>
    <?php for ($i = 1; $i < count($model->images); $i++): ?>
      <li data-target="#<?= $this->id ?>" data-slide-to="<?= $i ?>"></li>
    <?php endfor; ?>
    </ol>
    <div class="carousel-inner">
    <?php foreach ($model->images as $i => $image): ?>
      <?php $l10n = $image->getL10nModel(Yii::app()->getLanguage()); ?>
      <div class="item<?= $i == 0 ? ' active' : '' ?>">
        <?= CHtml::image($image->image->getUrl($this->getWidth(), $this->getHeight(), true), $l10n->title) ?>
        <?php if (!empty($l10n->title) || !empty($l10n->caption)): ?>
          <div class="carousel-caption">
            <?php if ($l10n->page || $l10n->url): ?>
              <a href="<?= $l10n->page ? $l10n->page->createUrl() : $l10n->url ?>">
            <?php endif; ?>
            <?php if (!empty($l10n->title)): ?>
              <h4><?= $l10n->title ?></h4>
            <?php endif; ?>
            <?php if (!empty($l10n->caption)): ?>
              <p><?= $l10n->caption ?></p>
            <?php endif; ?>
            <?php if ($l10n->page || $l10n->url): ?>
            </a>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
    </div>
    <a class="carousel-control left" href="#carousel-<?= $this->id ?>" data-slide="prev">&lsaquo;</a>
    <a class="carousel-control right" href="#carousel-<?= $this->id ?>" data-slide="next">&rsaquo;</a>
  </div>
<?php endif; ?>
