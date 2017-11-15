<?php
/**
 * ViraCMS Uploaded Media Files Management Controller
 *
 * @package vira.core.core
 * @subpackage vira.core.content
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class MediaController extends VSystemCrudController
{
  protected $actions = array(
    'index',
    'create',
    'view',
    'download',
    'update',
    'delete',
  );

  protected $accessRules = array(
    '*' => array('staticMedia'),
  );

  public function actionDownload($id)
  {
    $model = $this->loadModel($id, 'download');
    VFileHelper::sendFile($model);
  }

  public function getTitle($view, $params = array())
  {
    extract($params);

    switch ($view) {
      case 'index':
        return Yii::t('admin.content.titles', 'Uploaded Media Files');

      case 'create':
        return Yii::t('admin.content.titles', 'New Upload');

      case 'view':
        return Yii::t('admin.content.titles', 'View Media File "{filename}"', array('{filename}' => $model->filename));

      case 'update':
        return Yii::t('admin.content.titles', 'Update Media File "{filename}"', array('{filename}' => $model->filename));

      case 'delete':
        return Yii::t('admin.content.titles', 'Delete Media File "{filename}"', array('{filename}' => $model->filename));

      case 'mass':
        return Yii::t('admin.content.titles', 'Mass action with media files');
    }

    return '';
  }

  protected function getActionSuccessMessage($action, $params = array())
  {
    extract($params);
    switch ($action) {
      case 'create':
        return Yii::t('admin.content.messages', 'Media File "{filename}" has been successfully uploaded', array('{filename}' => $model->filename));

      case 'update':
        return Yii::t('admin.content.messages', 'Media File "{filename}" has been successfully updated', array('{filename}' => $model->filename));

      case 'delete':
        return Yii::t('admin.content.messages', 'Media File "{filename}" has been successfully removed', array('{filename}' => $model->filename));
    }
    parent::getActionSuccessMessage($action, $params);
  }

  public function getActionConfirmMessage($action, $params = array())
  {
    extract($params);
    switch ($action) {
      case 'delete':
        return Yii::t('admin.content.messages', 'Are you sure to delete media file "{filename}"?', array('{filename}' => $model->filename));
    }

    return parent::getActionConfirmMessage($action, $params);
  }

  public function getMassActionConfirmMessage($action, $params = array())
  {
    extract($params);
    switch ($action) {
      case 'delete':
        return Yii::t('admin.content.messages', 'Are you sure to delete selected media files?');
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
        return Yii::t('admin.content.messages', 'Media files has been successfully removed');
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
    return Yii::t('admin.content.errors', 'Media file not found');
  }

  protected function preprocessData(&$data)
  {
    $data['maxSize'] = min(
      Yii::app()->format->parseSize(ini_get('upload_max_filesize')), Yii::app()->format->parseSize(ini_get('post_max_size')), Yii::app()->format->parseSize(ini_get('memory_limit'))
    );
  }

  protected function beforeUpdateModel(&$model, $action = null)
  {
    if (parent::beforeUpdateModel($model, $action)) {
      $model->upload = CUploadedFile::getInstance($model, 'upload');
      if ($model->upload instanceof CUploadedFile && $model->validate()) {
        if (Yii::app()->storage->fileExists($model->path)) {
          Yii::app()->storage->deleteFile($model->path);
        }
        $model->path = Yii::app()->storage->addFile($model->upload->tempName, $model->upload->name);
        $model->filename = $model->upload->name;
        $model->mime = $model->upload->type;
        $model->size = $model->upload->size;
      }

      return true;
    }

    return false;
  }

  public function getModelTitle($model)
  {
    return $model->path;
  }

  public function getModel($scenario = 'search')
  {
    return new VContentMedia($scenario);
  }

  public function getPlainModel($scenario)
  {
    return VContentMedia::model();
  }
}
