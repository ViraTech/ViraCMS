<?php if ($data->public): ?>
  <i class="icon icon-circle text-success" title="<?= Yii::t('common', 'Published') ?>"></i>
<?php else: ?>
  <i class="icon icon-circle text-error" title="<?= Yii::t('common', 'Hidden') ?>"></i>
<?php endif; ?>
<span class="label label-info pull-right"><?= $data->languageID ?></span>
<span class="text-wrap"><?= $data->title ?></span>
