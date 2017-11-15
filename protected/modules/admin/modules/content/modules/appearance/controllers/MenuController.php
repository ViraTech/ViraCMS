<?php
/**
 * ViraCMS Custom Menu's Management Controller
 *
 * @package vira.core.core
 * @subpackage vira.core.content
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class MenuController extends VSystemCrudController
{
  protected $actions = array(
    'index',
    'create',
    'view',
    'update',
    'delete',
    'reload',
  );

  protected $accessRules = array(
    '*' => array('contentCustomMenu'),
  );

  public function actionReload($site)
  {
    header('Content-Type: application/json');

    $data = array();
    $this->loadSitemap($data, $site);

    echo CJSON::encode($data);
  }

  public function getTitle($view, $params = array())
  {
    extract($params);

    switch ($view) {
      case 'index':
        return Yii::t('admin.content.titles', 'Custom Menus');

      case 'create':
        return Yii::t('admin.content.titles', 'New Custom Menu');

      case 'view':
        return Yii::t('admin.content.titles', 'View Custom Menu "{title}"', array('{title}' => $model->title));

      case 'update':
        return Yii::t('admin.content.titles', 'Update Custom Menu "{title}"', array('{title}' => $model->title));

      case 'delete':
        return Yii::t('admin.content.titles', 'Delete Custom Menu "{title}"', array('{title}' => $model->title));

      case 'mass':
        return Yii::t('admin.content.titles', 'Mass action with custom menus');
    }

    return '';
  }

  protected function getActionSuccessMessage($action, $params = array())
  {
    extract($params);
    switch ($action) {
      case 'create':
        return Yii::t('admin.content.messages', 'Custom menu "{title}" has been successfully created', array('{title}' => $model->title));

      case 'update':
        return Yii::t('admin.content.messages', 'Custom menu "{title}" has been successfully updated', array('{title}' => $model->title));

      case 'delete':
        return Yii::t('admin.content.messages', 'Custom menu "{title}" has been successfully removed', array('{title}' => $model->title));
    }
    parent::getActionSuccessMessage($action, $params);
  }

  public function getActionConfirmMessage($action, $params = array())
  {
    extract($params);
    switch ($action) {
      case 'delete':
        return Yii::t('admin.content.messages', 'Are you sure to delete custom menu "{title}"?', array('{title}' => $model->title));
    }

    return parent::getActionConfirmMessage($action, $params);
  }

  public function getMassActionConfirmMessage($action, $params = array())
  {
    extract($params);
    switch ($action) {
      case 'delete':
        return Yii::t('admin.content.messages', 'Are you sure to delete selected custom menus?');
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
        return Yii::t('admin.content.messages', 'Custom menus has been successfully removed');
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
    return Yii::t('admin.content.errors', 'Custom menu not found');
  }

  protected function preprocessData(&$data)
  {
    $data['currentLanguageID'] = Yii::app()->getLanguage();
    $data['languages'] = VLanguageHelper::getLanguages();
    $data['targets'] = array(
      '' => Yii::t('admin.content.labels', 'Standard Item'),
      '_blank' => Yii::t('admin.content.labels', 'Open in New Tab/Window'),
    );
    $this->loadSitemap($data, $data['model']->siteID);
  }

  public function getModelTitle($model)
  {
    return $model->title;
  }

  public function getModel($scenario = 'search')
  {
    $model = new VCustomMenu($scenario);

    if ($scenario == 'create') {
      $model->siteID = Yii::app()->site->id;
    }

    return $model;
  }

  public function getPlainModel($scenario)
  {
    return VCustomMenu::model();
  }

  protected function beforeUpdateModel(&$model, $action = null)
  {
    if (parent::beforeUpdateModel($model, $action)) {
      $model->menu = Yii::app()->request->getParam('items', array());

      return true;
    }

    return false;
  }

  protected function loadSitemap(&$data, $siteID)
  {
    if ($siteID) {
      $data['sitemap'] = Yii::app()->siteMap->get($siteID);
      $data['pages'] = Yii::app()->siteMap->getMapItems($siteID);
      $data['pageUrls'] = Yii::app()->siteMap->getMapItems($siteID, 'url', false);
    }
  }
}
