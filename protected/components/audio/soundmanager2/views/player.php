<div id="<?= $this->getWidgetId() ?>" class="audio-player">
  <a href="#" class="play" data-icon-playing="icon-pause" data-icon-standby="icon-play"><i class="icon-play"></i></a>
  <span class="title"><?= CHtml::encode($this->title ? $this->title : basename($this->url)) ?> <span class="duration"></span></span>
</div>
