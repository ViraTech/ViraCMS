<?php
/**
 * ViraCMS Item History Widget
 *
 * @package vira.core.core
 * @subpackage vira.core.widgets
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VHistoryWidget extends CWidget
{
  public $model;
  public $form;

  public function run()
  {
    if (empty($this->model) || empty($this->form)) {
      return;
    }

    $this->render('history', array(
      'events' => $this->getEvents(),
      'form' => $this->form,
    ));
  }

  public function getEvents()
  {
    $events = array();

    foreach (Yii::app()->collection->historyEvent->toArray() as $eventID => $eventName) {
      $history = $this->model->findHistory($eventID);

      if (empty($history) || $history->isNewRecord) {
        continue;
      }

      $events[] = array(
        'id' => $eventID,
        'name' => $eventName,
        'model' => $history,
      );
    }

    return $events;
  }
}
