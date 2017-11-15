<?php
/**
 * ViraCMS Default Console Application
 * Based On Yii Framework CConsoleApplication Class
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VConsoleApplication extends CConsoleApplication
{
  /**
   * @var string the software license key
   */
  public $licenseKey;

  /**
   * @var array the behaviors configuration
   */
  public $behaviors = array(
    array(
      'class' => 'core.behaviors.VSiteBehavior',
    ),
  );

  /**
   * @var boolean maintenance mode flag
   */
  public $maintenance = false;

  /**
   * @var string backend language
   */
  public $backendLanguage;

  /**
   * @var string the application version
   */
  protected $_version;

  /**
   * @var boolean the component initialized flag
   */
  protected $_initialized = false;

  /**
   * Additional initialization of the console application
   */
  public function init()
  {
    parent::init();
    putenv('TMPDIR=' . $this->getRuntimePath());
    $this->_initialized = true;
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
}
