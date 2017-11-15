<?= CHtml::textArea(
  $name,
  is_array($value) ? implode(PHP_EOL,$value) : $value,
  array(
    'rows' => 5,
    'class' => 'input-block-level',
  )
) ?>
