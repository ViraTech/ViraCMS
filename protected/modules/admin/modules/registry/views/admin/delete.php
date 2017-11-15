<?php

$this->renderPartial('action', array(
  'model' => $model,
  'confirmation' => $this->getActionConfirmMessage('delete', array('model' => $model)),
  'button' => $this->getActionButtonConfig('delete', array('model' => $model)),
));
