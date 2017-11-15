<?= call_user_func_array(
  array(
    $form,
    $this->uneditable ? 'uneditableRow' : 'textFieldRow',
  ),
  array(
    $model,
    'title',
    array_filter(array(
      'class' => 'input-block-level',
      'name' => $this->mode == VSeoWidget::MODE_MULTI_LANGUAGE ? get_class($model) . '[' . $model->languageID . '][title]' : null,
    ))
  )
) ?>
<?= call_user_func_array(
  array(
    $form,
    $this->uneditable ? 'uneditableRow' : 'textFieldRow',
  ),
  array(
    $model,
    'keywords',
    array_filter(array(
      'class' => 'input-block-level',
      'name' => $this->mode == VSeoWidget::MODE_MULTI_LANGUAGE ? get_class($model) . '[' . $model->languageID . '][keywords]' : null,
    ))
  )
) ?>
<?= call_user_func_array(
  array(
    $form,
    $this->uneditable ? 'uneditableRow' : 'textAreaRow',
  ),
  array(
    $model,
    'description',
    array_filter(array(
      'class' => 'input-block-level' . ($this->uneditable ? ' uneditable-textarea' : ''),
      'rows' => '4',
      'name' => $this->mode == VSeoWidget::MODE_MULTI_LANGUAGE ? get_class($model) . '[' . $model->languageID . '][description]' : null,
    ))
  )
) ?>
