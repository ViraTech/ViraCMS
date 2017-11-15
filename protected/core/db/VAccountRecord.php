<?php
/**
 * ViraCMS Site Accounts Base Model
 *
 * @package vira.core.core
 * @subpackage vira.core.registry
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VAccountRecord extends VActiveRecord
{
  /**
   * Length of password salt
   */
  const PASSWORD_SALT_LENGTH = 16;

  /**
   * @var string set new password attribute
   */
  public $newPassword;

  /**
   * @var string confirm set new password attribute
   */
  public $newPasswordConfirm;

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

  protected function beforeSave()
  {
    if (parent::beforeSave()) {
      if (in_array($this->scenario, array('passwordUpdate', 'create'))) {
        $this->password = $this->hashPassword($this->newPassword);
      }

      return true;
    }

    return false;
  }

  /**
   * Event fired when requested password change by password restoration procedure
   * @param CEvent $event event
   */
  public function onRestorePasswordRequest($event)
  {
    $this->raiseEvent('onRestorePasswordRequest', $event);
  }

  /**
   * Event fired when password was changed by password restoration procedure
   * @param CEvent $event event
   */
  public function onRestorePasswordChange($event)
  {
    $this->raiseEvent('onRestorePasswordChange', $event);
  }

  /**
   * Event fired when error occurred while user changing password
   * @param CEvent $event event
   */
  public function onRestorePasswordChangeError($event)
  {
    $this->raiseEvent('onRestorePasswordChangeError', $event);
  }

  /**
   * Event fired when user changes his password
   * @param CEvent $event event
   */
  public function onPasswordChange($event)
  {
    $this->raiseEvent('onPasswordChange', $event);
  }

  /**
   * Event fired when user was disabled
   * @param CEvent $event event
   */
  public function onDisable($event)
  {
    $this->raiseEvent('onDisable', $event);
  }

  /**
   * Event fired when user was enabled
   * @param CEvent $event event
   */
  public function onEnable($event)
  {
    $this->raiseEvent('onEnable', $event);
  }

  /**
   * Event fired when error occurred whil user logging in
   * @param CEvent $event event
   */
  public function onLoginError($event)
  {
    $this->raiseEvent('onLoginError', $event);
  }

  /**
   * Event fired when user has been logged in
   * @param CEvent $event event
   */
  public function onAfterLogin($event)
  {
    $this->raiseEvent('onAfterLogin', $event);
  }

  /**
   * Event fired before user logged out
   * @param CEvent $event event
   */
  public function onBeforeLogout($event)
  {
    $this->raiseEvent('onBeforeLogout', $event);
  }

  /**
   * Event fired after user logged out
   * @param CEvent $event event
   */
  public function onAfterLogout($event)
  {
    $this->raiseEvent('onAfterLogout', $event);
  }

  /**
   * Called after administrator logged in
   */
  public function afterLogin()
  {
    $this->onAfterLogin(new CEvent($this));
  }

  /**
   * Called before administrator logged out
   */
  public function beforeLogout()
  {
    $this->onBeforeLogout(new CEvent($this));
  }

  /**
   * Called after User logged out
   */
  public function afterLogout()
  {
    $this->onAfterLogout(new CEvent($this));
  }

  /**
   * Change User's password
   * @param string $password new password
   * @param string $salt (optional) new salt
   * @return boolean result after save model
   */
  public function setPassword($password, $salt = null)
  {
    if ($salt !== null) {
      $this->salt = $salt;
    }

    $this->password = $this->hashPassword($password);
    $result = $this->save();

    if ($result) {
      $this->onRestorePasswordChange(new CEvent($this, array(
        'password' => $password,
      )));
    }
    else {
      $this->onRestorePasswordChangeError(new CEvent($this, array(
        'password' => $password,
      )));
    }

    return $result;
  }

  /**
   * Validate User's password
   * @param string $password password
   * @return boolean validation was succeed
   */
  public function validatePassword($password)
  {
    return $this->hashPassword($password) === $this->password;
  }

  /**
   * Hash User password with system salt
   * @param string $password password
   * @return string hashed password
   */
  public function hashPassword($password)
  {
    if (empty($this->salt)) {
      $this->salt = Yii::app()->passwordGenerator->generate(self::PASSWORD_SALT_LENGTH);
    }

    return md5($this->salt . $password);
  }
}
