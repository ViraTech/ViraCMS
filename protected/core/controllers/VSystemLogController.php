<?php
/**
 * ViraCMS System (backend) Log Viewer Controller
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
abstract class VSystemLogController extends VSystemController
{
  const CSV_ENCODING = 'cp1251';

  /**
   * @var string default layout
   */
  public $layout = 'inner';

  /**
   * @var array access rules
   */
  protected $accessRules;

  /**
   * @var array available actions in this controller
   */
  protected $actions = array(
    'index',
    'clear',
    'download',
  );

  /**
   * Default access rules
   * @return array
   */
  public function accessRules()
  {
    $rules = array();

    foreach ($this->actions as $action) {
      if (isset($this->accessRules[$action])) {
        $rules[] = array(
          'allow',
          'actions' => array($action),
          'roles' => $this->accessRules[$action],
        );
      }
    }

    $rules[] = array(
      'allow',
      'actions' => $this->actions,
      'roles' => isset($this->accessRules['*']) ? $this->accessRules['*'] : array(VAuthManager::ROLE_SUPERADMIN),
    );

    return array_merge($rules, parent::accessRules());
  }

  /**
   * Index page
   */
  public function actionIndex()
  {
    $r = Yii::app()->request;

    $model = $this->getModel('search');
    $model->unsetAttributes();
    $model->setAttributes($r->getParam(get_class($model), array()), true);

    if ($r->isAjaxRequest) {
      if ($r->getParam('ajax') == $model->getGridID()) {
        $this->renderPartial('grid', array(
          'model' => $model,
        ));
      }
      Yii::app()->end();
    }

    $this->pageTitle = $this->getTitle('index', array('model' => $model));
    $this->render('index', array(
      'model' => $model,
      'info' => $this->getModelInfo(),
    ));
  }

  /**
   * Clear log
   */
  public function actionClear()
  {
    $r = Yii::app()->request;
    $model = $this->getPlainModel();

    if ($r->isPostRequest) {
      if ($this->beforeClear($model)) {
        $model->deleteAll();
        $this->afterClear($model);
        Yii::app()->user->setFlash('success', $this->getClearSuccessMessage(array('model' => $model)));
      }
      else {
        Yii::app()->user->setFlash('error', $this->getClearErrorMessage(array('model' => $model)));
      }

      $this->redirect($this->createUrl('index'));
    }

    $this->pageTitle = $this->getTitle('clear');
    $this->render('clear');
  }

  /**
   * Download log contents
   */
  public function actionDownload()
  {
    $userNameCache = array();
    $model = $this->getPlainModel();
    $tmpFile = $this->prepareDownloadFile();
    if (!$model->count() || $tmpFile == null) {
      Yii::app()->user->setFlash('error', Yii::t('common', 'Cannot create log file while log is empty.'));
      $this->redirect(array('index'));
    }

    VFileHelper::sendFile($tmpFile, get_class($model) . '.csv');
  }

  /**
   * Run before log to be cleared
   * @param mixed $model model
   * @return boolean
   */
  protected function beforeClear(&$model)
  {
    return true;
  }

  /**
   * Run after log has cleared
   * @param mixed $model model
   */
  protected function afterClear($model)
  {
    
  }

  /**
   * Return model info, i.e. number of entries and timestamp boundaries
   * @return array
   */
  public function getModelInfo()
  {
    $model = $this->getPlainModel();
    return array(
      'entriesQty' => $model->count(),
      'minDatetime' => Yii::app()->db->createCommand()->select('MIN(time)')->from($model->tableName())->queryScalar(),
      'maxDatetime' => Yii::app()->db->createCommand()->select('MAX(time)')->from($model->tableName())->queryScalar(),
    );
  }

  /**
   * Create download file and prepare it to be filled with data
   * @return resource
   */
  protected function prepareDownloadFile()
  {
    return null;
  }

  /**
   * Return title of current section
   * @param string $view view name
   * @param array $params params to be extracted into function
   * @return string
   */
  protected function getTitle($view, $params = array())
  {
    return '';
  }

  /**
   * Return success message for log clearing
   * @param array $params params to be extracted into function
   * @return string
   */
  protected function getClearSuccessMessage($params = array())
  {
    extract($params);
    return Yii::t('admin.messages', 'Log "{class}" has been cleared successfully', array('{class}' => get_class($model)));
  }

  /**
   * Return error message occurred while clearing the log
   * @param array $params params to be extracted into function
   * @return string
   */
  protected function getClearErrorMessage($params = array())
  {
    extract($params);
    return Yii::t('common', 'An error occurred while cleanup log "{class}".', array('{class}' => get_class($model)));
  }

  /**
   * Must return new model object with scenario set
   * @param string $scenario scenario name
   */
  abstract function getModel($scenario = 'search');

  /**
   * Must return static model object with scenario set
   * @param string $scenario scenario name
   */
  abstract function getPlainModel();
}
