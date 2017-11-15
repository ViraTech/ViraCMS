<?= CHtml::dropDownList(
  $name,
  $value ? $value : $defaultValue,
  $values,
  array(
    'class' => 'input-block-level',
  )
) ?>
