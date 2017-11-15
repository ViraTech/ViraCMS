<?= $form->errorSummary($model) ?>
<?= $form->dropDownListRow($model, 'menuID', CHtml::listData(VCustomMenu::model()->autoFilter(true)->findAll(), 'id', 'title'), array('class' => 'input-block-level', 'empty' => '')) ?>
<?= $form->dropDownListRow($model, 'position', $model->getPositionOptions(), array('class' => 'input-block-level')) ?>
<?= $form->dropDownListRow($model, 'fixed', $model->getFixedOptions(), array('class' => 'input-block-level')) ?>
<?= $form->dropDownListRow($model, 'container', $model->getContainerOptions(), array('class' => 'input-block-level', 'empty' => '')) ?>
<?= $form->checkboxRow($model, 'brand') ?>
<?= $form->textFieldRow($model, 'brandName', array('class' => 'input-block-level')) ?>
<?= $form->textFieldRow($model, 'brandImageUrl', array('class' => 'input-block-level')) ?>
