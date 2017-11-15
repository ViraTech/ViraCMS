<?php
/**
 * ViraCMS Model Event Log Behavior
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VSystemLogBehavior extends CActiveRecordBehavior
{
  const PHP_GET_CLASS = 'get_class($this)';
  const PHP_GET_PK = '$pk = $this->getPrimaryKey() && is_array($pk) ? explode(",", $pk) : $pk';

  /**
   * @var string message for create action
   */
  public $createMessage = 'Model class {class} ID {primaryKey} has been created';

  /**
   * @var array parameters for create action message
   */
  public $createParams = array(
    '{class}' => self::PHP_GET_CLASS,
    '{primaryKey}' => self::PHP_GET_PK,
  );

  /**
   * @var string message for update action
   */
  public $updateMessage = 'Model class {class} ID {id} has been updated';

  /**
   * @var array parameters for update action message
   */
  public $updateParams = array(
    '{class}' => self::PHP_GET_CLASS,
    '{primaryKey}' => self::PHP_GET_PK,
  );

  /**
   * @var string message for delete action
   */
  public $deleteMessage = 'Model class {class} ID {id} has been removed';

  /**
   * @var array parameters for delete action message
   */
  public $deleteParams = array(
    '{class}' => self::PHP_GET_CLASS,
    '{primaryKey}' => self::PHP_GET_PK,
  );

  /**
   * @var string message for enable action
   */
  public $enableMessage = 'Model class {class} ID {id} has been enabled';

  /**
   * @var array parameters for enable action message
   */
  public $enableParams = array(
    '{class}' => self::PHP_GET_CLASS,
    '{primaryKey}' => self::PHP_GET_PK,
  );

  /**
   * @var string message for disable action
   */
  public $disableMessage = 'Model class {class} ID {id} has been disabled';

  /**
   * @var array parameters for disable action message
   */
  public $disableParams = array(
    '{class}' => self::PHP_GET_CLASS,
    '{primaryKey}' => self::PHP_GET_PK,
  );

  /**
   * @var string message for approve action
   */
  public $approveMessage = 'Model class {class} ID {id} has been approved';

  /**
   * @var array parameters for approve action message
   */
  public $approveParams = array(
    '{class}' => self::PHP_GET_CLASS,
    '{primaryKey}' => self::PHP_GET_PK,
  );

  /**
   * @var string message for discard action
   */
  public $discardMessage = 'Model class {class} ID {id} has been discarded';

  /**
   * @var array parameters for discard action message
   */
  public $discardParams = array(
    '{class}' => self::PHP_GET_CLASS,
    '{primaryKey}' => self::PHP_GET_PK,
  );

  /**
   * @var string message translation category
   */
  public $translateCategory = 'events';

  /**
   * After model save event handler
   * @param CEvent $event
   */
  public function afterSave($event)
  {
    switch ($this->owner->getScenario()) {
      case 'create':
      case 'insert':
        $message = $this->createMessage;
        $params = $this->createParams;
        break;

      case 'update':
        $message = $this->updateMessage;
        $params = $this->updateParams;
        break;

      case 'enable':
        $message = $this->enableMessage;
        $params = $this->enableParams;
        break;

      case 'disable':
        $message = $this->disableMessage;
        $params = $this->disableParams;
        break;

      case 'approve':
        $message = $this->approveMessage;
        $params = $this->approveParams;
        break;

      case 'discard':
        $message = $this->discardMessage;
        $params = $this->disableParams;
        break;
    }

    if (isset($message)) {
      $this->logEvent($message, $params);
    }
  }

  /**
   * After model delete event handler
   * @param CEvent $event
   */
  public function afterDelete($event)
  {
    if ($this->getOwner()->getScenario() != 'auto') {
      $this->logEvent($this->deleteMessage, $this->deleteParams);
    }
  }

  /**
   * Log event to system log
   * @param string $message event message
   * @param array $params event parameters
   */
  protected function logEvent($message, $params = array())
  {
    Yii::app()->systemLog->logEvent($message, $this->evaluateEventParams($params), $this->translateCategory);
  }

  /**
   * Evaluate event parameters value
   * @param array $params parameters
   * @return array
   */
  protected function evaluateEventParams($params)
  {
    $eventParams = array();

    if (is_array($params)) {
      foreach ($params as $paramName => $paramExpression) {
        $eventParams[$paramName] = $this->owner->evaluateExpression($paramExpression);
      }
    }

    return $eventParams;
  }
}
