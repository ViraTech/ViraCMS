<?php
/**
 * ViraCMS System Pages Management Controller
 *
 * @package vira.core.core
 * @subpackage vira.core.content
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class SystemController extends VSystemCrudController
{
  protected $actions = array(
    'index',
    'create',
    'config',
    'update',
    'delete',
    'ajax',
  );

  protected $accessRules = array(
    '*' => array('siteSystemPage'),
  );

  public function actionCreate()
  {
    $model = $this->getModel('create');
    $this->processAjaxRequest('create', $model);
    $this->updateModel($model);
    $this->setPageTitle($this->getTitle('create', array('model' => $model)));
    $this->renderUpdate($model, array(), 'config');
  }

  public function actionConfig($id, $copied = false)
  {
    $model = $this->loadModel($id, $copied ? 'copied' : 'update');
    $this->updateModel($model);
    $this->setPageTitle($this->getTitle('config', array('model' => $model)));
    $this->renderUpdate($model, array(), 'config');
  }

  public function actionUpdate($id)
  {
    $this->layout = 'no-footer';
    parent::actionUpdate($id);
  }

  public function actionAjax()
  {
    $r = Yii::app()->request;

    if (($site = $r->getParam('site')) !== null) {
      echo CJSON::encode(array(
        'layouts' => CHtml::listData(VSiteLayout::model()->from($site)->findAll(), 'id', 'title')
      ));
      Yii::app()->end();
    }
  }

  public function getTitle($view, $params = array())
  {
    extract($params);

    switch ($view) {
      case 'index':
        return Yii::t('admin.content.titles', 'System Pages');

      case 'create':
        return Yii::t('admin.content.titles', 'New System Page');

      case 'update':
        return Yii::t('admin.content.titles', 'Update System Page "{title}"', array('{title}' => $model->title));

      case 'delete':
        return Yii::t('admin.content.titles', 'Delete System Page "{title}"', array('{title}' => $model->title));
    }

    return '';
  }

  protected function getActionSuccessMessage($action, $params = array())
  {
    extract($params);
    switch ($action) {
      case 'create':
        return Yii::t('admin.content.messages', 'System page "{title}" has been successfully created', array('{title}' => $model->title));

      case 'update':
        return Yii::t('admin.content.messages', 'System page "{title}" has been successfully updated', array('{title}' => $model->title));

      case 'delete':
        return Yii::t('admin.content.messages', 'System page "{title}" has been successfully removed', array('{title}' => $model->title));
    }
    parent::getActionSuccessMessage($action, $params);
  }

  public function getActionConfirmMessage($action, $params = array())
  {
    extract($params);
    switch ($action) {
      case 'delete':
        return Yii::t('admin.content.messages', 'Are you sure to delete system page "{title}"?', array('{title}' => $model->title));
    }

    return parent::getActionConfirmMessage($action, $params);
  }

  public function getActionButtonConfig($action, $params = array())
  {
    extract($params);
    switch ($action) {
      case 'delete':
        return array(
          'type' => 'danger',
          'buttonType' => 'submit',
          'label' => Yii::t('common', 'Delete'),
          'icon' => 'icon-trash',
          'htmlOptions' => array(
            'name' => 'delete',
            'data-loading-text' => '<i class="icon-spinner icon-spin"></i> ' . Yii::t('common', 'Removing...'),
          ),
        );
    }

    return parent::getActionButtonConfig($action, $params);
  }

  public function getErrorMessage($params = array())
  {
    extract($params);
    return Yii::t('common', 'An error occurred while processing: {error}.', array('{error}' => $model->getFirstError()));
  }

  public function getMassActionErrorMessage($params = array())
  {
    extract($params);
    return Yii::t('common', 'Please select something!');
  }

  public function getNotFoundErrorMessage($params = array())
  {
    return Yii::t('admin.content.errors', 'System page not found');
  }

  protected function preprocessData(&$data)
  {
    $data['currentLanguageID'] = Yii::app()->getLanguage();
    $data['languages'] = VLanguageHelper::getLanguages();
  }

  public function getModelTitle($model)
  {
    return $model->title;
  }

  public function getModel($scenario = 'search')
  {
    return new VSystemPage($scenario);
  }

  public function getPlainModel($scenario)
  {
    return VSystemPage::model();
  }

  protected function beforeUpdateModel(&$model, $action = null)
  {
    if (parent::beforeUpdateModel($model, $action)) {
      if (in_array($action ? $action : $this->action->id, array('create', 'config'))) {
        $model->populateL10nModels(Yii::app()->request);

        return $model->validate() && $model->validateL10nModels();
      }

      return true;
    }

    return false;
  }

  protected function afterUpdateModel($model, $action = null)
  {
    if (in_array($action ? $action : $this->action->id, array('create', 'config'))) {
      $model->saveL10nModels();
    }
    parent::afterUpdateModel($model, $action);
  }

  public function getMcvList()
  {
    $list = array();

    foreach (VSystemView::model()->findAll() as $view) {
      $list[implode(',', array(
          $view->module,
          $view->controller,
          $view->view,
        ))] = $view->translate ? Yii::t($view->translate, $view->title) : $view->title;
    }

    return $list;
  }
}
