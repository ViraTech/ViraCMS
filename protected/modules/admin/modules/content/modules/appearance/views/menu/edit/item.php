<li data-entry-id="<?= CHtml::encode($id) ?>" data-entry-url="<?= CHtml::encode($url) ?>" data-page-id="<?= $pageID ?>" data-entry-titles="<?= CHtml::encode(is_array($titles) ? CJSON::encode($titles) : $titles) ?>" data-anchor="<?= $anchor ?>" data-target="<?= $target ?>">
  <div class="btn-group">
    <a class="btn" href="#"><span><?= $title ? $title : Yii::t('admin.content.labels', 'No Title Given') ?></span> <small class="muted"><?= $url ?><?= $anchor ? '#' . $anchor : '' ?></small></a>
    <a class="btn btn-primary btn-control control-edit" title="<?= Yii::t('admin.content.labels', 'Edit Menu Item') ?>" href="#"><i class="icon-pencil"></i></a>
    <a class="btn btn-success btn-control control-add-page" title="<?= Yii::t('admin.content.labels', 'Add Child Item') ?>" href="#"><i class="icon-plus"></i></a>
    <a class="btn btn-danger btn-control control-delete-page" title="<?= Yii::t('admin.content.labels', 'Delete Item') ?>" href="#"><i class="icon-trash"></i></a>
  </div>
  <?php if (isset($items) && is_array($items)) $this->renderPartial('edit/items', array('items' => $items)); ?>
  <!-- subitems -->
</li>
