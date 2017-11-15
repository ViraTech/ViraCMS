<?= $form->errorSummary($model) ?>
<?= $form->dropDownListRow($model, 'menuID', CHtml::listData(VCustomMenu::model()->autoFilter(true)->findAll(), 'id', 'title'), array('class' => 'input-block-level', 'empty' => '')) ?>
<?= $form->dropDownListRow($model, 'menuType', $model->getMenuTypes(), array('class' => 'input-block-level')) ?>
