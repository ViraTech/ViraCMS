<div id="<?= $this->id ?>">
  <?= CHtml::openTag('div', $this->htmlOptions) ?>
    <a class="btn btn-small dropdown-toggle input-block-level" data-toggle="dropdown" href="#">
      <?= Yii::t('common', 'Page Size: {n}', array($this->value)) ?> <span class="caret"></span>
    </a>
    <ul class="dropdown-menu">
    <?php foreach ($this->sizes as $i): ?>
      <li><a href="?<?= $this->pageVar ?>=<?= $i ?>" data-size="<?= $i ?>"><?= Yii::t('common', 'Page Size: {n}', array($i)) ?></a></li>
    <?php endforeach; ?>
    </ul>
  </div>
</div>
