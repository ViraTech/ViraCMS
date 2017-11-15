<?php $this->renderPartial('header'); ?>
<?php if ($tmpFile): ?>
  <h4><?= Yii::t('admin.translate.titles', 'File Has Been Generated') ?></h4>
  <p><?= Yii::t('admin.translate.messages', 'Your download will start shortly. If nothing happens, please follow this link: {url}.', array(
    '{url}' => CHtml::link(basename($tmpFile), $this->createUrl('download', array('file' => $tmpFile))),
  )) ?></p>
<?php else: ?>
  <h4><?= Yii::t('admin.translate.titles', 'No Messages Found') ?></h4>
  <p><?= Yii::t('admin.translate.messages', 'No source messages found in the database. Nothing to generate, sorry.') ?></p>
<?php endif; ?>
<p>
  <a class="btn" href="<?= $this->createUrl('index') ?>"><?= Yii::t('common', 'OK') ?></a>
</p>
