<?php
/**
 * ViraCMS Search Index Component Data Models Behavior
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VSearchIndexerBehavior extends CActiveRecordBehavior
{
  /**
   * @var string the module name
   */
  public $module;

  /**
   * @var string the key attribute name
   */
  public $keyAttribute = 'id';

  /**
   * Add search content to search index storage
   * @param CEvent $event save event
   */
  public function afterSave($event)
  {
    if ($this->module && Yii::app()->hasComponent('searchIndex')) {
      Yii::app()->searchIndex->add($this->module, $this->owner[$this->keyAttribute]);
    }
  }

  /**
   * Delete search content from search index storage
   * @param CEvent $event delete event
   */
  public function afterDelete($event)
  {
    if ($this->module && Yii::app()->hasComponent('searchIndex')) {
      Yii::app()->searchIndex->remove($this->module, $this->owner[$this->keyAttribute]);
    }
  }
}
