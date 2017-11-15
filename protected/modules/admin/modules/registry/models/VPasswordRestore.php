<?php
/**
 * ViraCMS Restore Password Model
 *
 * @package vira.core.core
 * @subpackage vira.core.registry
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 *
 * @property string $id primary key
 * @property string $area site area
 * @property string $email user's e-mail
 * @property string $username user's login name
 * @property string $name user's name
 * @property integer $expire expiration timestamp
 */
class VPasswordRestore extends VActiveRecord
{
  /**
   * Default password restoration link time-to-live
   */
  const DEFAULT_PASSWORD_RESTORE_TTL = 86400;

  /**
   * @var string Captcha value
   */
  public $captcha;

  /**
   * @var mixed user account model
   */
  public $account;

  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }

  public function tableName()
  {
    return '{{core_password_restore}}';
  }

  public function rules()
  {
    return array(
      array('id', 'unsafe'),
      array('id', 'length', 'is' => 36),
      array('area,email', 'required'),
      array('area', 'in', 'range' => array(VIdentity::AREA_DEFAULT, VIdentity::AREA_ADMIN)),
      array('email,username,name', 'length', 'max' => 255),
      array('email', 'email'),
      array('captcha', 'captcha', 'allowEmpty' => !CCaptcha::checkRequirements(), 'captchaAction' => '/admin/captcha/captcha'),
      array('expire', 'numerical', 'integerOnly' => true),
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
      'email' => Yii::t('admin.registry.labels', 'E-Mail'),
      'captcha' => Yii::t('admin.registry.labels', 'Verification Code'),
    );
  }

  protected function beforeValidate()
  {
    if (parent::beforeValidate()) {
      if ($this->isNewRecord && $this->email) {
        switch ($this->area) {
          case VIdentity::AREA_ADMIN:
            $this->account = VSiteAdmin::model()->find("email=:email", array(':email' => $this->email));
            break;
        }

        if ($this->account == null) {
          $this->addError('email', Yii::t('admin.registry.errors', 'E-mail not found'));
        }
        else {
          $this->username = $this->account->username;
          $this->name = $this->account->name;
        }
      }

      return true;
    }

    return false;
  }

  protected function beforeSave()
  {
    if (parent::beforeSave()) {
      $this->expire = time() + self::DEFAULT_PASSWORD_RESTORE_TTL;
      return true;
    }

    return false;
  }

  protected function afterSave()
  {
    parent::afterSave();
    if ($this->account) {
      $this->account->onRestorePasswordRequest(new CEvent($this, array('account' => $this->account)));
    }
  }
}
