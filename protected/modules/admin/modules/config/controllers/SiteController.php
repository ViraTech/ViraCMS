<?php
/**
 * ViraCMS Sites Management Controller
 *
 * @package vira.core.core
 * @subpackage vira.core.content
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class SiteController extends VSystemCrudController
{
  protected $actions = array(
    'index',
    'view',
    'create',
    'update',
    'delete',
    'open',
  );

  protected $accessRules = array(
    '*' => array('coreSite'),
  );

  public function actionOpen($id)
  {
    $site = $this->loadModel($id);

    $recently = Yii::app()->user->getState('Vira.RecentlyOpenSites', array());
    if (($key = array_search($site->id, $recently)) !== false) {
      unset($recently[$key]);
    }
    Yii::app()->user->setState('Vira.RecentlyOpenSites', array_merge(array($site->id), $recently));

    $this->redirect($site->host ? ((stripos($site->host, 'http://') !== 0 && stripos($site->host, 'https://') !== 0 ? 'http://' : '') . $site->host . '/') : $this->createUrl('/site/index'));
  }

  public function getTitle($view, $params = array())
  {
    extract($params);

    switch ($view) {
      case 'index':
        return Yii::t('admin.content.titles', 'Sites');

      case 'create':
        return Yii::t('admin.content.titles', 'New Site');

      case 'view':
        return Yii::t('admin.content.titles', 'View Site "{title}"', array('{title}' => $model->title));

      case 'update':
        return Yii::t('admin.content.titles', 'Update Site "{title}"', array('{title}' => $model->title));

      case 'delete':
        return Yii::t('admin.content.titles', 'Delete Site "{title}"', array('{title}' => $model->title));

      case 'mass':
        return Yii::t('admin.content.titles', 'Mass action with sites');
    }

    return '';
  }

  protected function getActionSuccessMessage($action, $params = array())
  {
    extract($params);
    switch ($action) {
      case 'create':
        return Yii::t('admin.content.messages', 'Site "{title}" has been successfully created', array('{title}' => $model->title));

      case 'update':
        return Yii::t('admin.content.messages', 'Site "{title}" has been successfully updated', array('{title}' => $model->title));

      case 'delete':
        return Yii::t('admin.content.messages', 'Site "{title}" has been successfully removed', array('{title}' => $model->title));
    }
    parent::getActionSuccessMessage($action, $params);
  }

  public function getActionConfirmMessage($action, $params = array())
  {
    extract($params);
    switch ($action) {
      case 'delete':
        return Yii::t('admin.content.messages', 'Are you sure to delete site "{title}"?', array('{title}' => $model->title));
    }

    return parent::getActionConfirmMessage($action, $params);
  }

  public function getMassActionConfirmMessage($action, $params = array())
  {
    extract($params);
    switch ($action) {
      case 'delete':
        return Yii::t('admin.content.messages', 'Are you sure to delete selected site?');
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
        return Yii::t('admin.content.messages', 'Sites has been successfully removed');
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
    return Yii::t('admin.content.errors', 'Site not found');
  }

  protected function preprocessData(&$data)
  {
    $data['languages'] = VLanguageHelper::getLanguages();
    $data['themes'] = Yii::app()->themeManager->getFrontendThemes();
  }

  public function getModelTitle($model)
  {
    return $model->title;
  }

  public function getModel($scenario = 'search')
  {
    return new VSite($scenario);
  }

  public function getPlainModel($scenario)
  {
    return VSite::model();
  }
}
