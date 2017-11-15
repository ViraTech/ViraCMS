<?php if (count($languages) > 1): ?>
  <ul class="nav nav-tabs">
  <?php foreach ($languages as $language): ?>
    <li<?= $language->id == $currentLanguageID ? ' class="active"' : '' ?>><a href="#seo-<?= $language->id ?>" data-toggle="tab"><?= $language->title ?></a></li>
  <?php endforeach; ?>
  </ul>
<?php endif; ?>
<?php if (count($languages) > 1): ?>
  <div class="tab-content">
  <?php foreach ($languages as $language): ?>
    <div class="tab-pane fade<?= $language->id == $currentLanguageID ? ' active in' : '' ?>" id="seo-<?= $language->id ?>">
      <?php $this->render('seo-form', array(
        'form' => $form,
        'model' => $model->getSeoModel($language->id),
      )); ?>
    </div>
  <?php endforeach; ?>
  </div>
<?php else: ?>
  <?php $this->render('seo-form', array(
    'form' => $form,
    'model' => $model->getSeoModel($currentLanguageID),
  )); ?>
<?php endif; ?>
