<?php
/**
 * ViraCMS Core Authentication Log Model
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
 * @property integer $type action type (login, logout or password restore)
 * @property integer $result action result (successful or not)
 * @property string $authorType type of event author
 * @property string $authorID author account ID
 * @property integer $remote remote IP address
 * @property integer $time event occure timestamp
 */
class VLogAuth extends VActiveRecord
{
  /**
   * @param string $className
   * @return VLogAuth
   */
  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }

  public function tableName()
  {
    return '{{core_log_auth}}';
  }

  public function rules()
  {
    return array(
      array('id,siteID,type,result,authorType,authorID,remote,time', 'safe', 'on' => 'search')
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
      'type' => Yii::t('admin.registry.labels', 'Action Type'),
      'result' => Yii::t('admin.registry.labels', 'Action Result'),
      'authorType' => Yii::t('admin.registry.labels', 'Author Type'),
      'authorID' => Yii::t('admin.registry.labels', 'Author'),
      'remote' => Yii::t('admin.registry.labels', 'Remote IP Address'),
      'time' => Yii::t('admin.registry.labels', 'Event Date&Time'),
    );
  }

  protected function beforeSave()
  {
    if (parent::beforeSave()) {
      if ($this->isNewRecord) {
        $this->siteID = Yii::app()->site->id;

        if (Yii::app()->hasComponent('user')) {
          if (empty($this->authorType)) {
            $this->authorType = Yii::app()->user->type;
          }
          if (empty($this->authorID)) {
            $this->authorID = Yii::app()->user->id;
          }
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
    $criteria->compare('t.type', $this->type);
    $criteria->compare('t.result', $this->result);
    $criteria->compare('t.authorType', $this->authorType);
    if (!empty($this->remote)) {
      $criteria->compare('t.remote', ip2long($this->remote));
    }
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

  public function log($accountID)
  {
    $criteria = new CDbCriteria();
    $criteria->with = array(
      'site',
    );
    $criteria->compare('t.authorType', VAccountTypeCollection::ADMINISTRATOR);
    $criteria->compare('t.authorID', $accountID);

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
