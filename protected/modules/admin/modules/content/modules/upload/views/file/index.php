<?php $this->renderPartial('header'); ?>
<?php $this->widget('bootstrap.widgets.TbAlert'); ?>
<p>
  <a href="<?= $this->createUrl('create') ?>" class="btn btn-success"><i class="icon-upload-alt"></i> <?= Yii::t('admin.content.labels', 'Upload File') ?></a>
</p>
<?php $this->renderPartial('grid', array('model' => $model)); ?>
<form method="post" id="mass" class="mass-actions" onsubmit="return processSelected()">
  <?= Yii::t('common', 'Mass actions with selected:') ?>
  <div class="btn-group">
    <button name="delete" class="btn btn-danger" type="submit"><i class="icon-trash"></i> <?= Yii::t('common', 'Delete') ?></button>
  </div>
</form>
<?php $this->cs->registerScript(get_class($model) . '_AfterAjaxUpdate', "function afterAjaxUpdate(){}", CClientScript::POS_HEAD); ?>
<?php $this->cs->registerScript(get_class($model) . '_ProcessSelected', "function processSelected() { $('input[name^=" . $model->getGridID() . "_c0]:checked').clone().hide().prependTo('#mass'); return true; }", CClientScript::POS_HEAD); ?>
