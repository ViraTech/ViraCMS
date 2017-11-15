<?= $form->errorSummary($model) ?>
<?= $form->dropDownListRow($model, 'align', $model->getAligns(), array('class' => 'input-block-level', 'empty' => '')) ?>
