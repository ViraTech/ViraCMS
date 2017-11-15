<form class="form-search" method="get" action="<?= Yii::app()->createUrl('/search/index') ?>">
  <div class="input-append">
    <input class="search-query input-<?= $this->size ?>" type="text" name="q" value="<?= Yii::app()->request->getParam('q', '') ?>" placeholder="<?= Yii::t('common', 'Search') ?>">
    <button type="submit" class="btn"><s class="icon-search"></s></button>
  </div>
</form>
