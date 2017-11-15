<?php
/**
 * ViraCMS Default Web Module Component
 * Based On Yii Framework CWebModule Class
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VWebModule extends CWebModule
{
  /**
   * @var string module assets directory path
   */
  public $assetsDir;

  /**
   * @var string module assets base URL
   */
  public $assetsUrl;

  /**
   * @var boolean enable publishing and register of assets
   */
  public $publish = false;

  /**
   * Module initiation
   */
  public function init()
  {
    Yii::app()->eventManager->attach($this);
    parent::init();

    if ($this->publish) {
      $this->publishAssets();
    }
  }

  /**
   * Publish module assets
   */
  public function publishAssets()
  {
    $this->assetsDir = $this->getBasePath() . DIRECTORY_SEPARATOR . 'assets';
    if (file_exists($this->assetsDir) && is_dir($this->assetsDir)) {
      $this->assetsUrl = Yii::app()->assetManager->publish($this->assetsDir);
      $this->registerAssets();
    }
  }

  /**
   * Register published assets
   * Assets can be accessed as self::$assetsDir locally and self::$assetsUrl remotely
   */
  public function registerAssets()
  {
    
  }

  /**
   * Return class base alias
   * @return string
   */
  public function getBaseAlias()
  {
    $applicationPath = Yii::app()->basePath;
    $modulePath = $this->getBasePath();

    return 'application.' . str_replace(DIRECTORY_SEPARATOR, '.', trim(str_replace($applicationPath, '', $modulePath), DIRECTORY_SEPARATOR));
  }
}
