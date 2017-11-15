<?php
/**
 * ViraCMS E-Mail Templates Management Controller
 *
 * @package vira.core.core
 * @subpackage vira.core.content
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class MailController extends VSystemCrudController
{
  protected $actions = array(
    'index',
    'create',
    'view',
    'update',
    'delete',
  );

  protected $accessRules = array(
    '*' => array('coreEmail'),
  );

  public function getTitle($view, $params = array())
  {
    extract($params);

    switch ($view) {
      case 'index':
        return Yii::t('admin.content.titles', 'E-Mail Templates');

      case 'create':
        return Yii::t('admin.content.titles', 'New E-Mail Template');

      case 'view':
        return Yii::t('admin.content.titles', 'View E-Mail Template "{title}"', array('{title}' => $model->name));

      case 'update':
        return Yii::t('admin.content.titles', 'Update E-Mail Template "{title}"', array('{title}' => $model->name));

      case 'delete':
        return Yii::t('admin.content.titles', 'Delete E-Mail Template "{title}"', array('{title}' => $model->name));

      case 'mass':
        return Yii::t('admin.content.titles', 'Mass action with e-mail templates');
    }

    return '';
  }

  protected function getActionSuccessMessage($action, $params = array())
  {
    extract($params);
    switch ($action) {
      case 'create':
        return Yii::t('admin.content.messages', 'E-Mail template "{title}" has been successfully created', array('{title}' => $model->name));

      case 'update':
        return Yii::t('admin.content.messages', 'E-Mail template "{title}" has been successfully updated', array('{title}' => $model->name));

      case 'delete':
        return Yii::t('admin.content.messages', 'E-Mail template "{title}" has been successfully removed', array('{title}' => $model->name));
    }
    parent::getActionSuccessMessage($action, $params);
  }

  public function getActionConfirmMessage($action, $params = array())
  {
    extract($params);
    switch ($action) {
      case 'delete':
        return Yii::t('admin.content.messages', 'Are you sure to delete e-mail template "{title}"?', array('{title}' => $model->name));
    }

    return parent::getActionConfirmMessage($action, $params);
  }

  public function getMassActionConfirmMessage($action, $params = array())
  {
    extract($params);
    switch ($action) {
      case 'delete':
        return Yii::t('admin.content.messages', 'Are you sure to delete selected e-mail template?');
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
        return Yii::t('admin.content.messages', 'E-mail templates has been successfully removed');
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
    return Yii::t('admin.content.errors', 'E-mail template not found');
  }

  protected function preprocessData(&$data)
  {
    $data['languages'] = VLanguageHelper::getLanguages();
  }

  public function getModelTitle($model)
  {
    return $model->name;
  }

  public function getModel($scenario = 'search')
  {
    return new VMailTemplate($scenario);
  }

  public function getPlainModel($scenario)
  {
    return VMailTemplate::model();
  }

  protected function beforeUpdateModel(&$model, $action = null)
  {
    if (parent::beforeUpdateModel($model, $action)) {
      if (in_array($action ? $action : $this->action->id, array('create', 'update'))) {
        $model->populateL10nModels(Yii::app()->request);

        return $model->validate() && $model->validateL10nModels();
      }

      return true;
    }

    return false;
  }

  protected function afterUpdateModel($model, $action = null)
  {
    if (in_array($action ? $action : $this->action->id, array('create', 'update'))) {
      $model->saveL10nModels();
    }
    parent::afterUpdateModel($model, $action);
  }
}
