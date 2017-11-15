<?php
/**
 * ViraCMS Site Administrators Management Controller
 *
 * @package vira.core.core
 * @subpackage vira.core.registry
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class AdminController extends VSystemCrudController
{
  protected $actions = array(
    'index',
    'create',
    'view',
    'update',
    'delete',
    'enable',
    'disable',
  );

  protected $accessRules = array(
    '*' => array('registryAdmin'),
  );

  public function getTitle($view, $params = array())
  {
    extract($params);

    switch ($view) {
      case 'index':
        return Yii::t('admin.registry.titles', 'Administrator Accounts');

      case 'create':
        return Yii::t('admin.registry.titles', 'New Administrator Account');

      case 'view':
        return Yii::t('admin.registry.titles', 'View Administrator Account "{id}"', array('{id}' => $model->id));

      case 'update':
        return Yii::t('admin.registry.titles', 'Update Administrator Account "{id}"', array('{id}' => $model->id));

      case 'enable':
        return Yii::t('admin.registry.titles', 'Enable Administrator Account "{id}"', array('{id}' => $model->id));

      case 'disable':
        return Yii::t('admin.registry.titles', 'Disable Administrator Account "{id}"', array('{id}' => $model->id));

      case 'delete':
        return Yii::t('admin.registry.titles', 'Delete Administrator Account "{id}"', array('{id}' => $model->id));

      case 'mass':
        return Yii::t('admin.registry.titles', 'Mass action with administrator accounts');
    }

    return '';
  }

  protected function getActionSuccessMessage($action, $params = array())
  {
    extract($params);
    switch ($action) {
      case 'create':
        return Yii::t('admin.registry.messages', 'Administrator account "{id}" has been successfully created', array('{id}' => $model->id));

      case 'update':
        return Yii::t('admin.registry.messages', 'Administrator account "{id}" has been successfully updated', array('{id}' => $model->id));

      case 'enable':
        return Yii::t('admin.registry.messages', 'Administrator account "{id}" has been successfully enabled', array('{id}' => $model->id));

      case 'disable':
        return Yii::t('admin.registry.messages', 'Administrator account "{id}" has been successfully disabled', array('{id}' => $model->id));

      case 'delete':
        return Yii::t('admin.registry.messages', 'Administrator account "{id}" has been successfully removed', array('{id}' => $model->id));
    }
    parent::getActionSuccessMessage($action, $params);
  }

  public function getActionConfirmMessage($action, $params = array())
  {
    extract($params);
    switch ($action) {
      case 'enable':
        return Yii::t('admin.registry.messages', 'Are you sure to enable administrator account "{id}"?', array('{id}' => $model->id));

      case 'disable':
        return Yii::t('admin.registry.messages', 'Are you sure to disable administrator account "{id}"?', array('{id}' => $model->id));

      case 'delete':
        return Yii::t('admin.registry.messages', 'Are you sure to delete administrator account "{id}"?', array('{id}' => $model->id));
    }

    return parent::getActionConfirmMessage($action, $params);
  }

  public function getMassActionConfirmMessage($action, $params = array())
  {
    extract($params);
    switch ($action) {
      case 'enable':
        return Yii::t('admin.registry.messages', 'Are you sure to activate selected administrator accounts?');

      case 'disable':
        return Yii::t('admin.registry.messages', 'Are you sure to ban selected administrator accounts?');

      case 'delete':
        return Yii::t('admin.registry.messages', 'Are you sure to delete selected administrator accounts?');
    }

    return parent::getMassActionConfirmMessage($action, $params);
  }

  public function getActionButtonConfig($action, $params = array())
  {
    extract($params);
    switch ($action) {
      case 'enable':
        return array(
          'type' => 'success',
          'buttonType' => 'submit',
          'label' => Yii::t('common', 'Enable'),
          'icon' => 'icon-ok-sign',
          'htmlOptions' => array(
            'name' => 'enable',
            'data-loading-text' => '<i class="icon-spinner icon-spin"></i> ' . Yii::t('common', 'Saving...'),
          ),
        );

      case 'disable':
        return array(
          'type' => 'inverse',
          'buttonType' => 'submit',
          'label' => Yii::t('common', 'Disable'),
          'icon' => 'icon-ban-circle',
          'htmlOptions' => array(
            'name' => 'disable',
            'data-loading-text' => '<i class="icon-spinner icon-spin"></i> ' . Yii::t('common', 'Saving...'),
          ),
        );

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
      case 'enable':
        return Yii::t('admin.registry.messages', 'Administrator accounts has been successfully activated');

      case 'disable':
        return Yii::t('admin.registry.messages', 'Administrator accounts has been successfully disabled');

      case 'delete':
        return Yii::t('admin.registry.messages', 'Administrator accounts has been successfully removed');
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
    return Yii::t('admin.registry.errors', 'Administrator account not found');
  }

  public function getModelTitle($model)
  {
    return implode(' ', array($model->name, '(' . $model->email . ')'));
  }

  public function getModel($scenario = 'search')
  {
    return new VSiteAdmin($scenario);
  }

  public function getPlainModel($scenario)
  {
    return VSiteAdmin::model();
  }

  protected function beforeUpdateModel(&$model, $action = null)
  {
    if (parent::beforeUpdateModel($model, $action)) {
      $attr = Yii::app()->request->getParam(get_class($model));
      if (!empty($attr['newPassword']) || !empty($attr['newPasswordConfirm'])) {
        $model->setScenario('passwordUpdate');
      }
      if (in_array($action, array('create', 'update'))) {
        $model->setSiteAccessList(Yii::app()->request->getParam('SiteAccessList', array()));
      }
      return true;
    }

    return false;
  }

  protected function preprocessData(&$data)
  {
    $data['sites'] = VSite::model()->autoFilter()->findAll();
  }
}
