<li class="span4">
  <div class="img-holder">
    <img data-image-id="<?= $id ?>" src="<?= $img ?>" />
  </div>
  <?php foreach ($languages as $language): ?>
    <?php $l10n = $model !== null ? $model->getL10nModel($language->id, false) : false; ?>
    <?php $this->renderPartial('description', array(
      'l10n' => $l10n,
      'languageID' => $language->id,
    )); ?>
    <?php if ($l10n && $language->id == Yii::app()->getLanguage()) {
      $description = $l10n->title ? $l10n->title : $l10n->caption;
    } ?>
<?php endforeach; ?>
  <span>
    <a href="#image-description" class="descr <?= empty($description) ? ' muted' : '' ?>" data-toggle="modal"><?= empty($description) ? Yii::t('admin.content.messages', 'No caption given yet') : $description ?></a>
  </span>
</li>
