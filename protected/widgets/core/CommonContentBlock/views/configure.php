<?= $form->errorSummary($model) ?>
<?= $form->dropDownListRow($model, 'contentID', CHtml::listData(VContentCommon::model()->orderByName()->findAll(), 'id', 'title'), array('class' => 'input-block-level', 'empty' => '')) ?>
