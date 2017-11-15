<?php
/**
 * ViraCMS Core Event Log Model
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 *
 * @property string $id primary key
 * @property integer $siteID site identifier
 * @property string $className class name
 * @property string $primaryKey primary key
 * @property string $source event source (module/controller/action)
 * @property string $event multilanguage text representation of event
 * @property string $params message params for translation
 * @property string $translate translation category
 * @property string $authorType type of event author
 * @property string $authorID author account ID
 * @property integer $remote remote IP address
 * @property integer $time event occure timestamp
 */
class VLogEvent extends VActiveRecord
{
  /**
   * @param string $className
   * @return VLogEvent
   */
  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }

  public function tableName()
  {
    return '{{core_log_event}}';
  }

  public function rules()
  {
    return array(
      array('id,siteID,className,primaryKey,source,event,authorType,authorID,remote,time', 'safe', 'on' => 'search')
    );
  }

  public function relations()
  {
    return array(
      'site' => array(self::BELONGS_TO, 'VSite', 'siteID'),
      'author' . VAccountTypeCollection::ADMINISTRATOR => array(self::BELONGS_TO, 'VSiteAdmin', 'authorID'),
    );
  }

  public function behaviors()
  {
    return CMap::mergeArray(
        array(
        'GuidBehavior' => array(
          'class' => 'VGuidBehavior',
        ),
        ), parent::behaviors()
    );
  }

  public function attributeLabels()
  {
    return array(
      'id' => Yii::t('common', 'ID'),
      'siteID' => Yii::t('admin.registry.labels', 'Site'),
      'className' => Yii::t('admin.registry.labels', 'Class Name'),
      'primaryKey' => Yii::t('admin.registry.labels', 'Primary Key'),
      'source' => Yii::t('admin.registry.labels', 'Source'),
      'event' => Yii::t('admin.registry.labels', 'Event'),
      'params' => Yii::t('admin.registry.labels', 'Parameters'),
      'translate' => Yii::t('admin.registry.labels', 'Translate Category'),
      'authorType' => Yii::t('admin.registry.labels', 'Author Type'),
      'authorID' => Yii::t('admin.registry.labels', 'Author'),
      'remote' => Yii::t('admin.registry.labels', 'Remote IP Address'),
      'time' => Yii::t('admin.registry.labels', 'Event Date&Time'),
    );
  }

  protected function afterFind()
  {
    parent::afterFind();
    $this->params = @unserialize($this->params);
    if (!is_array($this->params)) {
      $this->params = array();
    }
  }

  protected function beforeValidate()
  {
    if (parent::beforeValidate()) {

      if (is_array($this->primaryKey)) {
        $this->primaryKey = implode(',', $this->primaryKey);
      }

      return true;
    }

    return false;
  }

  protected function beforeSave()
  {
    if (parent::beforeSave()) {
      if ($this->isNewRecord) {
        $controller = Yii::app()->getController();
        $this->siteID = Yii::app()->site->id;
        $this->source = $controller ? $controller->route : 'console';
        if (Yii::app()->hasComponent('user')) {
          $this->authorType = Yii::app()->user->type;
          $this->authorID = Yii::app()->user->id;
        }
        if (Yii::app()->hasComponent('request')) {
          $this->remote = ip2long(Yii::app()->request->userHostAddress);
        }
      }

      $this->time = time();

      return true;
    }

    return false;
  }

  public function search()
  {
    $criteria = new CDbCriteria;
    if (!empty($this->authorID)) {
      $criteria->join = 'LEFT JOIN {{core_site_admin}} a ON a.id = t.authorID';
      $criteria->compare('a.id', $this->authorID, true);
      $criteria->compare('a.name', $this->authorID, true, 'OR');
      $criteria->compare('a.email', $this->authorID, true, 'OR');
    }
    $criteria->compare('t.id', $this->id, true);
    $criteria->compare('t.className', $this->className, true);
    $criteria->compare('t.primaryKey', $this->primaryKey, true);
    $criteria->compare('t.authorType', $this->authorType);
    if (!empty($this->remote)) {
      $criteria->compare('t.remote', ip2long($this->remote));
    }
    $criteria->compare('t.source', $this->source, true);
    $criteria->with = array(
      'author' . VAccountTypeCollection::ADMINISTRATOR,
    );

    $this->addSiteCondition('siteID', $criteria);

    $this->addTimeRangeCondition('time', $criteria);

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'pagination' => array(
        'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params['defaultPageSize']),
        'pageVar' => 'page',
      ),
      'sort' => array(
        'defaultOrder' => 't.time DESC',
      ),
    ));
  }
}
