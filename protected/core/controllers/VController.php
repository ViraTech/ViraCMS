<?php
/**
 * ViraCMS Base Controller
 * Based On Yii Framework CController Class
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VController extends CController
{
  const THEME_OVERRIDE_COOKIE_NAME = 'theme';

  /**
   * @var CClientScript Client script shortcut
   */
  public $cs;

  /**
   * @var string URL of public part
   */
  public $baseUrl;

  /**
   * @var string URL of current theme
   */
  public $themeUrl;

  /**
   * @var array Breadcrumbs storage
   */
  public $breadcrumbs = array();

  /**
   * @var array breadcrumbs
   */
  protected $_breadcrumbs = array();

  /**
   * Init controller
   */
  public function init()
  {
    Yii::app()->eventManager->attach($this);
    parent::init();
    $this->cs = Yii::app()->getClientScript();
    // workaround over CFormatter boolean labels that hasn't locale implementation
    Yii::app()->format->booleanFormat = array(
      1 => Yii::t('common', 'Yes'),
      0 => Yii::t('common', 'No'),
    );
    $this->setPageTitle('');
  }

  /**
   * Before controller action event
   * @param CAction $action Action
   * @return boolean
   */
  protected function beforeAction($action)
  {
    if (parent::beforeAction($action)) {
      $this->onBeforeAction(new CEvent($this, array(
        'action' => $action,
      )));

      $this->setTheme();

      return true;
    }

    return false;
  }

  /**
   * Set current theme
   */
  protected function setTheme()
  {
    if (!(Yii::app()->theme instanceof VTheme)) {
      if ($this instanceof VSystemController) {
        $themeName = Yii::app()->themeManager->backendTheme;
        $themeType = VThemeManager::THEME_BACKEND;
      }
      elseif ($this instanceof VPublicController) {
        $cookies = Yii::app()->getRequest()->cookies;
        if (!empty($cookies[self::THEME_OVERRIDE_COOKIE_NAME])) {
          $themeName = $cookies[self::THEME_OVERRIDE_COOKIE_NAME];
          if (!Yii::app()->themeManager->getIsFrontendThemeExist($themeName)) {
            $themeName = null;
          }
        }

        if (empty($themeName) && Yii::app()->site && Yii::app()->site->theme) {
          $themeName = Yii::app()->site->theme;
        }
        else {
          $themeName = Yii::app()->themeManager->frontendTheme;
        }

        $themeType = VThemeManager::THEME_FRONTEND;
      }

      if (!empty($themeName) && !empty($themeType)) {
        Yii::app()->setTheme($themeName, $themeType);
        $this->themeUrl = Yii::app()->getTheme()->getBaseUrl();
        $this->baseUrl = Yii::app()->getRequest()->getBaseUrl();
      }
    }
  }

  /**
   * After controller action event
   * @param CAction $action
   */
  protected function afterAction($action)
  {
    // clear original route and params to session
    Yii::app()->user->setState('Vira.OriginModel', null);
    Yii::app()->user->setState('Vira.OriginRoute', null);
    Yii::app()->user->setState('Vira.OriginParams', null);

    $this->onAfterAction(new CEvent($this, array('action' => $action)));
  }

  /**
   * Generate URL to core style (css) file
   * @param string $css name of style file (with or without extension)
   * @return string
   */
  public function coreCssUrl($css)
  {
    return $this->baseUrl . '/css/' . (strpos($css, '.css') === false ? $css . '.css' : $css);
  }

  /**
   * Generate URL to core javascript file
   * @param string $script name of javascript file (with or without extension)
   * @return string
   */
  public function coreScriptUrl($script)
  {
    return $this->baseUrl . '/js/' . (strpos($script, '.js') === false ? $script . '.js' : $script);
  }

  /**
   * Generate URL to core image file
   * @param string $image name of image file with extension
   * @return string
   */
  public function coreImageUrl($image)
  {
    return $this->baseUrl . '/img/' . $image;
  }

  /**
   * Before rendering event
   * @param string $view view file name
   * @return boolean is application can continue?
   */
  protected function beforeRender($view)
  {
    if (parent::beforeRender($view)) {
      $this->registerThemeFiles();
      $this->onBeforeRender(new CEvent($this, array(
        'view' => $view,
      )));
      return true;
    }

    return false;
  }

  /**
   * After rendering event
   * @param string $view view file name
   * @param string $output rendered output
   */
  public function afterRender($view, &$output)
  {
    $this->onAfterRender(new CEvent($this, array(
      'view' => $view,
      'output' => &$output,
    )));
    parent::afterRender($view, $output);
  }

  /**
   * Before action event
   * @param CEvent $event
   */
  public function onBeforeAction($event)
  {
    $this->raiseEvent('onBeforeAction', $event);
  }

  /**
   * Before render event
   * @param CEvent $event
   */
  public function onBeforeRender($event)
  {
    $this->raiseEvent('onBeforeRender', $event);
  }

  /**
   * After render event
   * @param CEvent $event
   */
  public function onAfterRender($event)
  {
    $this->raiseEvent('onAfterRender', $event);
  }

  /**
   * After action event
   * @param CEvent $event
   */
  public function onAfterAction($event)
  {
    $this->raiseEvent('onAfterAction', $event);
  }

  /**
   * Processes the request using another controller action.
   * @see CController::forward
   * @param string $route
   * @param boolean $exit
   * @param mixed $model original page model
   */
  public function forward($route, $exit = true, $params = array(), $model = null)
  {
    // save original route and params to the session
    Yii::app()->user->setState('Vira.OriginModel', $model);
    Yii::app()->user->setState('Vira.OriginRoute', $this->route);
    Yii::app()->user->setState('Vira.OriginParams', $this->actionParams);

    $_GET = CMap::mergeArray($_GET, $params);

    parent::forward($route, $exit);
  }

  /**
   * Origin model (after forward)
   * @return mixed model
   */
  public function getOriginModel()
  {
    return Yii::app()->user->getState('Vira.OriginModel');
  }

  /**
   * Origin route (after forward)
   * @return string
   */
  public function getOriginRoute()
  {
    return Yii::app()->user->getState('Vira.OriginRoute');
  }

  /**
   * Origin params (after forward)
   * @return array
   */
  public function getOriginParams()
  {
    return Yii::app()->user->getState('Vira.OriginParams');
  }

  /**
   * Register theme stylesheets, scripts etc.
   */
  public function registerThemeFiles()
  {
    $theme = Yii::app()->getTheme();

    if ($theme instanceof VTheme) {
      if (Yii::app()->hasComponent('bootstrap')) {
        if ($theme->getParam('bootstrapCss')) {
          Yii::app()->bootstrap->registerCoreCss();
        }

        if ($theme->getParam('responsiveCss')) {
          Yii::app()->bootstrap->registerResponsiveCss();
        }

        if ($theme->getParam('yiiCss')) {
          Yii::app()->bootstrap->registerYiiCss();
        }

        if ($theme->getParam('bootstrapJs')) {
          Yii::app()->bootstrap->registerCoreScripts();
        }
      }

      $css = $theme->getCss();
      if (is_array($css)) {
        foreach ($css as $file) {
          $this->cs->registerThemeCssFile($theme->getCssUrl($file));
        }
      }

      $scripts = $theme->getScripts();

      if (is_array($scripts)) {
        foreach ($scripts as $position => $files) {
          foreach ($files as $file) {
            $this->cs->registerScriptFile($theme->getScriptUrl($file), Yii::app()->themeManager->getPosition($position));
          }
        }
      }
    }
  }

  /**
   * Set page breadcrumbs
   * @param array $data breadcrumbs as array where key is url and value is title
   */
  public function setBreadcrumbs($data)
  {
    $this->_breadcrumbs = CMap::mergeArray($this->getBreadcrumbsHome(), is_array($data) ? $data : array($data));
  }

  /**
   * Return breadcrumbs for current page
   * @return array
   */
  public function getBreadcrumbs()
  {
    return empty($this->_breadcrumbs) ? $this->getBreadcrumbsHome() : $this->_breadcrumbs;
  }

  /**
   * Return default breadcrumbs (home page)
   * @return array
   */
  protected function getBreadcrumbsHome()
  {
    return array($this->createUrl('/site/index') => Yii::t('common', 'Home'));
  }

  /**
   * Set search engine optimization keywords
   * @param mixed $keywords the keywords
   */
  public function setSeoKeywords($keywords)
  {
    if (is_array($keywords)) {
      $keywords = implode(',', $keywords);
    }

    $this->cs->registerMetaTag($keywords, 'keywords', null);
  }

  /**
   * Set search engine optimization page description
   * @param string $description the description
   */
  public function setSeoDescription($description)
  {
    $this->cs->registerMetaTag($description, 'description', null);
  }

  /**
   * Looks for the layout view script based on the layout name.
   * @param mixed $layoutName layout name
   * @return string the view file for the layout. False if the view file cannot be found
   */
  public function getLayoutFile($layoutName)
  {
    if ($layoutName === false) {
      return false;
    }

    if (($theme = Yii::app()->getTheme()) !== null) {
      if (($layoutFile = $theme->getLayoutFile($this, $layoutName)) !== false) {
        return $layoutFile;
      }
    }

    if (empty($layoutName)) {
      $module = $this->getModule();

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
        $module = Yii::app();
      }

      $layoutName = $module->layout;
    }

    $module = $this->getModule();
    do {
      if ($module === null) {
        $module = Yii::app();
      }
      if (file_exists($module->getLayoutPath())) {
        break;
      }
    } while (($module = $module->getParentModule()) !== null);

    return $this->resolveViewFile(
        $layoutName, $module ? $module->getLayoutPath() : null, Yii::app()->getLayoutPath(), $module ? $module->getViewPath() : null
    );
  }

  /**
   * Finds a view file based on its name. To find out more info look CController.
   * @param string $viewName the view name
   * @param string $viewPath the directory that is used to search for a relative view name
   * @param string $basePath the directory that is used to search for an absolute view name under the application
   * @param string $moduleViewPath the directory that is used to search for an absolute view name under the current module.
   * If this is not set, the application base view path will be used.
   * @return mixed the view file path. False if the view file does not exist.
   */
  public function resolveViewFile($viewName, $viewPath, $basePath, $moduleViewPath = null)
  {
    if (empty($viewName)) {
      return false;
    }

    $defaultExtension = '.php';

    if (($renderer = Yii::app()->getViewRenderer()) !== null) {
      $extension = $renderer->fileExtension;
    }
    else {
      $extension = $defaultExtension;
    }

    if (strpos($viewName, '.')) {
      $viewFilePath = Yii::getPathOfAlias($viewName) . $extension;
    }
    else {
      $viewFilePath = $this->findViewFile($viewName, array(
        $viewPath,
        $moduleViewPath,
        $basePath,
        ), $extension, $defaultExtension);
    }

    return is_file($viewFilePath) ? Yii::app()->findLocalizedFile($viewFilePath) : false;
  }

  /**
   * Find view file
   * @param string $viewName the view name
   * @param array $where the directories list where to search the file
   * @param string $extension the file extension
   * @param string $defaultExtension the default extension (php)
   * @return mixed path to the file if any found, false otherwise
   */
  protected function findViewFile($viewName, $where, $extension, $defaultExtension)
  {
    $viewFile = trim($viewName, ' /\\');
    $searchFiles = array();

    foreach ($where as $path) {
      $searchFiles[] = $path . DIRECTORY_SEPARATOR . $viewFile . $extension;

      if ($extension != $defaultExtension) {
        $searchFiles[] = $path . DIRECTORY_SEPARATOR . $viewFile . $defaultExtension;
      }
    }

    foreach ($searchFiles as $searchFile) {
      if (is_file($searchFile)) {
        return $searchFile;
      }
    }

    return false;
  }

  /**
   * Begins the rendering of content that is to be decorated by the specified view.
   * @param mixed $view the name of the view that will be used to decorate the content
   * @param array $data the variables (name => value) to be extracted and made available in the decorative view.
   */
  public function beginContent($view = null, $data = array())
  {
    $this->beginWidget('application.widgets.core.VContentDecorator', array(
      'view' => $view,
      'data' => $data,
    ));
  }

  /**
   * Ends the rendering of content.
   * @see beginContent
   */
  public function endContent()
  {
    $this->endWidget('application.widgets.core.VContentDecorator');
  }

  /**
   * Checks if current page is "home" page
   * @return boolean
   */
  public function getIsHomePage()
  {
    return $this->route == 'site/index';
  }
}
