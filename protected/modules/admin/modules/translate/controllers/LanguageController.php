<?php
/**
 * ViraCMS Languages Management Controller
 *
 * @package vira.core.core
 * @subpackage vira.core.translate
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class LanguageController extends VSystemCrudController
{
  protected $actions = array(
    'index',
    'create',
    'view',
    'update',
    'disable',
    'enable',
    'delete',
  );

  protected $accessRules = array(
    '*' => array('translateLanguages'),
  );

  public function getTitle($view, $params = array())
  {
    extract($params);

    switch ($view) {
      case 'index':
        return Yii::t('admin.translate.titles', 'Languages');

      case 'create':
        return Yii::t('admin.translate.titles', 'New Language');

      case 'view':
        return Yii::t('admin.translate.titles', 'View Language "{code}"', array('{code}' => $model->id));

      case 'update':
        return Yii::t('admin.translate.titles', 'Update Language "{code}"', array('{code}' => $model->id));

      case 'enable':
        return Yii::t('admin.translate.titles', 'Enable Language "{code}"', array('{code}' => $model->id));

      case 'disable':
        return Yii::t('admin.translate.titles', 'Disable Language "{code}"', array('{code}' => $model->id));

      case 'delete':
        return Yii::t('admin.translate.titles', 'Delete Language "{code}"', array('{code}' => $model->id));

      case 'mass':
        return Yii::t('admin.translate.titles', 'Mass action with languages');
    }

    return '';
  }

  protected function getActionSuccessMessage($action, $params = array())
  {
    extract($params);
    switch ($action) {
      case 'create':
        return Yii::t('admin.translate.messages', 'Language "{code}" has been successfully created', array('{code}' => $model->id));

      case 'update':
        return Yii::t('admin.translate.messages', 'Language "{code}" has been successfully updated', array('{code}' => $model->id));

      case 'enable':
        return Yii::t('admin.translate.messages', 'Language "{code}" has been successfully enabled', array('{code}' => $model->id));

      case 'disable':
        return Yii::t('admin.translate.messages', 'Language "{code}" has been successfully disabled', array('{code}' => $model->id));

      case 'delete':
        return Yii::t('admin.translate.messages', 'Language "{code}" has been successfully removed', array('{code}' => $model->id));
    }
    parent::getActionSuccessMessage($action, $params);
  }

  public function getActionConfirmMessage($action, $params = array())
  {
    extract($params);
    switch ($action) {
      case 'enable':
        return Yii::t('admin.translate.messages', 'Are you sure to enable language "{code}"?', array('{code}' => $model->id));

      case 'disable':
        return Yii::t('admin.translate.messages', 'Are you sure to disable language "{code}"?', array('{code}' => $model->id));

      case 'delete':
        return Yii::t('admin.translate.messages', 'Are you sure to delete language "{code}"?', array('{code}' => $model->id));
    }

    return parent::getActionConfirmMessage($action, $params);
  }

  public function getMassActionConfirmMessage($action, $params = array())
  {
    extract($params);
    switch ($action) {
      case 'delete':
        return Yii::t('admin.translate.messages', 'Are you sure to delete selected languages?');
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
          'icon' => 'ok-circle',
          'htmlOptions' => array(
            'name' => 'enable',
            'data-loading-text' => '<i class="icon-spinner icon-spin"></i> ' . Yii::t('common', 'Processing...'),
          ),
        );

      case 'disable':
        return array(
          'type' => 'inverse',
          'buttonType' => 'submit',
          'label' => Yii::t('common', 'Disable'),
          'icon' => 'ban-circle',
          'htmlOptions' => array(
            'name' => 'disable',
            'data-loading-text' => '<i class="icon-spinner icon-spin"></i> ' . Yii::t('common', 'Processing...'),
          ),
        );

      case 'delete':
        return array(
          'type' => 'danger',
          'buttonType' => 'submit',
          'label' => Yii::t('common', 'Delete'),
          'icon' => 'trash',
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
        return Yii::t('admin.translate.messages', 'Languages has been successfully removed');
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
    return Yii::t('admin.translate.errors', 'Language not found');
  }

  public function getModelTitle($model)
  {
    return $model->title;
  }

  public function getModel($scenario = 'search')
  {
    return new VLanguage($scenario);
  }

  public function getPlainModel($scenario)
  {
    return VLanguage::model();
  }
}
