<?php
/**
 * ViraCMS Application Settings Form
 *
 * @package vira.core.core
 * @subpackage vira.core.admin
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class ApplicationSettings extends CFormModel
{
  const PACKAGE_CONFIG_TEMPLATE_ALIAS = 'application.data.packages';
  const PACKAGE_SETTINGS_FILE_NAME = 'settings.php';
  const APPLICATION_ALIAS = 'application';
  const APPLICATION_CONFIG_ALIAS = 'application.config';
  const APPLICATION_CONSTANT_FILE = 'const.php';
  const LOCAL_OVERRIDE_FILE = 'local.php';

  private $_sections = array();
  public $section = 'index';
  private $config = array();
  private $mode;
  private $override;

  /**
   * The component initialization
   * Loads configuration template and local override files
   */
  public function init()
  {
    $installed = $this->loadInstalledPackages();
    foreach ($installed as $package) {
      $file = implode(DIRECTORY_SEPARATOR, array(
        Yii::getPathOfAlias(self::PACKAGE_CONFIG_TEMPLATE_ALIAS),
        $package['id'],
        self::PACKAGE_SETTINGS_FILE_NAME,
      ));

      if (file_exists($file)) {
        $data = require $file;
        $this->config = CMap::mergeArray($this->config, $data);
      }
    }

    $file = implode(DIRECTORY_SEPARATOR, array(
      Yii::getPathOfAlias(self::APPLICATION_CONFIG_ALIAS),
      self::LOCAL_OVERRIDE_FILE,
    ));

    if (file_exists($file)) {
      $this->override = require $file;
    }

    foreach ($this->config as $section => $data) {
      $this->_sections[$section] = Yii::t('admin.titles', $data['label']);
    }
  }

  /**
   * Loads and returns installed packages list
   * @return array
   */
  protected function loadInstalledPackages()
  {
    $filename = Yii::getPathOfAlias(self::PACKAGE_CONFIG_TEMPLATE_ALIAS) . DIRECTORY_SEPARATOR . 'installed.php';

    return $filename && file_exists($filename) ? require $filename : array();
  }

  /**
   * Returns the configuration template
   * @return array
   */
  public function getTemplate()
  {
    return $this->config;
  }

  /**
   * Is application core component update available?
   * @return boolean
   * @todo need to be rewritten to not rely on the cache component
   */
  public function isUpdateAvailable()
  {
    return Yii::app()->format->formatBoolean(
        ($version = Yii::app()->cache->get('Vira.Core.Update')) !== false ?
        version_compare(Yii::app()->getVersion(), $version) === -1 :
        false
    );
  }

  /**
   * Returns configuration file path
   * @param string $filename the file name
   * @return string
   */
  protected function getConfigFile($filename)
  {
    return implode(DIRECTORY_SEPARATOR, array(
      Yii::getPathOfAlias(self::APPLICATION_CONFIG_ALIAS),
      $filename . '.php',
    ));
  }

  /**
   * Is filename writeable?
   * @param string $filename configuration file name
   * @return boolean
   */
  public function isWriteable($filename)
  {
    return is_writable($this->getConfigFile($filename));
  }

  /**
   * Check maintenance mode is enabled
   * @return boolean
   */
  public function getIsMaintenanceMode()
  {
    return Yii::app()->format->formatBoolean(Yii::app()->maintenance);
  }

  /**
   * Returns application core path (usually is 'protected' directory)
   * @return string
   */
  public function getCorePath()
  {
    return Yii::getPathOfAlias(self::APPLICATION_ALIAS);
  }

  /**
   * Returns current section label
   * @return string
   */
  public function currentMenuLabel()
  {
    return $this->getMenuLabel($this->section);
  }

  /**
   * Returns menu label for specified section
   * @param string $section the section identifier
   * @return string
   */
  public function getMenuLabel($section)
  {
    $sections = $this->getSections();
    return isset($sections[$section]) ? $sections[$section] : ucfirst($section);
  }

  /**
   * Returns available sections
   * @return array
   */
  public function getSections()
  {
    return CMap::mergeArray(
        array('index' => Yii::t('admin.titles', 'Summary')), $this->_sections
    );
  }

  /**
   * Returns current section categories
   * @return array
   */
  public function getSectionCategories()
  {
    return empty($this->config[$this->section]['categories']) ?
      array(array('label' => '')) :
      $this->config[$this->section]['categories'];
  }

  /**
   * Returns configuration template for the section and the category
   * @param string $section section identifier
   * @param string $categoryID category identifier
   * @return array
   */
  public function getCategory($section, $categoryID)
  {
    return empty($this->config[$section]['categories'][$categoryID]) ?
      array() :
      $this->config[$section]['categories'][$categoryID];
  }

  /**
   * Returns configuration options for the category
   * @param array $category category part of configuration template
   * @return array
   */
  public function getItems($category)
  {
    $items = array();
    if (isset($category['items']) && count($category['items'])) {
      foreach ($category['items'] as $name => $item) {
        $path = explode('.', $item['path']);
        $path[] = $name;
        $path = array_filter($path);

        $item['name'] = 'config[' . implode('][', $path) . ']';
        $item['id'] = implode('.', $path);

        $override = $this->getOverrideValue($item['id']);
        $item['value'] = isset($item['valueExpression']) ?
          $this->getCurrentValue(
            $item['valueExpression'], $override ?
            $override :
            $item['defaultValue']
          ) :
          $override;

        $items[] = $item;
      }
    }

    return $items;
  }

  /**
   * Returns current application mode
   * @return string
   */
  public function getMode()
  {
    if (!$this->mode) {
      $const = $this->getConfigFile(self::APPLICATION_CONSTANT_FILE);
      $content = file_exists($const) ? file_get_contents($const) : null;
      if ($content && preg_match("/define\('YII_MODE','(\w+)'\)/", $content, $matches)) {
        $this->mode = $matches[1];
      }
      else {
        $this->mode = YII_MODE;
      }
    }

    return $this->mode;
  }

  /**
   * Returns local configuration override values
   * @return array
   */
  public function getOverride()
  {
    return $this->override;
  }

  /**
   * Sets new local configuration override values
   * @param array $override
   */
  public function setOverride($override)
  {
    if (is_array($override)) {
      $this->override = CMap::mergeArray($this->override, $override);
    }
  }

  /**
   * Returns configuration option specified by the identifier
   * @param string $id the identifier
   * @return mixed
   */
  public function getOverrideValue($id)
  {
    $path = explode('.', $id);
    $reference = & $this->override;
    foreach ($path as $key) {
      if (isset($reference[$key])) {
        $reference = & $reference[$key];
      }
      else {
        $reference = null;
        break;
      }
    }

    return $reference;
  }

  /**
   * Sets configuration option specified by the identifier
   * @param string $id the identifier
   * @param mixed $value new value
   */
  public function setOverrideValue($id, $value)
  {
    $path = explode('.', $id);
    $reference = & $this->override;

    foreach ($path as $key) {
      if (!isset($reference[$key])) {
        $reference[$key] = array();
      }
      $reference = & $reference[$key];
    }

    $reference = $value;
  }

  /**
   * Returns evaluated option value
   * @param string $expression php expression
   * @param mixed $defaultValue default value
   * @return mixed
   */
  public function getCurrentValue($expression, $defaultValue)
  {
    $value = $expression ? $this->evaluateExpression($expression, array(
        'override' => $this->override,
      )) : null;

    return $value !== null ? $value : $defaultValue;
  }

  /**
   * Returns cache engines list
   * All engines will be checked for availability
   * (this is used in cache configuration)
   * @return array
   */
  public function getCacheEngines()
  {
    $cache = Yii::app()->hasComponent('cache') ? Yii::app()->cache : null;

    $engines = array(
      'file' => array(
        'available' => true,
        'active' => $cache && get_class($cache) == 'CFileCache',
        'title' => Yii::t('admin.titles', 'File Cache'),
        'form' => 'FileCacheConfig',
      ),
      'db' => array(
        'available' => true,
        'active' => $cache && get_class($cache) == 'CDbCache',
        'title' => Yii::t('admin.titles', 'Database Cache'),
        'form' => 'DbCacheConfig',
      ),
      'memcache' => array(
        'available' => class_exists('Memcache', false),
        'active' => $cache && get_class($cache) == 'CMemCache',
        'title' => 'MemCache',
        'form' => 'MemCacheConfig',
      ),
      'redis' => array(
        'available' => function_exists('stream_socket_client'),
        'active' => $cache && get_class($cache) == 'CRedisCache',
        'title' => 'Redis',
        'form' => 'RedisCacheConfig',
      ),
      'eaccelerator' => array(
        'available' => extension_loaded('eaccelerator'),
        'active' => $cache && get_class($cache) == 'CEAcceleratorCache',
        'title' => 'EAccelerator',
        'form' => 'EAcceleratorCacheConfig',
      ),
      'win' => array(
        'available' => extension_loaded('wincache') && ini_get('wincache.ucenabled'),
        'active' => $cache && get_class($cache) == 'CWinCache',
        'title' => 'WinCache',
        'form' => 'WinCacheConfig',
      ),
      'xcache' => array(
        'available' => function_exists('xcache_isset'),
        'active' => $cache && get_class($cache) == 'CXCache',
        'title' => 'XCache',
        'form' => 'XCacheConfig',
      ),
    );

    return $engines;
  }

  /**
   * Formats configuration option parameters for the partial view
   * @param array $item the configuration option
   * @return array
   */
  public function getItemParams($item)
  {
    $params = $item;

    if (isset($item['model']) && isset($item['key']) && isset($item['attribute'])) {
      $params['values'] = array();
      $className = $item['model'];
      if (@class_exists($className)) {
        $class = new $className();
        $params['values'] = CHtml::listData($class->findAll(), $item['key'], $item['attribute']);
      }
    }
    elseif (isset($item['valuesExpression']) && $item['valuesExpression']) {
      $params['values'] = $this->evaluateExpression($item['valuesExpression']);
    }

    if (isset($params['valuesTranslate'])) {
      foreach ($params['values'] as $key => $value) {
        $params['values'][$key] = Yii::t($params['valuesTranslate'], $value);
      }
    }

    return $params;
  }
}
