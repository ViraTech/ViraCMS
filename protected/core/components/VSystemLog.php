<?php
/**
 * ViraCMS System Log Component
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VSystemLog extends VApplicationComponent
{
  /**
   * Add record to events log
   * @param string $message event message (in source language)
   * @param array $params message params (in appropriated for @see Yii::t style)
   * @param string $category message category
   * @return boolean
   */
  public function logEvent($message, $params = array(), $category = 'coreEvents')
  {
    $record = new VLogEvent;

    $record->setAttributes(array(
      'event' => $message,
      'params' => serialize($params),
      'translate' => $category,
      ), false);

    return $record->save(false);
  }

  /**
   * Add record to security log
   * @param integer $type event type @see VAuthLogTypeCollection
   * @param integer $result
   * @param integer $authorType author type @see VAccountTypeCollection
   * @param string $authorID account identifier
   * @return boolean
   */
  public function logAuth($type, $result, $authorType = null, $authorID = null)
  {
    $record = new VLogAuth;

    $record->setAttributes(array(
      'type' => $type,
      'result' => $result,
      'authorType' => $authorType,
      'authorID' => $authorID,
      ), false);

    return $record->save(false);
  }
}
