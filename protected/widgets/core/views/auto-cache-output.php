<?php

if ($this->beginCache($this->getCacheKey(), $this->getCacheParams())) {
  $this->widget->run();
  $this->endCache();
}
