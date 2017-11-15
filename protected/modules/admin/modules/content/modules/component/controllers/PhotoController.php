<?php
/**
 * ViraCMS Photo Gallery Management Controller
 *
 * @package vira.core.core
 * @subpackage vira.core.content
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class PhotoController extends VSystemCrudController
{
  const UPLOADED_IMAGES_KEY = 'Vira.Photo.Upload';
  const IMAGE_PREVIEW_WIDTH = 256;
  const IMAGE_PREVIEW_HEIGHT = 128;

  protected $actions = array(
    'index',
    'view',
    'create',
    'update',
    'delete',
    'upload',
  );

  protected $accessRules = array(
    'index' => array('corePhotoRead'),
    'view' => array('corePhotoRead'),
    'create' => array('corePhotoUpdate'),
    'update' => array('corePhotoUpdate'),
    'delete' => array('corePhotoUpdate'),
    'upload' => array('corePhotoDelete'),
  );

  private $_images;

  public function actionIndex()
  {
    $uploaded = Yii::app()->user->getState(self::UPLOADED_IMAGES_KEY, array());
    if (!empty($uploaded)) {
      $pks = array();

      foreach ($uploaded as $img) {
        $pks[] = $img['id'];
      }

      foreach (VContentImage::model()->findAllByPk($pks) as $model) {
        $model->delete();
      }

      Yii::app()->user->setState(self::UPLOADED_IMAGES_KEY, array());
    }

    parent::actionIndex();
  }

  public function actionUpload($filename)
  {
    $images = Yii::app()->user->getState(self::UPLOADED_IMAGES_KEY, array());
    $r = Yii::app()->request;

    $upload = CUploadedFile::getInstanceByName($filename);

    if ($upload instanceof CUploadedFile) {
      $tmpFile = $upload->tempName;
    }
    else {
      $tmpFile = tempnam(Yii::app()->runtimePath, 'imgxhr');
      $tmpFileHandle = fopen($tmpFile, 'w');
      $input = fopen("php://input", "r");
      $realSize = stream_copy_to_stream($input, $tmpFileHandle);
      fclose($input);
      fclose($tmpFileHandle);
      if (isset($_SERVER['CONTENT_LENGTH']) && $realSize !== intval($_SERVER['CONTENT_LENGTH'])) {
        @unlink($tmpFile);
      }
    }

    if (file_exists($tmpFile)) {
      $image = new VContentImage('auto');
      $image->filename = $filename;
      $image->path = Yii::app()->storage->addFile($tmpFile, $filename);
      if (!$image->save()) {
        throw new CHttpException(400, Yii::t('common', 'Can not save model {model}: {error}', array(
          '{model}' => get_class($image),
          '{error}' => $image->getFirstError(),
        )));
      }

      $images[] = array('id' => $image->id);
      Yii::app()->user->setState(self::UPLOADED_IMAGES_KEY, $images);

      $result = array(
        'success' => true,
        'html' => $this->renderPartial('image', array('image' => $this->createImageModel($image)), true),
      );
    }
    else {
      $result = array(
        'error' => Yii::t('common', 'An error occurred while processing'),
      );
    }

    echo CJSON::encode($result);
  }

  public function getTitle($view, $params = array())
  {
    extract($params);

    switch ($view) {
      case 'index':
        return Yii::t('admin.content.titles', 'Photo');

      case 'create':
        return Yii::t('admin.content.titles', 'New Photo');

      case 'view':
        return Yii::t('admin.content.titles', 'View Photo');

      case 'update':
        return Yii::t('admin.content.titles', 'Update Photo');

      case 'delete':
        return Yii::t('admin.content.titles', 'Delete Photo');

      case 'mass':
        return Yii::t('admin.content.titles', 'Mass action with photo');
    }

    return '';
  }

  protected function getActionSuccessMessage($action, $params = array())
  {
    extract($params);

    switch ($action) {
      case 'create':
        return Yii::t('admin.content.messages', 'Photo "{title}" has been successfully created', array('{title}' => $model->title));

      case 'update':
        return Yii::t('admin.content.messages', 'Photo "{title}" has been successfully updated', array('{title}' => $model->title));

      case 'delete':
        return Yii::t('admin.content.messages', 'Photo "{title}" has been successfully removed', array('{title}' => $model->title));
    }

    parent::getActionSuccessMessage($action, $params);
  }

  public function getActionConfirmMessage($action, $params = array())
  {
    extract($params);

    switch ($action) {
      case 'delete':
        return Yii::t('admin.content.messages', 'Are you sure to delete photo "{title}"?', array('{title}' => $model->title));
    }

    return parent::getActionConfirmMessage($action, $params);
  }

  public function getMassActionConfirmMessage($action, $params = array())
  {
    extract($params);

    switch ($action) {
      case 'delete':
        return Yii::t('admin.content.messages', 'Are you sure to delete selected photo?');
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
        return Yii::t('admin.content.messages', 'Selected photo has been successfully removed');
    }

    return parent::getMassActionSuccessMessage($action, $params);
  }

  public function getErrorMessage($params = array())
  {
    extract($params);

    return Yii::t('common', 'An error occurred while processing: {error}.', array(
        '{error}' => $model->getFirstError(),
    ));
  }

  public function getMassActionErrorMessage($params = array())
  {
    extract($params);
    return Yii::t('common', 'Please select something!');
  }

  public function getNotFoundErrorMessage($params = array())
  {
    return Yii::t('admin.content.messages', 'Photo not found');
  }

  public function getModelTitle($model)
  {
    return $model->title;
  }

  public function getModel($scenario = 'search')
  {
    $model = new VPhoto($scenario);

    if ($scenario == 'create') {
      $model->languageID = Yii::app()->getLanguage();
    }

    return $model;
  }

  public function getPlainModel($scenario)
  {
    $model = VPhoto::model();
    $model->setScenario($scenario);

    return $model;
  }

  protected function beforeUpdateModel(&$model, $action = null)
  {
    if (parent::beforeUpdateModel($model, $action)) {
      if (in_array($action, array('create', 'update'))) {
        $r = Yii::app()->getRequest();
        $images = $r->getParam('images', array());

        foreach ($this->getImages($model) as $image) {
          foreach ($images as $imageID => $attributes) {
            if ($image->imageID == $imageID) {
              $image->setAttributes($attributes);
            }
          }
        }

        return $model->validate();
      }

      return true;
    }

    return false;
  }

  protected function afterUpdateModel($model, $action = null)
  {
    if (in_array($action, array('create', 'update'))) {
      foreach ($this->getImages($model) as $image) {
        if ($image->deleteFlag) {
          if (!$image->isNewRecord) {
            $image->delete();
          }
        }
        else {
          if ($image->image) {
            $image->image->siteID = $model->siteID;
            $image->image->className = get_class($model);
            $image->image->primaryKey = $model->id;
            $image->image->save(false);
          }
          $image->save(false);
        }
      }

      Yii::app()->user->setState(self::UPLOADED_IMAGES_KEY, array());
    }

    parent::afterUpdateModel($model, $action);
  }

  protected function preprocessData(&$data)
  {
    $data['languages'] = VLanguageHelper::getLanguages();
    $data['currentLanguage'] = Yii::app()->getLanguage();
  }

  protected function createImageModel($image, $ownerID = null)
  {
    $model = new VPhotoImage('create');

    $model->ownerID = $ownerID;
    $model->imageID = $image['id'];

    return $model;
  }

  public function getImages($model)
  {
    if ($this->_images === null) {
      $this->_images = $model->images;

      foreach (Yii::app()->user->getState(self::UPLOADED_IMAGES_KEY, array()) as $image) {
        $this->_images[] = $this->createImageModel($image, $model->id);
      }
    }

    return $this->_images;
  }
}
