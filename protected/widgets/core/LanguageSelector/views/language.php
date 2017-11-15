<div class="text-<?= $this->align ?>">
  <ul class="nav nav-pills" style="display: inline-block;">
  <?php foreach (VLanguageHelper::getLanguages() as $language): ?>
    <li<?= $language->id == Yii::app()->getLanguage() ? ' class="active"' : '' ?>>
      <a href="/?lang=<?= $language->id ?>"><?= $language->title ?></a>
    </li>
  <?php endforeach; ?>
  </ul>
</div>
