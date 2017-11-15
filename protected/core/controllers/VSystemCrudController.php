<?php
/**
 * ViraCMS System (backend) CRUD Controller
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
abstract class VSystemCrudController extends VSystemController
{
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
    'view',
    'create',
    'update',
    'delete',
    'enable',
    'disable',
    'approve',
    'discard',
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

    $this->processAjaxRequest('index', $model);
    if ($r->isPostRequest && $this->getViewFile('mass')) {
      $this->processMassAction($model);
    }

    $this->setPageTitle($this->getTitle('index', array('model' => $model)));
    $this->renderView('index', array(
      'model' => $model,
    ));
  }

  /**
   * View model
   * @param mixed $id model primary key
   */
  public function actionView($id)
  {
    $model = $this->loadModel($id);
    $this->processAjaxRequest('view', $model);
    $this->setPageTitle($this->getTitle('view', array('model' => $model)));
    $this->renderView('view', array(
      'model' => $model,
    ));
  }

  /**
   * Create model
   */
  public function actionCreate()
  {
    $model = $this->getModel('create');
    $this->processAjaxRequest('create', $model);
    $this->updateModel($model);
    $this->setPageTitle($this->getTitle('create', array('model' => $model)));
    $this->renderUpdate($model);
  }

  /**
   * Update model
   * @param mixed $id model primary key
   */
  public function actionUpdate($id)
  {
    $model = $this->loadModel($id);
    $this->processAjaxRequest('update', $model);
    $this->updateModel($model);
    $this->setPageTitle($this->getTitle('update', array('model' => $model)));
    $this->renderUpdate($model);
  }

  /**
   * Disable model
   * @param mixed $id model primary key
   */
  public function actionDisable($id)
  {
    $this->changeStatus($id, 'disable');
  }

  /**
   * Enabled model
   * @param mixed $id model primary key
   */
  public function actionEnable($id)
  {
    $this->changeStatus($id, 'enable');
  }

  /**
   * Approve model
   * @param mixed $id model primary key
   */
  public function actionApprove($id)
  {
    $this->changeStatus($id, 'approve');
  }

  /**
   * Discard model
   * @param mixed $id model primary key
   */
  public function actionDiscard($id)
  {
    $this->changeStatus($id, 'discard');
  }

  /**
   * Delete model
   * @param mixed $id model primary key
   */
  public function actionDelete($id)
  {
    $model = $this->loadModel($id);

    if (Yii::app()->request->isPostRequest) {
      if ($this->beforeUpdateModel($model, $this->action->id)) {
        if ($model->delete()) {
          $this->afterUpdateModel($model, $this->action->id);
          Yii::app()->user->setFlash('success', $this->getActionSuccessMessage('delete', array('model' => $model)));
          $this->redirect($this->createUrl('index'));
        }
      }
    }

    $this->setPageTitle($this->getTitle('delete', array('model' => $model)));
    $this->renderView('delete', array(
      'model' => $model,
    ));
  }

  /**
   * Change model status
   * @param mixed $id model primary key
   * @param string $action action (enable, disable, approve or decline)
   */
  protected function changeStatus($id, $action)
  {
    $r = Yii::app()->request;
    $model = $this->loadModel($id);

    if ($r->isPostRequest) {
      if ($this->beforeUpdateModel($model, $action)) {
        switch ($action) {
          case 'enable':
            $success = $model->enable();
            break;
          case 'disable':
            $success = $model->disable();
            break;
          case 'approve':
            $success = $model->approve();
            break;
          case 'discard':
            $success = $model->discard();
            break;
        }
      }

      if ($success) {
        $this->afterUpdateModel($model, $action);
        Yii::app()->user->setFlash('success', $this->getActionSuccessMessage($action, array('model' => $model)));
        $this->redirect($this->createUrl('index'));
      }
      else {
        Yii::app()->user->setFlash('error', $this->getErrorMessage(array('model' => $model)));
      }
    }

    if ($this->getViewFile($action)) {
      $this->setPageTitle($this->getTitle($action, array('model' => $model)));
      $this->renderView($action, array(
        'model' => $model,
      ));
    }
    else {
      $this->throwInvalidUsage();
    }
  }

  /**
   * Render update view
   * @param mixed $model model
   * @param array $params view params
   * @param string $viewName view name
   */
  protected function renderUpdate($model, $params = array(), $viewName = 'update')
  {
    $params['model'] = $model;
    $this->renderView($viewName, $params);
  }

  /**
   * Update model
   * @param mixed $model model
   */
  protected function updateModel($model)
  {
    $r = Yii::app()->request;
    $attributes = $r->getParam(get_class($model), array());
    $model->setAttributes($attributes, true);

    if ($r->isPostRequest) {
      $isNewRecord = $model->isNewRecord;

      if ($this->beforeUpdateModel($model, $this->action->id)) {
        if ($model->save()) {
          $this->afterUpdateModel($model, $this->action->id);
          Yii::app()->user->setFlash('success', $this->getActionSuccessMessage($isNewRecord ? 'create' : 'update', array('model' => $model)));
          $return = $r->getParam('return', false);
          $this->redirect($return ? ($return == 1 ? $this->createUrl('update', array('id' => $model->id)) : urldecode($return)) : $this->createUrl('index'));
        }
      }
    }
  }

  /**
   * Run after model has updated
   * @param mixed $model model
   * @param string $action action
   */
  protected function afterUpdateModel($model, $action = null)
  {
    
  }

  /**
   * Run before update model
   * @param mixed $model model
   * @param string $action action
   * @return boolean
   */
  protected function beforeUpdateModel(&$model, $action = null)
  {
    return true;
  }

  /**
   * Handle asyncronous requests
   * @param string $action action name
   * @param mixed $model model
   */
  protected function processAjaxRequest($action, $model)
  {
    $r = Yii::app()->request;
    if ($r->isAjaxRequest) {
      if ($action == 'index' && $r->getParam('ajax') == $model->getGridID()) {
        $this->renderPartial('grid', array(
          'model' => $model,
        ));
      }
      Yii::app()->end();
    }
  }

  /**
   * Handle mass actions
   * @param mixed $model model
   */
  protected function processMassAction($model)
  {
    $r = Yii::app()->request;
    $ids = $r->getParam($model->getGridID() . '_c0');
    $action = $r->getParam('action');
    $list = $r->getParam('list');

    if ($ids || ($action && $list)) {
      $entries = $this->loadModel($ids ? $ids : explode(';', $list));

      if (empty($action)) {
        foreach ($this->availableMassActions() as $action) {
          if (isset($_POST[$action])) {
            break;
          }
        }
      }

      $i = 0;
      $selected = array();
      $attributes = array();
      foreach ($entries as $entry) {
        if ($this->isMassActionAllowed($entry, $action)) {
          $selected[$i] = $this->getModelTitle($entry);
          $attributes[] = array('name' => $i, 'label' => 'ID ' . $entry->id);
          $i++;
        }
      }

      if ($selected == array()) {
        Yii::app()->user->setFlash('error', Yii::t('admin.errors', 'No entries allowed to remove found.'));
        $this->redirect(array('index'));
      }

      if ($ids) {
        if (empty($selected)) {
          Yii::app()->user->setFlash('error', $this->getMassActionNotFoundMessage($action, array('model' => $model)));
          $this->redirect(array('index'));
        }
      }
      elseif ($action && $list) {
        foreach ($entries as $entry) {
          $success = $entry !== null;

          if ($success) {
            $success = $this->beforeUpdateModel($entry, $action);
          }

          if ($success) {
            $success = $this->doMassAction($entry, $action);
          }

          if ($success) {
            $this->afterUpdateModel($entry, $action);
          }

          if ($entry->hasErrors()) {
            foreach ($entry->getErrors() as $attribute => $errors) {
              if (!$model->hasErrors($attribute)) {
                $model->addErrors(array($attribute => $errors));
              }
            }
          }
        }

        if ($success) {
          Yii::app()->user->setFlash('success', $this->getMassActionSuccessMessage($r->getParam('action')));
          $this->redirect(array('index'));
        }
      }

      $this->setPageTitle($this->getTitle('mass', array('model' => $model)));
      $this->renderView('mass', array(
        'model' => $model,
        'action' => $action,
        'selected' => $selected,
        'attributes' => $attributes,
        'confirmation' => $this->getMassActionConfirmMessage($action),
        'button' => $this->getActionButtonConfig($action),
        'list' => $ids ? implode(';', $ids) : $list,
      ));

      Yii::app()->end();
    }
    else {
      Yii::app()->user->setFlash('error', $this->getMassActionErrorMessage(array('action' => $r->getParam('action'))));
      $this->redirect(array('index'));
    }
  }

  /**
   * Return list of available mass actions
   * @return array
   */
  protected function availableMassActions()
  {
    return array(
      'enable',
      'disable',
      'approve',
      'discard',
      'delete',
    );
  }

  /**
   * Process mass action
   * @param mixed $model model
   * @param string $action action name
   * @param string $scenario scenario name
   * @return boolean
   */
  protected function doMassAction($model, $action, $scenario = null)
  {
    if ($scenario == null) {
      $scenario = $action;
    }

    switch ($action) {
      case 'enable':
        return $model->enable($scenario);
        break;
      case 'disable':
        return $model->disable($scenario);
        break;
      case 'approve':
        return $model->approve($scenario);
        break;
      case 'discard':
        return $model->discard($scenario);
        break;
      case 'delete':
        return $model->delete($scenario);
        break;
    }

    return false;
  }

  /**
   * Check if mass action is allowed with selected model
   * @param mixed $model model
   * @param string $action action name
   * @return boolean
   */
  protected function isMassActionAllowed($model, $action)
  {
    return true;
  }

  /**
   * Render selected view
   * @param string $view view name
   * @param array $params view parameters
   */
  protected function renderView($view, $params)
  {
    $this->preprocessData($params);
    $this->render($view, $params);
  }

  /**
   * Preprocess view data before view rendering
   * @param array $data view data
   */
  protected function preprocessData(&$data)
  {
    
  }

  /**
   * Invalid usage error
   * @throws CHttpException
   */
  protected function throwInvalidUsage()
  {
    throw new CHttpException(400, Yii::t('common', 'Invalid usage.'));
  }

  /**
   * Load model or models
   * @param mixed $pk primary key or array of PKs
   * @param string $scenario scenario name
   * @return mixed
   * @throws CHttpException
   */
  public function loadModel($pk, $scenario = 'update')
  {
    $model = $this->getPlainModel($scenario);

    $primaryKey = $model->getPrimaryKey();
    if (is_array($primaryKey)) {
      if (is_array($pk)) {
        $pks = array();
        foreach ($pk as $value) {
          $pks[] = array_combine(array_keys($primaryKey), explode(',', $value));
        }
        $models = $model->findAllByPk($pks);
      }
      else {
        $models = $model->findByPk(array_combine(array_keys($primaryKey), explode(',', $pk)));
      }
    }
    else {
      if (is_array($pk)) {
        $models = $model->findAllByPk($pk);
      }
      else {
        $models = $model->findByPk($pk);
      }
    }

    if ($models == null) {
      throw new CHttpException(404, $this->getNotFoundErrorMessage());
    }

    if (!is_array($models)) {
      $models->setScenario($scenario);
    }
    else {
      foreach ($models as $model) {
        $model->setScenario($scenario);
      }
    }

    $this->afterFindModel($models);

    return $models;
  }

  /**
   * Run after model or models has loaded
   * @param mixed $models model or array of models
   */
  protected function afterFindModel(&$models)
  {
    
  }

  /**
   * Return action buttons configuration
   * @param string $action action name
   * @param array $params parameters to be extracted into function
   * @return array
   */
  public function getActionButtonConfig($action, $params = array())
  {
    extract($params);
    return array(
      'type' => 'primary',
      'buttonType' => 'submit',
      'label' => Yii::t('common', mb_convert_case($action, MB_CASE_TITLE, Yii::app()->charset)),
      'htmlOptions' => array(
        'name' => $action,
      ),
    );
  }

  /**
   * Return model textual representation (title or name)
   * @param mixed $model model
   * @return string
   */
  protected function getModelTitle($model)
  {
    return $model->getAttribute('title');
  }

  /**
   * Return section title
   * @param string $view view name
   * @param array $params parameters to be extracted into function
   * @return string
   */
  protected function getTitle($view, $params = array())
  {
    extract($params);
    switch (strtolower($view)) {
      case 'index':
        return Yii::t('admin.titles', 'List of models "{class}"', array('{class}' => get_class($model)));
      case 'view':
        return Yii::t('admin.titles', 'View model "{class}" ID {id}', array('{class}' => get_class($model), '{id}' => $model->id));
      case 'create':
        return Yii::t('admin.titles', 'New model "{class}" ID {id}', array('{class}' => get_class($model), '{id}' => $model->id));
      case 'update':
        return Yii::t('admin.titles', 'Update model "{class}" ID {id}', array('{class}' => get_class($model), '{id}' => $model->id));
      case 'disable':
        return Yii::t('admin.titles', 'Disable model "{class}" ID {id}', array('{class}' => get_class($model), '{id}' => $model->id));
      case 'enable':
        return Yii::t('admin.titles', 'Enable model "{class}" ID {id}', array('{class}' => get_class($model), '{id}' => $model->id));
      case 'approve':
        return Yii::t('admin.titles', 'Approve model "{class}" ID {id}', array('{class}' => get_class($model), '{id}' => $model->id));
      case 'discard':
        return Yii::t('admin.titles', 'Discard model "{class}" ID {id}', array('{class}' => get_class($model), '{id}' => $model->id));
      default:
        return Yii::t('admin.titles', '{action} model "{class}" ID {id}', array('{action}' => mb_convert_case($view, MB_CASE_TITLE, Yii::app()->charset), '{class}' => get_class($model), '{id}' => $model->id));
    }
  }

  /**
   * Return action' success messages
   * @param string $action action name
   * @param array $params parameters to be extracted into function
   * @return string
   */
  protected function getActionSuccessMessage($action, $params = array())
  {
    extract($params);
    switch (strtolower($action)) {
      case 'create':
        return Yii::t('admin.messages', 'Model "{class}" ID {id} has been successfully updated', array('{class}' => get_class($model), '{id}' => $model->id));
      case 'update':
        return Yii::t('admin.messages', 'Model "{class}" ID {id} has been successfully created', array('{class}' => get_class($model), '{id}' => $model->id));
      case 'disable':
        return Yii::t('admin.messages', 'Model "{class}" ID {id} has been successfully disabled', array('{class}' => get_class($model), '{id}' => $model->id));
      case 'enable':
        return Yii::t('admin.messages', 'Model "{class}" ID {id} has been successfully enabled', array('{class}' => get_class($model), '{id}' => $model->id));
      case 'approve':
        return Yii::t('admin.messages', 'Model "{class}" ID {id} has been successfully approved', array('{class}' => get_class($model), '{id}' => $model->id));
      case 'discard':
        return Yii::t('admin.messages', 'Model "{class}" ID {id} has been successfully discarded', array('{class}' => get_class($model), '{id}' => $model->id));
      default:
        return Yii::t('admin.messages', 'Requested action "{action}" with model "{class}" ID {id} has been successfully completed', array('{action}' => $action, '{class}' => get_class($model), '{id}' => $model->id));
    }
  }

  /**
   * Return confirmation message for mass actions
   * @param string $action action name
   * @param array $params parameters to be extracted into function
   * @return string
   */
  public function getMassActionConfirmMessage($action, $params = array())
  {
    extract($params);
    return Yii::t('admin.default.titles', 'Are you sure to {action} selected entries?', array('{action}' => Yii::t('common', $action)));
  }

  /**
   * Return not found message for mass actions
   * @param string $action action name
   * @param array $params parameters to be extracted into function
   * @return string
   */
  protected function getMassActionNotFoundMessage($action, $params = array())
  {
    extract($params);
    return Yii::t('admin.errors', 'Can not find any objects suitable for action "{action}".', array('{action}' => Yii::t('common', $action)));
  }

  /**
   * Return confirmation message for single actions
   * @param string $action action name
   * @param array $params parameters to be extracted into function
   * @return string
   */
  public function getActionConfirmMessage($action, $params = array())
  {
    extract($params);
    return Yii::t('admin.messages', 'Are you sure to "{action}" model "{class}" ID {id}?', array('{action}' => strtolower($action), '{class}' => get_class($model), '{id}' => $model->id));
  }

  /**
   * Return success message for mass actions
   * @param string $action action name
   * @param array $params parameters to be extracted into function
   * @return string
   */
  protected function getMassActionSuccessMessage($action, $params = array())
  {
    return Yii::t('admin.messages', 'Operation successfully completed');
  }

  /**
   * Return error message for mass actions
   * @param array $params parameters to be extracted into function
   * @return string
   */
  protected function getMassActionErrorMessage($params = array())
  {
    return Yii::t('admin.errors', 'An error occurred while processing mass operation.');
  }

  /**
   * Return error message for single actions
   * @param array $params parameters to be extracted into function
   * @return string
   */
  protected function getErrorMessage($params = array())
  {
    return Yii::t('admin.errors', 'An error occurred while processing your request. Please try again later.');
  }

  /**
   * Return not found message
   * @param array $params parameters to be extracted into function
   * @return string
   */
  protected function getNotFoundErrorMessage($params = array())
  {
    return Yii::t('admin.errors', 'Model not found.');
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
  abstract function getPlainModel($scenario);
}
