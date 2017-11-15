<?php

$this->renderPartial('action', array(
  'model' => $model,
  'confirmation' => $this->getActionConfirmMessage('enable', array('model' => $model)),
  'button' => $this->getActionButtonConfig('enable', array('model' => $model)),
));
