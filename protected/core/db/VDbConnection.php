<?php
/**
 * ViraCMS Database Connection component
 * Based on Yii Framework CDbConnection Class
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VDbConnection extends CDbConnection
{
  public function init()
  {
    Yii::app()->eventManager->attach($this);
    parent::init();
  }

  protected function initConnection($pdo)
  {
    parent::initConnection($pdo);
    $this->onConnectionInit(new CEvent($this, array(
      'driver' => strtolower($pdo->getAttribute(PDO::ATTR_DRIVER_NAME)),
      'pdo' => $pdo,
    )));
  }

  public function onConnectionInit($event)
  {
    $this->raiseEvent('onConnectionInit', $event);
  }
}
