<?php
/**
 * ViraCMS Model GUID Behavior
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VGuidBehavior extends CActiveRecordBehavior
{
  const GUID_RANDOM = 1;
  const GUID_STRAIGHT = 2;

  public $attribute = 'id';
  public $type = self::GUID_RANDOM;

  public function afterConstruct($event)
  {
    $event->sender->setAttribute($this->attribute, $this->getGuid());
  }

  /**
   * Generate and return GUIDv4
   * @return string
   */
  public function getGuid()
  {
    switch ($this->type) {
      case self::GUID_STRAIGHT:
        return Yii::app()->guid->straight();

      default:
        return Yii::app()->guid->random();
    }
  }
}
