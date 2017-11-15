<div class="errorHandler">
  <h2><?= $header ?></h2>
  <h3><?= $code ?></h3>
  <h5><?= $message ?></h5>
  <a class="btn btn-primary" href="<?= Yii::app()->request->urlReferrer ? Yii::app()->request->urlReferrer : $this->createUrl('/admin/default/index') ?>"><i class="icon-chevron-left"></i> <?= Yii::t('common', 'Go Back') ?></a>
  <a class="btn btn-inverse" href="<?= $this->createUrl('/admin/default/index') ?>"><i class="icon-dashboard"></i> <?= Yii::t('admin.labels', 'Dashboard') ?></a>
</div>
