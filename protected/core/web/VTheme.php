<?php
/**
 * ViraCMS Base Theme Class
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VTheme extends CComponent
{
  const DEFAULT_IMAGE_DIR = 'img';

  /**
   * @var string theme type
   */
  private $_type;

  /**
   * @var string theme name
   */
  private $_name;

  /**
   * @var string theme files base path
   */
  private $_basePath;

  /**
   * @var string theme files base URL
   */
  private $_baseUrl;

  /**
   * @var array theme params
   */
  private $_params = array();

  /**
   * The theme contructor
   * @param string $type the theme type (backend or frontend)
   * @param string $name the theme name
   * @param array $params the theme parameters
   * @param string $basePath the theme base path
   * @param string $baseUrl the theme base url
   */
  public function __construct($type, $name, $params = array(), $basePath, $baseUrl)
  {
    $this->_type = $type;
    $this->_name = $name;
    $this->_params = $params;
    $this->_baseUrl = $baseUrl;
    $this->_basePath = $basePath;
  }

  /**
   * Return this theme type
   * @return string
   */
  public function getType()
  {
    return $this->_type;
  }

  /**
   * Return theme name
   * @return string
   */
  public function getName()
  {
    return $this->_name;
  }

  /**
   * Return theme base URL
   * @return string
   */
  public function getBaseUrl()
  {
    return $this->_baseUrl;
  }

  /**
   * Return theme base path
   * @return string
   */
  public function getBasePath()
  {
    return $this->_basePath;
  }

  /**
   * Return theme views path
   * @return string
   */
  public function getViewPath()
  {
    return implode(DIRECTORY_SEPARATOR, array(
      Yii::app()->basePath,
      'themes',
      $this->_type,
      $this->_name,
      'views',
    ));
  }

  /**
   * Return theme layouts path
   * @return string
   */
  public function getLayoutPath()
  {
    return $this->getViewPath() . DIRECTORY_SEPARATOR . 'layouts';
  }

  /**
   * Return theme system views path
   * @return string
   */
  public function getSystemViewPath()
  {
    return $this->getViewPath() . DIRECTORY_SEPARATOR . 'system';
  }

  /**
   * Return theme skins path
   * @return string
   */
  public function getSkinPath()
  {
    return $this->getViewPath() . DIRECTORY_SEPARATOR . 'skins';
  }

  /**
   * Return given view file path
   * @param mixed $controller the controller
   * @param string $viewName view name
   * @return string
   */
  public function getViewFile($controller, $viewName)
  {
    $moduleViewPath = $this->getViewPath();
    if (($module = $controller->getModule()) !== null) {
      $moduleViewPath .= '/' . $module->getId();
    }

    return $controller->resolveViewFile($viewName, $this->getViewPath() . '/' . $controller->getUniqueId(), $this->getViewPath(), $moduleViewPath);
  }

  /**
   * Return list of available theme layouts
   * @return array
   */
  public function getLayouts()
  {
    $list = array();

    $layouts = glob($this->getViewPath() . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . '*.php');
    if (!empty($layouts)) {
      foreach ($layouts as $file) {
        if (is_file($file)) {
          $list[] = basename($file, '.php');
        }
      }
    }

    return $list;
  }

  /**
   * Return given layout file path or false if it can not be found
   * @param mixed $controller the controller
   * @param string $layoutName layout name
   * @return mixed
   */
  public function getLayoutFile($controller, $layoutName)
  {
    $moduleViewPath = $this->getViewPath();
    $basePath = $this->getLayoutPath();
    $module = $controller->getModule();
    if (empty($layoutName)) {
      while ($module !== null) {
        if ($module->layout === false) {
          return false;
        }

        if (!empty($module->layout)) {
          break;
        }

        $module = $module->getParentModule();
      }

      if ($module === null) {
        $layoutName = Yii::app()->layout;
      }
      else {
        $layoutName = $module->layout;
        $moduleViewPath .= '/' . $module->getId();
      }
    }
    elseif ($module !== null) {
      $moduleViewPath .= '/' . $module->getId();
    }

    return $controller->resolveViewFile($layoutName, $moduleViewPath . '/layouts', $basePath, $moduleViewPath);
  }

  /**
   * Return theme specific stylesheet files
   * @return array
   */
  public function getCss()
  {
    $css = $this->getParam('css');

    if (!is_array($css)) {
      $css = array_filter(array($css));
    }

    return $css;
  }

  /**
   * Return theme specific javascript files
   * @return array
   */
  public function getScripts()
  {
    $scripts = $this->getParam('scripts');

    if (!is_array($scripts)) {
      $scripts = null;
    }

    return $scripts;
  }

  /**
   * Return theme image directory
   * @return string
   */
  public function getImageDir()
  {
    return $this->getParam('imageDir');
  }

  /**
   * Return theme logo
   * @return string
   */
  public function getLogoImage()
  {
    return $this->getParam('logo');
  }

  /**
   * Return theme captcha options
   * @return array
   */
  public function getCaptchaOptions()
  {
    $defaultOptions = Yii::app()->themeManager->getDefaultCaptchaOptions();
    $captchaOptions = $this->getParam('captchaOptions');

    return is_array($captchaOptions) ? CMap::mergeArray($defaultOptions, $captchaOptions) : $defaultOptions;
  }

  /**
   * Return given theme parameter
   * @param string $param parameter name
   * @return mixed
   */
  public function getParam($param)
  {
    return isset($this->_params[$param]) ? $this->_params[$param] : null;
  }

  /**
   * Generate URL to theme image file
   * @param string $image name of image file with extension
   * @return string
   */
  public function getImageUrl($image)
  {
    if (stripos($image, 'http://') === 0 || stripos($image, 'https://') === 0) {
      return $image;
    }

    $imageDir = $this->getParam('imageDir');

    return $this->getBaseUrl() . '/' . ($imageDir ? $imageDir : self::DEFAULT_IMAGE_DIR) . '/' . $image;
  }

  /**
   * Generate URL to theme style (css) file
   * @param string $css name of style file (with or without extension)
   * @return string
   */
  public function getCssUrl($css)
  {
    if (stripos($css, 'http://') === 0 || stripos($css, 'https://') === 0 || stripos($css, '//') === 0) {
      return $css;
    }

    $cssDir = $this->getParam('cssDir');

    return $this->getBaseUrl() . '/' . ($cssDir ? $cssDir : 'css') . '/' . (strpos($css, '.css') === false ? $css . '.css' : $css);
  }

  /**
   * Generate URL to theme javascript file
   * @param string $script name of javascript file (with or without extension)
   * @return string
   */
  public function getScriptUrl($script)
  {
    if (stripos($script, 'http://') === 0 || stripos($script, 'https://') === 0) {
      return $script;
    }

    $scriptDir = $this->getParam('scriptDir');

    return $this->getBaseUrl() . '/' . ($scriptDir ? $scriptDir : 'js') . '/' . (strpos($script, '.js') === false ? $script . '.js' : $script);
  }

  public function getPlaceholderFile()
  {
    return implode(DIRECTORY_SEPARATOR, array(
      $this->getBasePath(),
      $this->getImageDir(),
      $this->getParam('placeholder'),
    ));
  }

  /**
   * Returns url to the placeholder image or an empty image if the theme does not have this one
   * @param integer $width the width
   * @param integer $height the height
   * @return type
   */
  public function getPlaceholderUrl($width, $height)
  {
    $placeholder = $this->getPlaceholderFile();

    if ($placeholder) {
      $mime = VFileHelper::getMimeTypeByExtension($placeholder);

      return Yii::app()->createUrl('/image/placeholder', array(
          'hash' => Yii::app()->image->generateHash($width, $height, $mime),
          'width' => $width,
          'height' => $height,
          'filename' => basename($placeholder),
      ));
    }
    else {
      return Yii::app()->createUrl('/image/empty');
    }
  }
}
