<?php

$this->renderPartial('action', array(
  'model' => $model,
  'confirmation' => $this->getActionConfirmMessage('disable', array('model' => $model)),
  'button' => $this->getActionButtonConfig('disable', array('model' => $model)),
));
