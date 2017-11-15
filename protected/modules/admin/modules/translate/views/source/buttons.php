<div class="btn-group">
  <?php foreach ($languages as $languageID => $language) {
    $found = false;
    $empty = false;
    $params = $model->primaryKey;
    $params['lid'] = $languageID;
    $params['return'] = $returnUrl;
    if ($model->translations) {
      foreach ($model->translations as $translation) {
        if ($translation->languageID == $languageID) {
          $found = true;
          $empty = empty($translation->translate);
          $params = array(
            'id' => $translation->id,
            'return' => $returnUrl,
          );
          break;
        }
      }
    }
    echo CHtml::link(
      $language, $this->createUrl($found ? $update : $create, $params), array(
      'class' => 'btn btn-mini btn-' . ($found && !$empty ? 'success' : 'danger'),
      )
    );
  } ?>
</div>
