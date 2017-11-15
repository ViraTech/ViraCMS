<div class="errorHandler">
  <?php if (isset($header)): ?>
    <h2><?= $header ?></h2>
  <?php endif; ?>

  <?php if (isset($title)): ?>
    <h3><?= $title ?></h3>
  <?php endif; ?>

  <?php if (isset($message)): ?>
    <p><?= $message ?></p>
  <?php endif; ?>

  <div>
    <a class="btn btn-primary" href="javascript:history.go(-1)"><i class="icon-chevron-left"></i> <?= Yii::t('common', 'Go Back') ?></a>
    <a class="btn btn-primary" href="<?= Yii::app()->homeUrl ?>"><i class="icon-home"></i> <?= Yii::t('common', 'Home Page') ?></a>
  </div>
</div>
