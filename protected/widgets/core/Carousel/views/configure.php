<?= $form->errorSummary($model) ?>
<?= $form->checkboxRow($model, 'showLatestCarousel', array('onclick' => '$("#' . CHtml::activeId($model, 'carouselID') . '").val("")')) ?>
<?= $form->dropDownListRow($model, 'carouselID', CHtml::listData(VCarousel::model()->published()->with(array('currentL10n'))->findAll(), 'id', 'title'), array('class' => 'input-block-level', 'empty' => '', 'onchange' => '$("#' . CHtml::activeId($model, 'showLatestCarousel') . '").prop("checked",!$(this).val())')) ?>
<div class="row-fluid">
  <div class="span6">
    <?= $form->textFieldRow($model, 'width', array('class' => 'input-block-level')) ?>
  </div>
  <div class="span6">
    <?= $form->textFieldRow($model, 'height', array('class' => 'input-block-level')) ?>
  </div>
</div>
<?= $form->checkboxRow($model, 'cacheEnabled') ?>
