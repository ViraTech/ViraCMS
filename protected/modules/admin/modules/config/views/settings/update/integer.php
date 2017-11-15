<?= CHtml::textField(
  $name,
  $value,
  array(
    'class' => 'input-block-level',
    'placeholder' => ' -- ' . Yii::t('admin.labels','Use default value') . ' --',
  )
) ?>
