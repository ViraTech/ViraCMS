<form class="form-horizontal" method="post" action="<?= $this->createUrl('index') ?>" onsubmit="$('button[type=submit]', this).button('loading'); return true;">
  <div class="row-fluid">
    <div class="span4">
      <ul class="nav nav-tabs nav-stacked">
        <?php foreach ($this->settings->getSections(false) as $id => $title): ?>
          <li<?= $id == 'index' ? ' class="active"' : '' ?>><a href="#<?= $id ?>" data-toggle="tab"><?= $title ?></a></li>
        <?php endforeach; ?>
      </ul>
      <h5><?= Yii::t('admin.titles', 'New Configuration') ?></h5>
      <button type="submit" class="btn btn-success" data-loading-text="<?= CHtml::encode('<i class="icon-spinner icon-spin"></i> ' . Yii::t('admin.labels', 'Applying...')) ?>"><i class="icon-ok"></i> <?= Yii::t('admin.labels', 'Apply') ?></i></button>
    </div>
    <div class="span8">
      <div class="tab-content">
      <?php foreach ($this->settings->getSections(false) as $id => $title): ?>
        <div class="tab-pane hide fade<?= $id == 'index' ? ' active in' : '' ?>" id="<?= $id ?>" style="padding: 0; background: transparent;">
          <?php $this->renderPartial($id == 'index' ? 'summary' : 'section', array(
              'model' => $model,
              'section' => $id,
          )); ?>
        </div>
      <?php endforeach; ?>
      </div>
    </div>
  </div>
</form>
