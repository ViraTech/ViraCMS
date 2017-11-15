<?php
/**
 * ViraCMS Application Theme's Manager Component
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VThemeManager extends VApplicationComponent
{
  const THEME_FRONTEND = 'frontend';
  const THEME_BACKEND = 'backend';
  const DEFAULT_BASEPATH_DIRECTORY = 'themes';
  const DEFAULT_BASEPATH_FRONTEND = 'frontend';
  const DEFAULT_BASEPATH_BACKEND = 'backend';

  /**
   * @var string default themes class name
   */
  public $themeClass = 'VTheme';

  /**
   * @var string the captcha action class name
   */
  public $captchaActionClass = 'VCaptchaAction';

  /**
   * @var string default frontend theme name
   */
  public $frontendTheme;

  /**
   * @var string default backend theme name
   */
  public $backendTheme;

  /**
   * @var array themes base path cache
   */
  private $_basePath = array();

  /**
   * @var array themes base URl cache
   */
  private $_baseUrl = array();

  /**
   * @var array available themes cache
   * Format is:
   * <pre>
   * array(
   *   'backend' => array( // backend themes
   *     'themeName' => array(
   *       'title' => 'Some Backend Theme', // theme name
   *       'titleTranslate' => 'translate_category', // theme name' translate category
   *       'css' => array( // styles list needs to be attached
   *         'style.css', // with or without extension
   *       ),
   *       'scripts' => array(
   *         'head' => array( // script position - head, begin, end, load or ready as described in CClientScript
   *           'script.js', // with or without extension
   *         ),
   *       ),
   *       'imageDir'='img', // image directory relative to theme root
   *       'logo' => 'logo.jpg', // logo image path relative to image directory or theme root (if imageDir is not set)
   *       'placeholder' => 'placeholder.jpg', // the placeholder image
   *       'bootstrapCss' => false, // register bootstrap core css file
   *       'responsiveCss' => false, // register bootstrap responsive css file
   *       'yiiCss' => false, // register bootstrap specific YII css file
   *       'bootstrapJs' => false, // register bootstrap js functions file
   *       'captchaOptions' => array(), // captcha options as it's required for @see VCaptchaAction
   *     ),
   *  ),
   *  'frontend' => array( // frontend themes
   *    'themeName' => array( ... ),
   * )
   * </pre>
   */
  private $_themes = array();

  /**
   * Set themes config
   * @param array $values the themes configuration array
   */
  public function setThemes($values)
  {
    $this->_themes = $values;
  }

  /**
   * Returns themes configuration
   * @return array
   */
  public function getThemes()
  {
    return $this->_themes;
  }

  /**
   * Return theme object as given name and type or null if no theme found
   * @param string $name theme name
   * @param string $type theme type
   * @return mixed
   */
  public function getTheme($name, $type = self::THEME_FRONTEND)
  {
    $path = $this->getBasePath($type) . DIRECTORY_SEPARATOR . $name;
    $url = $this->getBaseUrl($type) . '/' . $name;
    $params = isset($this->_themes[$type][$name]) ? $this->_themes[$type][$name] : array();

    if (is_dir($path)) {
      $class = Yii::import($this->themeClass, true);

      return new $class($type, $name, $params, $path, $url);
    }
    else {
      return null;
    }
  }

  /**
   * Return all availabe themes for given type
   * @staticvar array $themes themes cache
   * @param string $type themes type
   * @return array
   */
  public function getThemeNames($type = self::THEME_FRONTEND)
  {
    static $themes;

    if ($themes === null) {
      $themes = array();
    }

    if (empty($themes[$type])) {
      $themes[$type] = array();
      $found = glob($this->getBasePath($type));
      foreach ($found as $file) {
        if (is_dir($file) && !in_array($file, array('.', '..', '.svn'))) {
          $themes[$type][] = basename($file);
        }
      }
      sort($themes[$type]);
    }

    return $themes[$type];
  }

  /**
   * Finds if frontend theme with given name is exist
   * @param string $name theme name
   * @return boolean
   */
  public function getIsFrontendThemeExist($name)
  {
    return isset($this->themes[self::THEME_FRONTEND][$name]);
  }

  /**
   * Returns frontend themes list available for dropdowns
   * @return array
   */
  public function getFrontendThemes()
  {
    return $this->getThemesDropdown(VThemeManager::THEME_FRONTEND);
  }

  /**
   * Return backend themes list available for dropdowns
   * @return array
   */
  public function getBackendThemes()
  {
    return $this->getThemesDropdown(VThemeManager::THEME_BACKEND);
  }

  /**
   * Returns themes list of certain type.
   * Theme names will be translated if translation is available.
   * @param string $type the theme type
   * @return array
   */
  protected function getThemesDropdown($type)
  {
    $themes = array();

    foreach ($this->themes[VThemeManager::THEME_FRONTEND] as $id => $config) {
      $title = $config['title'];

      if (isset($config['titleTranslate'])) {
        $title = Yii::t($config['titleTranslate'], $title);
      }

      $themes[$id] = $title;
    }

    return $themes;
  }

  /**
   * Returns all of available theme layouts
   * @param string $name the theme name
   * @param string $type the theme type
   * @return array
   */
  public function getThemeLayouts($name, $type = VThemeManager::THEME_FRONTEND)
  {
    $layouts = array();

    // collect theme layouts
    $theme = Yii::app()->themeManager->getTheme($name, $type);
    if (!empty($theme)) {
      $themeLayouts = $theme->getLayouts();
    }

    if (!empty($themeLayouts)) {
      foreach ($themeLayouts as $layout) {
        $layouts[Yii::t('common', 'Theme {name} Layouts', array('{name}' => $theme->name))][$layout] = $layout;
      }
    }

    $files = glob(Yii::getPathOfAlias('application.views.layouts') . DIRECTORY_SEPARATOR . '*.php');
    foreach ($files as $file) {
      if (is_file($file)) {
        $layout = basename($file, '.php');
        if (!isset($layouts[$layout])) {
          $layouts[Yii::t('common', 'System Layouts')][$layout] = $layout;
        }
      }
    }

    return $layouts;
  }

  /**
   * Return themes base path for specified type
   * @param string $type themes type
   * @return string
   */
  public function getBasePath($type = self::THEME_FRONTEND)
  {
    if (empty($this->_basePath[$type])) {
      $basePath = self::DEFAULT_BASEPATH_DIRECTORY . DIRECTORY_SEPARATOR;
      $basePath .= $type == self::THEME_FRONTEND ? self::DEFAULT_BASEPATH_FRONTEND : self::DEFAULT_BASEPATH_BACKEND;
      $this->setBasePath(dirname(Yii::app()->request->scriptFile) . DIRECTORY_SEPARATOR . $basePath, $type);
    }

    return $this->_basePath[$type];
  }

  /**
   * Set base directory path for selected themes type
   * @param string $value directory path
   * @param string $type themes type
   * @throws CException
   */
  public function setBasePath($value, $type = self::THEME_FRONTEND)
  {
    $this->_basePath[$type] = realpath($value);
    if (empty($this->_basePath[$type]) || !is_dir($this->_basePath[$type])) {
      throw new CException(Yii::t('yii', 'Theme directory "{directory}" does not exist.', array('{directory}' => $value)));
    }
  }

  /**
   * Return base URL for given themes type
   * @param string $type themes type
   * @return string
   */
  public function getBaseUrl($type = self::THEME_FRONTEND)
  {
    if (empty($this->_baseUrl[$type])) {
      $baseUrl = Yii::app()->getBaseUrl() . '/' . self::DEFAULT_BASEPATH_DIRECTORY . '/';
      $baseUrl .= $type == self::THEME_FRONTEND ? self::DEFAULT_BASEPATH_FRONTEND : self::DEFAULT_BASEPATH_BACKEND;
      $this->setBaseUrl($baseUrl, $type);
    }

    return $this->_baseUrl[$type];
  }

  /**
   * Set base URL for given themes type
   * @param string $value base URL
   * @param string $type themes type
   */
  public function setBaseUrl($value, $type = self::THEME_FRONTEND)
  {
    $this->_baseUrl[$type] = rtrim($value, '/');
  }

  /**
   * Returns the default captcha params
   * @return array
   */
  public function getDefaultCaptchaOptions()
  {
    return array(
      'class' => $this->captchaActionClass,
    );
  }

  /**
   * Return CClientScript position for theme script position
   * @param string $position theme position
   * @return integer CClientScript position
   */
  public function getPosition($position)
  {
    $positions = $this->scriptPositions();
    if (isset($positions[$position])) {
      return $positions[$position];
    }

    return CClientScript::POS_READY;
  }

  /**
   * CClientScript over theme script position mappings
   * @return array
   */
  private function scriptPositions()
  {
    return array(
      'head' => CClientScript::POS_HEAD,
      'begin' => CClientScript::POS_BEGIN,
      'end' => CClientScript::POS_END,
      'load' => CClientScript::POS_LOAD,
      'ready' => CClientScript::POS_READY,
    );
  }
}
