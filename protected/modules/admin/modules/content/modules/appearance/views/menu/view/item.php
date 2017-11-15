<li>
  <span><?= $title ? $title : Yii::t('admin.content.labels', 'No Title Given') ?></span><br><small class="muted"><?= $url ?></small>
  <!-- subitems -->
  <?php if (isset($items) && is_array($items)) $this->renderPartial('view/items', array('items' => $items)); ?>
</li>
