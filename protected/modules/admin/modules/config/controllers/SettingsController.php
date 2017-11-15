<?php
/**
 * ViraCMS Application Settings Controller
 *
 * @package vira.core.core
 * @subpackage vira.core.admin
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class SettingsController extends VSystemController
{
  const CORE_UPDATE_CHECK_TIMEOUT = 604800;

  public $layout = 'config';
  public $settings;
  public $sections;
  public $section;

  public function accessRules()
  {
    return CMap::mergeArray(
        array(
        array(
          'allow',
          'actions' => array(
            'index',
            'cache',
            'check',
          ),
          'roles' => array('coreSettings'),
        ),
        ), parent::accessRules()
    );
  }

  /**
   * Initialize controller
   */
  public function init()
  {
    parent::init();
    $this->settings = new ApplicationSettings;
    $this->sections = $this->settings->getSections();
    $this->setPageTitle(Yii::t('admin.titles', 'Application Settings'));
  }

  /**
   * Application configuration form
   */
  public function actionIndex()
  {
    $r = Yii::app()->request;

    if ($r->isPostRequest) {
      $success = true;

      $this->settings->setOverride($r->getParam('config', array()));

      $mode = $r->getParam('mode', YII_MODE);
      if ($mode != YII_MODE) {
        $success = $this->setMode($mode);
      }

      if ($success && $this->exportOverrideFile()) {
        Yii::app()->user->setFlash('success', Yii::t('admin.messages', 'New configuration settings applied'));
        $this->flushConfigCache();
        $this->redirect(array('index'));
      }
    }

    $this->render('index', array(
      'model' => $this->settings,
    ));
  }

  /**
   * Application cache configuration form
   */
  public function actionCache()
  {
    $r = Yii::app()->request;
    $engines = $this->settings->getCacheEngines();
    foreach ($engines as &$engine) {
      if (isset($engine['form']) && $engine['form']) {
        $engine['form'] = @class_exists($engine['form']) ? new $engine['form'] : null;
      }
      if (!($engine['form'] && @class_exists($engine['form']->class))) {
        $engine['skip'] = true;
      }
      unset($engine);
    }

    $engine = null;

    if ($r->isPostRequest) {
      $engine = $r->getParam('engine');
      if (isset($engines[$engine]) && is_a($engines[$engine]['form'], 'BaseCacheConfig')) {
        $form = $engines[$engine]['form'];
        $form->attributes = $r->getParam(get_class($form), array());
        if ($form->validate()) {
          $this->settings->setOverrideValue('components.cache', $form->getConfiguration());

          if ($this->exportOverrideFile()) {
            Yii::app()->user->setFlash('success', Yii::t('admin.messages', 'New cache configuration has been applied.'));
            $this->flushConfigCache();
            $this->redirect(array('cache'));
          }
        }
      }
      else {
        Yii::app()->user->setFlash('error', Yii::t('admin.errors', 'Can not determine selected cache engine.'));
      }
    }

    $this->render('cache', array(
      'model' => $this->settings,
      'engines' => $engines,
      'engine' => $engine,
    ));
  }

  /**
   * Exports local override file
   * @return boolean
   */
  protected function exportOverrideFile()
  {
    $filename = Yii::getPathOfAlias('application.config.local') . '.php';
    $content = var_export($this->settings->getOverride(), true);

    try {
      $success = @file_put_contents($filename, "<?php\nreturn {$content};");
    }
    catch (Exception $e) {
      $success = false;
      $errorMessage = $e->getMessage();
    }

    if (!$success && !isset($errorMessage)) {
      $errorMessage = Yii::t('common', 'File {file} write access denied', array('{file}' => $filename));
    }

    if (!$success) {
      Yii::app()->user->setFlash('error', Yii::t('admin.errors', 'An error occurred while applying configuration settings: {error}.', array('{error}' => isset($errorMessage) ? $errorMessage : Yii::t('admin.errors', 'Unknown error.'))));
    }

    return $success;
  }

  /**
   * Set application mode (production, development)
   * @param string $mode new site mode
   * @return boolean
   */
  protected function setMode($mode)
  {
    $error = false;
    $dir = Yii::getPathOfAlias('application.config');
    $file = $dir . DIRECTORY_SEPARATOR . 'const.php';
    if ((file_exists($file) && is_writable($file)) || (is_writable($dir))) {
      $const = "<?php
  defined('YII_MODE') or define('YII_MODE','$mode');
  defined('YII_DEBUG') or define('YII_DEBUG'," . ($mode == 'development' ? 'true' : 'false') . ");
  defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL'," . ($mode == 'development' ? '3' : '0') . ");
";
      $f = fopen($file, 'w');
      if (!fwrite($f, $const)) {
        $error = true;
      }
      fflush($f);
      fclose($f);
    }
    else {
      $error = true;
    }

    if ($error) {
      Yii::app()->user->setFlash('error', Yii::t('admin.errors', 'File {file} or directory {dir} is not writeable', array('{file}' => $file, '{dir}' => $dir)));
    }

    return $error;
  }

  /**
   * Flush application' configuration cache
   */
  protected function flushConfigCache()
  {
    VCacheHelper::flushConfigCache();
    VCacheHelper::flushOpcodeCache();
  }
}
