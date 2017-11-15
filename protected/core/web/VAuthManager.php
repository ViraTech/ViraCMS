<?php
/**
 * ViraCMS Authentication Manager Rules
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VAuthManager extends CPhpAuthManager
{
  const ROLE_SUPERADMIN = 'superadmin';

  /**
   * @var array access sections
   */
  private $_accessSections = array();

  /**
   * @var array access groups
   */
  private $_accessGroups = array();

  /**
   * @var array access rules
   */
  private $_accessRules = array();

  /**
   * @var array administrator' roles cache
   */
  private $_adminRoles;

  /**
   * @var array action roles cache
   */
  private $_actionRoles;

  /**
   * @var array access rules cache
   */
  private $_adminAccessRules = array();

  /**
   * Initializes the application component
   */
  public function init()
  {
    $this->load();

    switch (Yii::app()->user->getType()) {

      /**
       * User is a guest - do nothing
       */
      case VAccountTypeCollection::GUEST:
        break;

      /**
       * User is site user - set user role
       */
      case VAccountTypeCollection::USER:
        $this->assign('user', Yii::app()->user->id);
        break;

      /**
       * User is site administrator - set one of admin's roles
       */
      case VAccountTypeCollection::ADMINISTRATOR:
        $role = Yii::app()->user->getAttribute('roleID');
        $this->assign($role, Yii::app()->user->id);
        foreach ($this->getAdminRoleRules($role) as $rule) {
          $this->assign($rule, Yii::app()->user->id);
        }
        break;
    }
  }

  /**
   * Set access sections
   * @param array $values the access sections
   */
  public function setAccessSections($values)
  {
    $this->_accessSections = $values;
  }

  /**
   * Returns access sections
   * @return array
   */
  public function getAccessSections()
  {
    return $this->_accessSections;
  }

  /**
   * Set access groups
   * @param array $values the access groups
   */
  public function setAccessGroups($values)
  {
    $this->_accessGroups = $values;
  }

  /**
   * Returns access groups
   * @return array
   */
  public function getAccessGroups()
  {
    return $this->_accessGroups;
  }

  /**
   * Set access rules
   * @param array $values the access rules
   */
  public function setAccessRules($values)
  {
    $this->_accessRules = $values;
  }

  /**
   * Returns access rules
   * @return array
   */
  public function getAccessRules()
  {
    return $this->_accessRules;
  }

  /**
   * Return available roles.
   * Guest and User roles are predefined. Anything else loads from VAccountRole
   */
  public function loadFromFile($file)
  {
    $roles = array(
      'guest' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Guest',
        'bizRule' => null,
        'data' => null,
      ),
      'user' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'User',
        'children' => array(
          'guest',
        ),
        'bizRule' => null,
        'data' => null,
      ),
    );

    return array_merge($roles, $this->getActionRoles(), $this->getAdminRoles());
  }

  /**
   * Returns the administrator's roles list
   * @return array
   */
  public function getAdminRoles()
  {
    if ($this->_adminRoles === null) {
      $this->_adminRoles = Yii::app()->cache->get('Vira.Core.AdminRole');
      if ($this->_adminRoles === false) {
        $this->_adminRoles = array();

        foreach (VAccountRole::model()->findAll() as $role) {
          $this->_adminRoles[$role->id] = array(
            'type' => CAuthItem::TYPE_ROLE,
            'description' => $role->title,
            'bizRule' => null,
            'data' => null,
          );
        }

        $this->_adminRoles[self::ROLE_SUPERADMIN]['children'] = array_keys($this->getActionRoles());

        Yii::app()->cache->set(
          'Vira.Core.AdminRole', $this->_adminRoles, Yii::app()->params['defaultCacheDuration'], new VTaggedCacheDependency(
          'Vira.Role', Yii::app()->params['defaultCacheTagDuration']
          )
        );
      }
    }

    return $this->_adminRoles;
  }

  /**
   * Return action roles
   * @return array
   */
  public function getActionRoles()
  {
    if ($this->_actionRoles === null) {
      $this->_actionRoles = array();
      foreach ($this->_accessRules as $id => $rule) {
        $this->_actionRoles[$id] = array(
          'type' => CAuthItem::TYPE_ROLE,
          'description' => $rule['title'],
          'bizRule' => null,
          'data' => null,
        );
      }
    }

    return $this->_actionRoles;
  }

  /**
   * Return administrator' roles access rules
   * @param string $roleID role identifier
   * @return array
   */
  public function getAdminRoleRules($roleID)
  {
    if (empty($this->_adminAccessRules)) {
      $this->_adminAccessRules = Yii::app()->cache->get('Vira.Core.AdminAccessRule');
      if ($this->_adminAccessRules === false) {
        $this->_adminAccessRules = array();
        foreach (VAccountAccess::model()->findAllByAttributes(array('permit' => '1')) as $rule) {
          $this->_adminAccessRules[$rule->accountRoleID][] = $rule->accessRuleID;
        }

        Yii::app()->cache->set(
          'Vira.Core.AdminAccessRule', $this->_adminAccessRules, Yii::app()->params['defaultCacheDuration'], new VTaggedCacheDependency(
          'Vira.Role', Yii::app()->params['defaultCacheTagDuration']
          )
        );
      }
    }

    return isset($this->_adminAccessRules[$roleID]) ? $this->_adminAccessRules[$roleID] : array();
  }

  /**
   * Checking access rules list
   * @param mixed $list can be array or common separated items string
   * @param boolean $strict strict checking
   * @return boolean
   */
  public function checkAccessList($list, $strict = false)
  {
    $ok = $strict;

    if (!is_array($list)) {
      $list = explode(',', $list);
    }

    foreach ($list as $entry) {
      $access = $this->checkAccess($entry, Yii::app()->user->id);
      $ok = $strict ? ($ok && $access) : ($ok || $access);
    }

    return $ok;
  }
}
