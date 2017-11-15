<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm',array(
  'type' => 'vertical',
  'action' => null,
  'htmlOptions' => array(
    'class' => 'modal-form',
    'onsubmit' => 'return false;',
  ),
)); ?>
<input type="hidden" name="update" value="1" />
<fieldset>
  <?php $form->errorSummary($model); ?>
  <?php $this->renderPartial($view,array(
    'form' => $form,
    'model' => $model,
  )); ?>
</fieldset>
<?php $this->endWidget(); ?>
