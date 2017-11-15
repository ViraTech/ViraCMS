<?php
/**
 * ViraCMS Administrator Login Credentials Form
 *
 * @package vira.core.core
 * @subpackage vira.core.admin
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class AdminCredentials extends CFormModel
{
  public $username;
  public $password;
  public $captcha;
  public $enableCaptcha;
  private $_identity;

  public function rules()
  {
    return array(
      array('username,password', 'required', 'message' => Yii::t('admin.registry.errors', 'please fill <strong>{attribute}</strong> field')),
      array('captcha', 'captcha', 'allowEmpty' => !($this->enableCaptcha && CCaptcha::checkRequirements()), 'captchaAction' => '/admin/captcha/captcha'),
      array('password', 'authenticate'),
    );
  }

  public function attributeLabels()
  {
    return array(
      'username' => Yii::t('admin.registry.labels', 'Username'),
      'password' => Yii::t('admin.registry.labels', 'Password'),
      'captcha' => Yii::t('admin.registry.labels', 'Verification Code'),
      'header' => Yii::t('admin.registry.labels', 'Sign In'),
    );
  }

  public function authenticate()
  {
    if (!$this->hasErrors()) {
      $this->_identity = new VIdentity($this->username, $this->password, VIdentity::AREA_ADMIN);
      if (!$this->_identity->authenticate()) {
        if ($this->_identity->errorCode == VIdentity::ERROR_USER_DISABLED) {
          $this->addError('password', Yii::t('common', 'account disabled'));
        }
        elseif ($this->_identity->errorCode == VIdentity::ERROR_SITE_ACCESS_DENIED) {
          $this->addError('password', Yii::t('admin.registry.errors', 'access denied to this site'));
        }
        else {
          $this->addError('password', Yii::t('common', 'access denied'));
        }
      }
    }
  }

  public function login()
  {
    if ($this->_identity === null) {
      $this->_identity = new VIdentity($this->username, $this->password, VIdentity::AREA_ADMIN);
      $this->_identity->authenticate();
    }
    if ($this->_identity->errorCode === VIdentity::ERROR_NONE) {
      Yii::app()->user->login($this->_identity, 3600);
      return true;
    }

    return false;
  }
}
