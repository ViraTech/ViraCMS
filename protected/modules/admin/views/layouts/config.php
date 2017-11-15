<?php $this->beginContent(); ?>
<div class="container">
  <header>
    <div class="row-alert">
      <?php $this->widget('bootstrap.widgets.TbAlert'); ?>
    </div>
    <h2><?= Yii::t('admin.titles', 'Application Settings') ?></h2>
  </header>
  <?= $content ?>
</div>
<?php $this->endContent(); ?>
