<?php
/**
 * ViraCMS Site Administrator Model
 *
 * @package vira.core.core
 * @subpackage vira.core.registry
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VSiteAdmin extends VAccountRecord
{
  const DEFAULT_ROLE = 'superadmin';

  /**
   * @var mixed site access list
   */
  protected $_siteAccessList;

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
          'createMessage' => 'Site administrator [{id}] "{name}" has been created',
          'createParams' => array(
            '{id}' => '$this->id',
            '{name}' => '$this->name',
          ),
          'updateMessage' => 'Site administrator [{id}] "{name}" has been updated',
          'updateParams' => array(
            '{id}' => '$this->id',
            '{name}' => '$this->name',
          ),
          'deleteMessage' => 'Site administrator [{id}] "{name}" has been removed',
          'deleteParams' => array(
            '{id}' => '$this->id',
            '{name}' => '$this->name',
          ),
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
   * @return VSiteAdmin
   */
  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }

  public function tableName()
  {
    return '{{core_site_admin}}';
  }

  public function rules()
  {
    return array(
      array('roleID,status,username,email,name', 'required'),
      array('id', 'unsafe', 'except' => 'create'),
      array('id', 'length', 'is' => 36),
      array('languageID', 'length', 'max' => 2),
      array('siteAccess', 'boolean'),
      array('status', 'in', 'range' => array_keys(Yii::app()->collection->accountType->getAccountStatus(VAccountTypeCollection::ADMINISTRATOR))),
      array('email,name,username', 'length', 'max' => 256),
      array('username,email', 'unique',),
      array('email', 'email', 'skipOnError' => true),
      array('salt', 'length', 'max' => self::PASSWORD_SALT_LENGTH),
      array('password', 'length', 'max' => 32),
      array('newPassword', 'required', 'on' => 'create'),
      array('newPassword', 'length', 'min' => Yii::app()->params['passwordLengthMin'], 'max' => Yii::app()->params['passwordLengthMax'], 'encoding' => Yii::app()->charset, 'on' => 'create,passwordUpdate'),
      array('newPassword,newPasswordConfirm', 'required', 'on' => 'create,passwordUpdate'),
      array('newPasswordConfirm', 'compare', 'compareAttribute' => 'newPassword', 'on' => 'create,passwordUpdate'),
      array('newPassword,newPasswordConfirm', 'safe'),
      array('password', 'unsafe'),
      array('id,siteAccess,roleID,email,username,name,status', 'safe', 'on' => 'search'),
    );
  }

  public function relations()
  {
    return array(
      'sites' => array(self::MANY_MANY, 'VSite', '{{core_site_admin_access}}(adminID,siteID)', 'order' => 'sites.default ASC,sites.id DESC'),
      'role' => array(self::BELONGS_TO, 'VAccountRole', 'roleID'),
      'language' => array(self::BELONGS_TO, 'VLanguage', 'languageID'),
    );
  }

  public function getEventHandlersClass()
  {
    return 'SiteAdminEventHandlers';
  }

  public function attributeLabels()
  {
    return array(
      'id' => Yii::t('common', 'ID'),
      'siteAccess' => Yii::t('admin.registry.labels', 'Allow Access To Any Site'),
      'roleID' => Yii::t('admin.registry.labels', 'Account Role'),
      'status' => Yii::t('admin.registry.labels', 'Status'),
      'email' => Yii::t('admin.registry.labels', 'E-Mail'),
      'username' => Yii::t('admin.registry.labels', 'Username'),
      'name' => Yii::t('admin.registry.labels', 'Name'),
      'languageID' => Yii::t('admin.registry.labels', 'Language'),
      'password' => Yii::t('admin.registry.labels', 'Current Password'),
      'newPassword' => Yii::t('admin.registry.labels', 'New Password'),
      'newPasswordConfirm' => Yii::t('admin.registry.labels', 'Repeat Password'),
    );
  }

  protected function beforeValidate()
  {
    if (parent::beforeValidate()) {
      if (empty($this->roleID)) {
        $this->roleID = self::DEFAULT_ROLE;
      }

      return true;
    }

    return false;
  }

  protected function afterSave()
  {
    parent::afterSave();

    $cmd = Yii::app()->db->createCommand();
    $cmd->delete('{{core_site_admin_access}}', 'adminID=:id', array(':id' => $this->id));
    if (!$this->siteAccess) {
      $accessList = array_unique($this->siteAccessList);
      foreach ($accessList as $siteID) {
        $cmd->insert('{{core_site_admin_access}}', array(
          'adminID' => $this->id,
          'siteID' => $siteID,
        ));
      }
    }
  }

  /**
   * Filters queries by administrator role
   * @param string $roleID administrator role identifier
   * @return VSiteAdmin
   */
  public function filterByRole($roleID)
  {
    if (!empty($roleID)) {
      $this->getDbCriteria()->mergeWith(array(
        'condition' => $this->quoteColumn('roleID') . '=:roleID',
        'params' => array(
          ':roleID' => $roleID,
        ),
      ));
    }

    return $this;
  }

  /**
   * Disables User
   * @return boolean result after save model
   */
  public function disable()
  {
    $this->status = VAccountTypeCollection::STATUS_ADMINISTRATOR_DISABLED;
    $result = $this->save();

    if ($result) {
      $this->onDisable(new CEvent($this));
    }

    return $result;
  }

  /**
   * Enables User
   * @return boolean result after save model
   */
  public function enable()
  {
    $this->status = VAccountTypeCollection::STATUS_ADMINISTRATOR_ACTIVE;
    $result = $this->save();

    if ($result) {
      $this->onEnable(new CEvent($this));
    }

    return $result;
  }

  /**
   * Is user active (enabled)
   * @return boolean
   */
  public function getIsActive()
  {
    return $this->status == VAccountTypeCollection::STATUS_ADMINISTRATOR_ACTIVE;
  }

  /**
   * Return site identifiers which admin has access
   * @return array access list
   */
  public function getSiteAccessList()
  {
    if ($this->_siteAccessList === null) {
      $this->_siteAccessList = array();
      $list = Yii::app()->db->
        createCommand()->
        select('siteID')->
        from('{{core_site_admin_access}}')->
        where('adminID=:adminID', array(
          ':adminID' => $this->id,
        ))->
        queryAll();

      if (is_array($list)) {
        foreach ($list as $row) {
          $this->_siteAccessList[] = $row['siteID'];
        }
      }
    }

    return $this->_siteAccessList;
  }

  /**
   * Update site access list
   * @param mixed $value new access list
   */
  public function setSiteAccessList($value)
  {
    $this->_siteAccessList = is_array($value) ? array_filter($value) : array();
  }

  /**
   * Determine is admin has site access or not
   * @param integer $siteID site identifier
   * @return boolean
   */
  public function hasSiteAccess($siteID)
  {
    return in_array($siteID, $this->getSiteAccessList());
  }

  /**
   * Returns admin' username
   * @return string
   */
  public function getUsername()
  {
    return $this->username;
  }

  /**
   * Performs search over records
   * @return CActiveDataProvider
   */
  public function search()
  {
    $criteria = new CDbCriteria;

    if (Yii::app()->user->getAttribute('siteAccess') == 0) {
      $criteria->join = 'LEFT JOIN {{core_site_admin_access}} a ON a.adminID=t.id';
      $criteria->addInCondition('a.siteID', Yii::app()->user->getModel()->getSiteAccessList());
      if ($this->siteAccess) {
        $criteria->condition .= ' AND a.siteID=:siteAccessID';
        $criteria->params[':siteAccessID'] = $this->siteAccess;
      }
    }
    elseif ($this->siteAccess) {
      $criteria->join = 'LEFT JOIN {{core_site_admin_access}} a ON a.adminID=t.id';
      $criteria->condition = 't.siteAccess>0 OR a.siteID=:siteAccessID';
      $criteria->params[':siteAccessID'] = $this->siteAccess;
    }

    $criteria->compare('t.id', $this->id, true);
    $criteria->compare('t.roleID', $this->roleID);
    $criteria->compare('t.email', $this->email, true);
    $criteria->compare('t.status', $this->status);
    $criteria->compare('t.name', $this->name, true);
    $criteria->with = array(
      'sites',
      'role',
    );

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'pagination' => array(
        'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params['defaultPageSize']),
        'pageVar' => 'page',
      ),
    ));
  }
}
