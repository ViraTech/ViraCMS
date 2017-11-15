<?php
/**
 * ViraCMS Site Administrator Roles Management Controller
 *
 * @package vira.core.core
 * @subpackage vira.core.registry
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class RoleController extends VSystemCrudController
{
  protected $actions = array(
    'index',
    'create',
    'view',
    'update',
    'delete',
  );

  protected $accessRules = array(
    '*' => array('registryRole'),
  );

  public function actionUpdate($id)
  {
    $model = $this->loadModel($id);

    if ($model->system) {
      $this->setPageTitle($this->getTitle('view', array('model' => $model)));
      $this->renderUpdate($model, array(), 'view');
      Yii::app()->end();
    }

    $this->processAjaxRequest('update', $model);
    $this->updateModel($model);
    $this->setPageTitle($this->getTitle('update', array('model' => $model)));
    $this->renderUpdate($model);
  }

  public function getTitle($view, $params = array())
  {
    extract($params);

    switch ($view) {
      case 'index':
        return Yii::t('admin.registry.titles', 'Account Roles');

      case 'create':
        return Yii::t('admin.registry.titles', 'New Account Role');

      case 'update':
        return Yii::t('admin.registry.titles', 'Update Account Role "{id}"', array('{id}' => $model->id));

      case 'view':
        return Yii::t('admin.registry.titles', $model->system ? 'View System Role "{id}"' : 'View Account Role "{id}"', array('{id}' => $model->id));

      case 'delete':
        return Yii::t('admin.registry.titles', 'Delete Account Role "{id}"', array('{id}' => $model->id));

      case 'mass':
        return Yii::t('admin.registry.titles', 'Mass action with account roles');
    }

    return '';
  }

  protected function getActionSuccessMessage($action, $params = array())
  {
    extract($params);
    switch ($action) {
      case 'create':
        return Yii::t('admin.registry.messages', 'Account role "{id}" has been successfully created', array('{id}' => $model->id));

      case 'update':
        return Yii::t('admin.registry.messages', 'Account role "{id}" has been successfully updated', array('{id}' => $model->id));

      case 'delete':
        return Yii::t('admin.registry.messages', 'Account role "{id}" has been successfully removed', array('{id}' => $model->id));
    }
    parent::getActionSuccessMessage($action, $params);
  }

  public function getActionConfirmMessage($action, $params = array())
  {
    extract($params);
    switch ($action) {
      case 'delete':
        return Yii::t('admin.registry.messages', 'Are you sure to delete account role "{id}"?', array('{id}' => $model->id));
    }

    return parent::getActionConfirmMessage($action, $params);
  }

  public function getMassActionConfirmMessage($action, $params = array())
  {
    extract($params);
    switch ($action) {
      case 'delete':
        return Yii::t('admin.registry.messages', 'Are you sure to delete selected account roles?');
    }

    return parent::getMassActionConfirmMessage($action, $params);
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

  public function getMassActionSuccessMessage($action, $params = array())
  {
    extract($params);
    switch ($action) {
      case 'delete':
        return Yii::t('admin.registry.messages', 'Account roles has been successfully removed');
    }

    return parent::getMassActionSuccessMessage($action, $params);
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
    return Yii::t('admin.registry.errors', 'Account role not found');
  }

  public function getModelTitle($model)
  {
    return $model->title;
  }

  public function getModel($scenario = 'search')
  {
    return new VAccountRole($scenario);
  }

  public function getPlainModel($scenario)
  {
    return VAccountRole::model();
  }

  protected function preprocessData(&$data)
  {
    $data['accessSections'] = Yii::app()->authManager->getAccessSections();
    $data['accessGroups'] = Yii::app()->authManager->getAccessGroups();
    $data['accessRules'] = Yii::app()->authManager->getAccessRules();
  }

  protected function afterUpdateModel($model, $action = null)
  {
    $accessFlags = Yii::app()->request->getParam('accessFlags');
    if ($accessFlags) {
      $accountAccessModel = VAccountAccess::model();
      $tableName = $accountAccessModel->tableName();
      $accountAccessModel->deleteAllByAttributes(array('accountRoleID' => $model->id));
      $command = Yii::app()->db->createCommand();
      foreach ($accessFlags as $flag => $value) {
        $command->insert($tableName, array(
          'accountRoleID' => $model->id,
          'accessRuleID' => $flag,
          'permit' => $value,
        ));
      }
    }

    Yii::app()->cache->deleteTag('Vira.Role');

    parent::afterUpdateModel($model, $action);
  }

  protected function isMassActionAllowed($model, $action)
  {
    if (parent::isMassActionAllowed($model, $action)) {
      if ($action == 'delete' && $model->system) {
        return false;
      }

      return true;
    }

    return false;
  }
}
