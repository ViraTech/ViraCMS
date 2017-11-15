<?php

$this->renderPartial('action', array(
  'delete' => true,
  'model' => $model,
  'confirmation' => $this->getActionConfirmMessage('delete', array('model' => $model)),
  'button' => $this->getActionButtonConfig('delete', array('model' => $model)),
));
