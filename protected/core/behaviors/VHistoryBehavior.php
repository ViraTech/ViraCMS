<?php
/**
 * ViraCMS Model History Behavior
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VHistoryBehavior extends CActiveRecordBehavior
{
  /**
   * Create/update history model
   * @param CEvent $event the event
   */
  public function beforeSave($event)
  {
    $sender = $event->sender;

    if ($sender->isNewRecord) {
      $eventName = VHistoryEventCollection::CREATED;
    }
    else {
      $eventName = VHistoryEventCollection::UPDATED;
    }

    if ($sender->scenario == 'enable') {
      $eventName = VHistoryEventCollection::PUBLISHED;
    }

    if ($sender->scenario == 'disable') {
      $eventName = VHistoryEventCollection::HIDDEN;
    }

    $this->getHistoryModel($sender, $eventName)->save(false);
  }

  /**
   * Clean up all of history models
   * @param CEvent $event the event
   */
  public function afterDelete($event)
  {
    $sender = $event->sender;

    foreach ($this->findHistoryModels($sender) as $model) {
      $model->delete();
    }
  }

  /**
   * Finds and returns history model for current owner and specified event
   * @param string $eventName the event name
   * @return GspHistory
   */
  public function findHistory($eventName)
  {
    return $this->getHistoryModel($this->owner, $eventName);
  }

  /**
   * Returns existing or new history model for specified model and event
   * @param mixed $owner the owner's model
   * @param string $eventName the event name
   * @return VHistory
   */
  protected function getHistoryModel($owner, $eventName)
  {
    if (($model = $this->findHistoryModel($owner, $eventName)) == null) {
      $model = new VHistory();
      $model->setAttributes($this->getHistoryModelAttributes($owner, $eventName), false);
    }

    return $model;
  }

  /**
   * Finds and returns all of history models for specified owner
   * @param mixed $owner the owner's model
   * @return VHistory[]
   */
  protected function findHistoryModels($owner)
  {
    return VHistory::model()->findAllByAttributes($this->getHistoryModelAttributes($owner));
  }

  /**
   * Finds and returns existing history model for specified model and event
   * @param mixed $owner the owner's model
   * @param string $event the event name
   * @return VHistory
   */
  protected function findHistoryModel($owner, $event)
  {
    return VHistory::model()->findByAttributes($this->getHistoryModelAttributes($owner, $event));
  }

  /**
   * Returns history model attributes based on owner and event
   * @param mixed $owner the owner's model
   * @param string $eventName the event name (optional)
   * @return VHistory
   */
  protected function getHistoryModelAttributes($owner, $eventName = null)
  {
    $attributes = array(
      'className' => get_class($owner),
      'primaryKey' => $owner->getPrimaryKey(),
    );

    if ($eventName) {
      $attributes['eventID'] = $eventName;
    }

    if (is_array($attributes['primaryKey'])) {
      $attributes['primaryKey'] = implode(',', $attributes['primaryKey']);
    }

    return $attributes;
  }
}
