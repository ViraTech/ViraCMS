<?= $form->errorSummary($model) ?>
<?= $form->checkBoxRow($model, 'showPageTitle') ?>
<?= $form->dropDownListRow($model, 'pageTitlePosition', $model->getTitlePositions(), array('class' => 'input-block-level', 'empty' => '')) ?>
<?= $form->textFieldRow($model, 'pageTitleTag') ?>
<?= $form->textFieldRow($model, 'pageTitleClass', array('class' => 'input-block-level')) ?>
