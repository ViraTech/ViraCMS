<?= $form->errorSummary($model) ?>
<?= $form->dropDownListRow($model, 'photoID', CHtml::listData(VPhoto::model()->published()->findAll(), 'id', 'title'), array('class' => 'input-block-level', 'empty' => '')) ?>
<div class="row-fluid">
  <div class="span6">
    <?= $form->textFieldRow($model, 'imageWidth', array('class' => 'input-block-level', 'placeholder' => VPhotoWidget::DEFAULT_WIDTH)) ?>
  </div>
  <div class="span6">
    <?= $form->textFieldRow($model, 'imageHeight', array('class' => 'input-block-level', 'placeholder' => VPhotoWidget::DEFAULT_HEIGHT)) ?>
  </div>
</div>
<div class="row-fluid">
  <div class="span6">
    <?= $form->dropDownListRow($model, 'rows', $model->getRows(), array('class' => 'input-block-level', 'empty' => Yii::t('common', 'None'))) ?>
  </div>
  <div class="span6">
    <?= $form->textFieldRow($model, 'limit', array('class' => 'input-small')) ?>
  </div>
</div>
