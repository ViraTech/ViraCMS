<?php
/**
 * ViraCMS Application Bootstrap Class
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VApplicationBootstrap
{
  /**
   * Config caching period, sec
   */
  const CACHE_PERIOD = 3600;

  /**
   * Type: console application
   */
  const APPLICATION_CONSOLE = 'console';

  /**
   * Type: web application
   */
  const APPLICATION_WEB = 'web';

  /**
   * @var string the application type
   */
  private $application;

  /**
   * @var string runtime directory to store cache files
   */
  private $runtimeDir;

  /**
   * @var string configuration directory
   */
  private $configDir;

  /**
   * @var mixed configuration storage
   */
  private $config;

  /**
   * @var mixed configuration files list
   */
  private $files;

  /**
   * Class constructor
   * @param string $dir base directory of configuration store
   * @param string $application the application type (@see self::APPLICATION_* constants)
   */
  function __construct($dir, $application = self::APPLICATION_WEB)
  {
    $this->application = $application;
    $this->configDir = $dir;
    $this->runtimeDir = implode(DIRECTORY_SEPARATOR, array(
      dirname(dirname($dir)),
      'runtime',
    ));
    $this->config = null;
  }

  /**
   * Create and start the application
   * @return mixed
   */
  public static function start($configDir, $application)
  {
    $bootstrap = new self($configDir, $application);
    $app = Yii::createApplication($bootstrap->getApplicationClass(), $bootstrap->getConfig());
    $app->run();
  }

  /**
   * Get configuration depends on application type
   * @param mixed $application application type
   * @return array
   */
  public function getConfig()
  {
    if (empty($this->config)) {
      $this->config = $this->getCache();
      if ($this->config == null) {
        $this->config = $this->generateConfig();
        $this->setCache();
      }
    }

    return $this->config;
  }

  /**
   * Config generator
   * @return CMap
   */
  private function generateConfig()
  {
    $config = array();
    $files = $this->getConfigFiles();
    foreach ($files as $file) {
      $data = include_once($file);
      if (is_array($data) && count($data)) {
        $config = CMap::mergeArray($config, $data);
      }
    }

    return $this->applyHostSpecific($config);
  }

  /**
   * Apply current host specific params
   * @param array $config current configuration
   * @return array configuration with applied host specific params
   */
  private function applyHostSpecific($config)
  {
    // current host overrides
    if (VIRA_HOST && file_exists($this->configDir . DIRECTORY_SEPARATOR . 'hosts.php')) {
      $data = include_once($this->configDir . DIRECTORY_SEPARATOR . 'hosts.php');
      if (isset($data[VIRA_HOST])) {
        $config = CMap::mergeArray($config, $data[VIRA_HOST]);
      }
    }

    return $config;
  }

  /**
   * Returns configuration files list
   * @return CMap
   */
  private function getConfigFiles()
  {
    if (empty($this->files)) {
      $files = array();

      $dirs = array(
        'common',
        'components',
        'widgets',
        'modules',
        'themes',
        'database',
        $this->application,
      );

      foreach ($dirs as $dir) {
        $find = glob(implode(DIRECTORY_SEPARATOR, array(
          $this->configDir,
          $dir,
          '*.php',
        )));
        if (is_array($find) && count($find)) {
          $files = CMap::mergeArray($files, $find);
        }
      }

      // local overrides
      if (file_exists($this->configDir . DIRECTORY_SEPARATOR . 'local.php')) {
        $files[] = $this->configDir . DIRECTORY_SEPARATOR . 'local.php';
      }

      $this->files = $files;
    }

    return $this->files;
  }

  /**
   * Trying to get configuration from cache file
   * @return mixed
   */
  private function getCache()
  {
    $config = null;

    $file = $this->getCacheFile();
    if (file_exists($file)) {
      if (filemtime($file) > (time() + self::CACHE_PERIOD)) {
        @unlink($file);
      }
      else {
        $config = file_get_contents($file);
        $config = @unserialize($config);
        if (!is_array($config)) {
          $config = null;
        }
      }
    }

    return $config;
  }

  /**
   * Put configuration to cache
   */
  private function setCache()
  {
    if (is_array($this->config)) {
      $file = $this->getCacheFile();
      if (is_writable($file) || is_writable(dirname($file))) {
        file_put_contents($file, serialize($this->config));
        chmod($file, 0666);
      }
    }
  }

  /**
   * Generate configuration cache file name
   * @return string
   */
  private function getCacheFile()
  {
    return $this->runtimeDir . DIRECTORY_SEPARATOR . $this->application . '.conf.cache';
  }

  /**
   * Returns current application class name
   * @return string
   */
  private function getApplicationClass()
  {
    switch ($this->application) {
      case self::APPLICATION_CONSOLE:
        return 'VConsoleApplication';

      default:
        return 'VWebApplication';
    }
  }
}
