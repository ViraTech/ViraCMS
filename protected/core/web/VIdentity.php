<?php
/**
 * ViraCMS User Identity Component
 * Based On Yii Framework CUserIdentity Class
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VIdentity extends CUserIdentity
{
  /**
   * @var integer User's ID
   */
  private $_id;

  /**
   * @var array authenticate fields
   */
  private $_authAttributes = array(
    'username',
    'email',
  );

  /**
   * @var string site's area where authentication made
   */
  private $_area;

  /**
   * Default area (e.g. user authenticated on site)
   */
  const AREA_DEFAULT = 0;

  /**
   * Administration area
   */
  const AREA_ADMIN = 999;

  /**
   * Administrator have no access to site he trying to authorize
   */
  const ERROR_SITE_ACCESS_DENIED = 990;

  /**
   * Administrator is disabled or blocked
   */
  const ERROR_USER_DISABLED = 999;

  /**
   * Constructor.
   * @param string $username username
   * @param string $password password
   * @param integer $area site's area to authenticate
   */
  public function __construct($username, $password, $area = self::AREA_DEFAULT)
  {
    $this->username = $username;
    $this->password = $password;
    $this->_area = $area;
  }

  /**
   * Authenticates user
   * @return boolean authentication was successful
   */
  public function authenticate()
  {
    // adjust username
    $username = trim(strtolower($this->username));

    // get model
    $model = $this->getModel();

    // set find criteria
    $criteria = new CDbCriteria;
    foreach ($this->_authAttributes as $attribute) {
      if ($model->hasAttribute($attribute)) {
        $criteria->compare('LOWER(' . $attribute . ')', $username, false, 'OR');
      }
    }
    //$criteria->addInCondition('t.siteID',array(Yii::app()->site->id,0));
    // find any suitable subject
    $subject = $model->find($criteria);

    // subject is not found
    if ($subject === null) {
      $this->errorCode = self::ERROR_USERNAME_INVALID;
    }
    // password is not valid
    elseif (!$subject->validatePassword($this->password)) {
      $this->errorCode = self::ERROR_PASSWORD_INVALID;
    }
    // user is not active (disabled, blocked etc)
    elseif (!$subject->isActive) {
      $this->errorCode = self::ERROR_USER_DISABLED;
    }
    // access to this site is denied (for admins)
    elseif ($this->_area == self::AREA_ADMIN && $subject->siteAccess == 0 && !$subject->hasSiteAccess(Yii::app()->site->id)) {
      $this->errorCode = self::ERROR_SITE_ACCESS_DENIED;
    }
    // access to this site is denied (for users)
    elseif ($this->_area == self::AREA_DEFAULT && $subject->siteID != Yii::app()->site->id) {
      $this->errorCode = self::ERROR_SITE_ACCESS_DENIED;
    }
    // user successfully authenticated
    else {
      $this->_id = $subject->id;
      $this->username = $subject->getUsername();
      $this->errorCode = self::ERROR_NONE;
    }

    // log error if authentication was unsuccessful and we know the subject
    if ($this->errorCode != self::ERROR_NONE && $subject != null) {
      $subject->onLoginError(new CEvent($this, array('account' => $subject)));
    }

    return $this->errorCode == self::ERROR_NONE;
  }

  /**
   * Returns user's ID
   * @return integer
   */
  public function getId()
  {
    return $this->_id;
  }

  /**
   * Set authentication attributes
   * @param array $authAttributes model attributes used to authenticate
   */
  public function setAuthAttributes($authAttributes = array())
  {
    $this->_authAttributes = $authAttributes;
  }

  /**
   * Returns authentication attributes
   * @return array attributes used to authenticate
   */
  public function getAuthAttributes()
  {
    return $this->_authFields;
  }

  /**
   * Returns model object depend on authentication area
   */
  public function getModel()
  {
    switch ($this->_area) {
      case self::AREA_ADMIN:
        return VSiteAdmin::model();

      default:
        return null;
    }
  }

  /**
   * Returns site area when user is authenticated
   * @return integer
   */
  public function getArea()
  {
    return $this->_area;
  }
}
