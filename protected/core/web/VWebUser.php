<?php
/**
 * ViraCMS Web Application User Component
 * Based On Yii Framework CWebUser Class
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VWebUser extends CWebUser
{
  /**
   * Model storage variable
   * @var VAccountRecord
   */
  private $_model;

  /**
   * @var integer user's type
   */
  private $_type = VAccountTypeCollection::GUEST;

  /**
   * Application component initialization
   */
  public function init()
  {
    parent::init();

    if (!$this->getIsGuest()) {
      $this->loadModel($this->id);
      if ($this->_model == null) {
        $this->logout();
      }
    }
  }

  /**
   * Returns User attribute
   * @param string $attribute attribute name
   * @return mixed
   */
  public function getAttribute($attribute)
  {
    $model = $this->loadModel($this->id);

    return !Yii::app()->user->getIsGuest() && $model != null && $model->hasAttribute($attribute) ? $model->getAttribute($attribute) : '';
  }

  /**
   * Returns cached User model
   * @return VUser
   */
  public function getModel()
  {
    return $this->loadModel($this->id);
  }

  /**
   * Get User model from database, cache it locally
   * @param integer $__id
   * @return VUser User model
   */
  protected function loadModel($__id = null)
  {
    if ($this->_model === null) {
      if ($__id !== null) {
        switch ($this->area) {
          case VIdentity::AREA_ADMIN:
            $this->_model = VSiteAdmin::model()->with('language')->findByPk($__id);
            $this->setType(VAccountTypeCollection::ADMINISTRATOR);
            break;

          default:
            break;
        }
      }
    }

    return $this->_model;
  }

  /**
   * Processing login
   * @param type $identity
   * @param type $duration
   * @param type $area Site area
   */
  public function login($identity, $duration = 0)
  {
    $this->area = $identity->getArea();

    return parent::login($identity, $duration);
  }

  /**
   * Method called right after User logged in
   * @param boolean $fromCookie
   */
  protected function afterLogin($fromCookie)
  {
    if (!$fromCookie && $this->model != null) {
      $this->model->afterLogin();
    }

    parent::afterLogin($fromCookie);
  }

  /**
   * Method called before User logged out
   * Loads model for event processing
   */
  protected function beforeLogout()
  {
    if (parent::beforeLogout()) {
      $model = $this->loadModel($this->id);

      if ($model) {
        $model->beforeLogout();
      }

      return true;
    }

    return false;
  }

  /**
   * Method called after User logged out
   */
  protected function afterLogout()
  {
    if ($this->_model) {
      $this->_model->afterLogout();
    }

    $this->_type = VAccountTypeCollection::GUEST;
    $this->area = VIdentity::AREA_DEFAULT;

    parent::afterLogout();
  }

  /**
   * Get user's type (administrator, user or guest)
   * @return integer user's type
   */
  public function getType()
  {
    return $this->_type;
  }

  /**
   * Set user's type (administrator, user or guest)
   * @param integer $value
   */
  public function setType($value)
  {
    $this->_type = $value;
  }

  /**
   * Get site area
   * @return integer
   */
  public function getArea()
  {
    return $this->getState('area', VIdentity::AREA_DEFAULT);
  }

  /**
   * Set site area
   * @param integer $value
   */
  public function setArea($value)
  {
    $this->setState('area', $value);
  }

  /**
   * Set login URL (depends on site area)
   * @param string $loginUrl login URL
   */
  public function setLoginUrl($loginUrl)
  {
    $this->loginUrl = $loginUrl;
  }
}
