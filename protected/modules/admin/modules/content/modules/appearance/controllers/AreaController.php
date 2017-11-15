<?php
/**
 * ViraCMS Page Areas Management Controller
 *
 * @package vira.core.core
 * @subpackage vira.core.content
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class AreaController extends VSystemCrudController
{
  protected $actions = array(
    'index',
    'create',
    'view',
    'update',
    'delete',
  );

  protected $accessRules = array(
    '*' => array('contentArea'),
  );

  public function getTitle($view, $params = array())
  {
    extract($params);

    switch ($view) {
      case 'index':
        return Yii::t('admin.content.titles', 'Content Areas');

      case 'create':
        return Yii::t('admin.content.titles', 'New Content Area');

      case 'view':
        return Yii::t('admin.content.titles', 'View Content Area "{title}"', array('{title}' => $model->title));

      case 'update':
        return Yii::t('admin.content.titles', 'Update Content Area "{title}"', array('{title}' => $model->title));

      case 'delete':
        return Yii::t('admin.content.titles', 'Delete Content Area "{title}"', array('{title}' => $model->title));

      case 'mass':
        return Yii::t('admin.content.titles', 'Mass action with content areas');
    }

    return '';
  }

  protected function getActionSuccessMessage($action, $params = array())
  {
    extract($params);
    switch ($action) {
      case 'create':
        return Yii::t('admin.content.messages', 'Content area "{title}" has been successfully created', array('{title}' => $model->title));

      case 'update':
        return Yii::t('admin.content.messages', 'Content area "{title}" has been successfully updated', array('{title}' => $model->title));

      case 'delete':
        return Yii::t('admin.content.messages', 'Content area "{title}" has been successfully removed', array('{title}' => $model->title));
    }
    parent::getActionSuccessMessage($action, $params);
  }

  public function getActionConfirmMessage($action, $params = array())
  {
    extract($params);
    switch ($action) {
      case 'delete':
        return Yii::t('admin.content.messages', 'Are you sure to delete content area "{title}"?', array('{title}' => $model->title));
    }

    return parent::getActionConfirmMessage($action, $params);
  }

  public function getMassActionConfirmMessage($action, $params = array())
  {
    extract($params);
    switch ($action) {
      case 'delete':
        return Yii::t('admin.content.messages', 'Are you sure to delete selected content areas?');
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
        return Yii::t('admin.content.messages', 'Content areas has been successfully removed');
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
    return Yii::t('admin.content.errors', 'Content area not found');
  }

  protected function beforeUpdateModel(&$model, $action = null)
  {
    if (parent::beforeUpdateModel($model, $action)) {
      $action = $action ? $action : $this->action->id;
      if (in_array($action, array('create', 'update'))) {
        $model->setLayouts(Yii::app()->request->getParam('layouts', array()));

        return $model->validate();
      }

      return true;
    }

    return false;
  }

  protected function afterUpdateModel($model, $action = null)
  {
    parent::afterUpdateModel($model, $action);
    if (in_array($action, array('create', 'update'))) {
      $model->removeLayouts();
      $model->addLayouts();
    }
  }

  protected function preprocessData(&$data)
  {
    $data['pageAreaTypes'] = Yii::app()->collection->pageAreaType->toArray();
    $data['sites'] = CHtml::listData(VSite::model()->findAll(), 'id', 'title');
    $data['layouts'] = CHtml::listData(VSiteLayout::model()->findAll(), 'id', 'title', 'siteID');
  }

  public function getModelTitle($model)
  {
    return $model->title;
  }

  public function getModel($scenario = 'search')
  {
    return new VPageArea($scenario);
  }

  public function getPlainModel($scenario)
  {
    return VPageArea::model();
  }
}
