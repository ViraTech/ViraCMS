<?php
/**
 * ViraCMS Item History Model
 *
 * @package vira.core.core
 * @subpackage vira.core.content
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 *
 * @property string $id the primary key
 * @property string $className the item's class name
 * @property string $primaryKey the item's primary key
 * @property string $eventID the event identifier
 * @property integer $timestamp the time stamp
 * @property string $userID the user identifier
 * @property string $ip the user's IP address
 * @property string $agent the user's browser
 *
 * @property VSiteAdmin $user the site administrator model
 * @property string $event the event name
 */
class VHistory extends VActiveRecord
{
  /**
   * @inheritdoc
   */
  public function behaviors()
  {
    return array(
      'GuidBehavior' => array(
        'class' => 'core.behaviors.VGuidBehavior',
        'type' => VGuidBehavior::GUID_STRAIGHT,
      ),
    );
  }

  /**
   * @param string $className
   * @return VHistory
   */
  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }

  /**
   * @inheritdoc
   */
  public function tableName()
  {
    return '{{core_history}}';
  }

  /**
   * @inheritdoc
   */
  public function primaryKey()
  {
    return 'id';
  }

  /**
   * @inheritdoc
   */
  public function attributeLabels()
  {
    return array(
      'userID' => Yii::t('admin.content.labels', 'Administrator'),
      'timestamp' => Yii::t('admin.content.labels', 'Date & Time'),
      'ip' => Yii::t('admin.content.labels', 'IP Address'),
      'agent' => Yii::t('admin.content.labels', 'Browser'),
    );
  }

  /**
   * @inheritdoc
   */
  public function relations()
  {
    return array(
      'user' => array(self::BELONGS_TO, 'VSiteAdmin', 'userID'),
    );
  }

  /**
   * @inheritdoc
   */
  protected function beforeSave()
  {
    if (parent::beforeSave()) {
      if (empty($this->eventID)) {
        return false;
      }

      if ($this->isNewRecord) {
        $this->timestamp = time();

        if (empty($this->userID) && Yii::app()->hasComponent('user')) {
          $this->userID = Yii::app()->user->id;
        }

        if (empty($this->ip) && Yii::app()->hasComponent('request')) {
          $this->ip = Yii::app()->request->userHostAddress;
        }

        if (empty($this->agent) && Yii::app()->hasComponent('request')) {
          $this->agent = Yii::app()->request->userAgent;
        }
      }

      return true;
    }

    return false;
  }

  /**
   * Returns event name
   * @return string
   */
  public function getEvent()
  {
    return Yii::app()->collection->historyEvent->itemAt($this->eventID);
  }
}
