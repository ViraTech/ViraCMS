<fieldset>
  <div class="row-fluid">
    <div class="span4">
      <?= $form->uneditableRow($model, 'timeUpdated', array('class' => 'input-block-level', 'value' => $model->timeUpdated ? Yii::app()->format->formatDatetime($model->timeUpdated) : '')) ?>
    </div>
    <div class="span8">
      <?= $form->uneditableRow($model, 'updatedBy', array('class' => 'input-block-level', 'value' => $model->whoUpdated ? $model->whoUpdated->name : '')) ?>
    </div>
  </div>
</fieldset>
