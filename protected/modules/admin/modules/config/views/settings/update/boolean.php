<?= CHtml::dropDownList(
  $name,
  $value ? '1' : '0',
  array(
    '1' => Yii::t('common','True'),
    '0' => Yii::t('common','False'),
  ),
  array(
    'class' => 'input-block-level',
  )
) ?>
