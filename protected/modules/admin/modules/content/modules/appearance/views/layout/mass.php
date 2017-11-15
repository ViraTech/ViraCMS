<?php $this->renderPartial('header'); ?>
<form method="post" class="form-horizontal" onsubmit="$('button[type=submit]', this).button('loading'); return true;">
  <fieldset>
    <legend><?= $confirmation ?></legend>
    <input type="hidden" name="action" value="<?= $action ?>" />
    <input type="hidden" name="list" value="<?= $list ?>" />
    <?php
    $this->widget('bootstrap.widgets.TbDetailView', array(
      'data' => $selected,
      'attributes' => $attributes,
    ));
    ?>
    <div class="form-actions">
      <?php $this->widget('bootstrap.widgets.TbButton', $button); ?>
      <a class="btn btn-link" href="<?= $this->createUrl('index'); ?>"><?= Yii::t('common', 'Cancel') ?></a>
    </div>
  </fieldset>
</form>
