<?php
/**
 * ViraCMS Default Web Application Component
 * Based On Yii Framework CWebApplication Class
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VWebApplication extends CWebApplication
{
  /**
   * @var array list of components must be preinitialized
   */
  public $preinit = array();

  /**
   * @var VTheme the theme object
   */
  private $_theme;

  /**
   * @var mixed the theme name
   */
  private $_themeName;

  /**
   * @var mixed the theme type
   */
  private $_themeType;

  /**
   * @var string the backend language identifier
   */
  private $_backendLanguage;

  /**
   * @var boolean the maintenance mode flag
   */
  public $maintenance = false;

  /**
   * @var array the behaviors configuration
   */
  public $behaviors = array(
    array(
      'class' => 'core.behaviors.VSiteBehavior',
    ),
  );

  /**
   * @var string the application core version
   */
  protected $_version;

  /**
   * @var boolean the component initialized flag
   */
  protected $_initialized = false;

  /**
   * Additional initialization of the web application
   */
  public function init()
  {
    parent::init();
    putenv('TMPDIR=' . $this->getRuntimePath());
    $this->preinitComponents();
    $this->_initialized = true;
  }

  /**
   * Initializes components from self::$preinit list
   */
  protected function preinitComponents()
  {
    foreach ($this->preinit as $id) {
      if ($component = $this->getComponent($id)) {
        $component->init();
      }
    }
  }

  /**
   * Return current application theme
   * @return VTheme
   */
  public function getTheme()
  {
    return $this->_theme;
  }

  /**
   * Set application theme
   * @param string $name the theme name
   * @param string $type the theme type
   */
  public function setTheme($name, $type)
  {
    $this->_themeName = $name;
    $this->_themeType = $type;
    if ($name && $type) {
      $this->_theme = $this->getThemeManager()->getTheme($this->_themeName, $this->_themeType);
    }
    else {
      $this->_theme = null;
    }
  }

  /**
   * Return application backend language
   * @return string
   */
  public function getBackendLanguage()
  {
    return $this->_backendLanguage ? $this->_backendLanguage : $this->sourceLanguage;
  }

  /**
   * Set application backend language
   * @param string $language language identifier
   */
  public function setBackendLanguage($language)
  {
    $this->_backendLanguage = $language;
  }

  /**
   * Sets ViraCMS application version
   * @param type $value
   */
  public function setVersion($value)
  {
    if ($this->_initialized === false) {
      $this->_version = $value;
    }
  }

  /**
   * Returns ViraCMS application version
   * @return string
   */
  public function getVersion()
  {
    return $this->_version;
  }

  public function setLicenseKey($value)
  {
  }

  public function getLicenseKey()
  {
    return 'COMMUNITY-EDITION';
  }
}
