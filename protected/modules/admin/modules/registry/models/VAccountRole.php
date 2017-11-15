<?php
/**
 * ViraCMS Account Role Model
 *
 * @package vira.core.core
 * @subpackage vira.core.registry
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 *
 * @property string $id role ID
 * @property string $title site title
 * @property boolean $system system role (uneditable)
 * @property boolean $allowAll disable access control (allow all actions)
 */
class VAccountRole extends VActiveRecord
{
  const DEFAULT_ROLE = 'superadmin';

  private $_accessFlags;

  /**
   * Initialize model
   */
  public function init()
  {
    parent::init();

    // attach administrative CRUD behaviours only when created inside the system CRUD controller
    if (is_a(Yii::app()->getController(), 'VSystemController')) {
      $this->attachBehaviors(array(
        'SystemLogBehavior' => array(
          'class' => 'VSystemLogBehavior',
          'createMessage' => 'Account role [{id}] "{title}" has been created',
          'createParams' => array('{id}' => '$this->id', '{title}' => '$this->title'),
          'updateMessage' => 'Account role [{id}] "{title}" has been updated',
          'updateParams' => array('{id}' => '$this->id', '{title}' => '$this->title'),
          'deleteMessage' => 'Account role [{id}] "{title}" has been removed',
          'deleteParams' => array('{id}' => '$this->id', '{title}' => '$this->title'),
          'translateCategory' => 'admin.registry.events',
        ),
        'HistoryBehavior' => array(
          'class' => 'VHistoryBehavior',
        ),
      ));
    }
  }

  /**
   * @param string $className
   * @return VAccountRole
   */
  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }

  public function tableName()
  {
    return '{{core_account_role}}';
  }

  public function relations()
  {
    return array(
      'access' => array(self::HAS_MANY, 'VAccountAccess', 'accountRoleID', 'condition' => $this->quoteColumn('access.permit') . '>0'),
    );
  }

  public function rules()
  {
    return array(
      array('id,title', 'required'),
      array('id', 'length', 'min' => 3, 'max' => 16),
      array('id', 'match', 'pattern' => '/^[a-z]+$/', 'message' => Yii::t('common', 'Only lowercased latin characters allowed.')),
      array('id', 'unique'),
      array('title', 'length', 'max' => 255),
      array('id,title', 'safe', 'on' => 'search'),
    );
  }

  public function attributeLabels()
  {
    return array(
      'id' => Yii::t('admin.registry.labels', 'Identifier'),
      'title' => Yii::t('admin.registry.labels', 'Title'),
      'system' => Yii::t('admin.registry.labels', 'System Role'),
      'allowAll' => Yii::t('admin.registry.labels', 'Permit Everything'),
    );
  }

  protected function afterSave()
  {
    Yii::app()->cache->deleteTag('Vira.Role');
    parent::afterSave();
  }

  protected function beforeDelete()
  {
    if (parent::beforeDelete()) {
      if ($this->system) {
        $this->addError('id', Yii::t('admin.registry.errors', 'You can not delete system role.'));
        return false;
      }

      if (self::model()->count() < 2) {
        $this->addError('id', Yii::t('admin.registry.errors', 'You can not delete the last role.'));
        return false;
      }

      return true;
    }

    return false;
  }

  public function getAccessFlags()
  {
    if ($this->_accessFlags == null) {
      $this->_accessFlags = CHtml::listData(VAccountAccess::model()->findAllByAttributes(array('accountRoleID' => $this->id)), 'accessRuleID', 'permit');
    }

    return $this->_accessFlags;
  }

  /**
   * Performs search over records
   * @return CActiveDataProvider
   */
  public function search()
  {
    $criteria = new CDbCriteria;
    $criteria->compare('t.id', $this->id);
    $criteria->compare('t.title', $this->title, true);

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'pagination' => array(
        'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params['defaultPageSize']),
        'pageVar' => 'page',
      ),
    ));
  }
}
