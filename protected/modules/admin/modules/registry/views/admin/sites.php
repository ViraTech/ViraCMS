<div class="control-group">
  <label class="control-label"><?= Yii::t('admin.registry.labels', 'Allow Access To Sites') ?></label>
  <div class="controls">
    <?php foreach ($sites as $i => $site): ?>
      <label class="checkbox inline" for="SiteAccessList_<?= $site->id ?>">
        <input name="SiteAccessList[]" id="SiteAccessList_<?= $site->id ?>" value="<?= $site->id ?>" type="checkbox"<?= $model->hasSiteAccess($site->id) ? ' checked' : '' ?>>
        <?= $site->title ?>
      </label>
    <?php endforeach; ?>
  </div>
</div>
