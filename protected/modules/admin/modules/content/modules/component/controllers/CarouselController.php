<?php
/**
 * ViraCMS Carousel Management Controller
 *
 * @package vira.core.core
 * @subpackage vira.core.content
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class CarouselController extends VSystemCrudController
{
  const UPLOADED_IMAGES_KEY = 'Vira.Carousel.Upload';
  const IMAGE_PREVIEW_WIDTH = 360;
  const IMAGE_PREVIEW_HEIGHT = 250;

  protected $actions = array(
    'index',
    'view',
    'create',
    'update',
    'delete',
    'enable',
    'disable',
    'upload',
    'ajax',
  );

  protected $accessRules = array(
    'index' => array(
      'coreCarouselRead',
    ),
    'view' => array(
      'coreCarouselRead',
    ),
    'create' => array(
      'coreCarouselUpdate',
    ),
    'update' => array(
      'coreCarouselUpdate',
    ),
    'upload' => array(
      'coreCarouselUpdate',
    ),
    'ajax' => array(
      'coreCarouselUpdate',
    ),
    'delete' => array(
      'coreCarouselDelete',
    ),
  );

  public function actionIndex()
  {
    $uploaded = Yii::app()->user->getState(self::UPLOADED_IMAGES_KEY, array());
    if (is_array($uploaded) && count($uploaded)) {
      $images = VContentImage::model()->findAllByPk($uploaded);
      foreach ($images as $image) {
        $image->setScenario('auto');
        $image->delete();
      }
      Yii::app()->user->setState(self::UPLOADED_IMAGES_KEY, array());
    }

    parent::actionIndex();
  }

  public function actionAjax($ajax)
  {
    header('Content-Type: application/json');
    $data = array();

    switch ($ajax) {
      case 'site':
        $sitemap = Yii::app()->siteMap->getMapItems(Yii::app()->request->getParam('site'));
        foreach (VLanguageHelper::getLanguages() as $language) {
          $data['sitemap'][$language->id] = $sitemap;
        }
        break;
    }

    echo CJSON::encode($data);
  }

  public function actionUpload($filename)
  {
    $images = Yii::app()->user->getState(self::UPLOADED_IMAGES_KEY, array());
    $url = '';
    $result = true;

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
      $model = new VContentImage('auto');
      $model->filename = $filename;
      $model->path = Yii::app()->storage->addFile($tmpFile, $filename, false);
      if (!$model->save()) {
        $result = false;
        $error = Yii::t('common', 'Can not save model {model}: {error}', array('{model}', get_class($model), '{error}' => $model->getFirstError()));
      }
      $image = $model->id;
      $url = $model->getUrl(self::IMAGE_PREVIEW_WIDTH, self::IMAGE_PREVIEW_HEIGHT, true);
      @unlink($tmpFile);
    }

    if ($result) {
      if ($image) {
        $images[] = $image;
      }
      Yii::app()->user->setState(self::UPLOADED_IMAGES_KEY, $images);

      $result = array(
        'success' => true,
        'url' => $url,
        'id' => $image,
      );
    }
    else {
      $result = array(
        'error' => empty($error) ? Yii::t('common', 'An error occurred while processing') : $error,
      );
    }

    echo CJSON::encode($result);
  }

  public function getTitle($view, $params = array())
  {
    extract($params);

    switch ($view) {
      case 'index':
        return Yii::t('admin.content.titles', 'Carousels');

      case 'create':
        return Yii::t('admin.content.titles', 'New Carousel');

      case 'update':
        return Yii::t('admin.content.titles', 'Update Carousel "{title}"', array('{title}' => $model->title));

      case 'enable':
        return Yii::t('admin.content.titles', 'Publish Carousel "{title}"', array('{title}' => $model->title));

      case 'disable':
        return Yii::t('admin.content.titles', 'Hide Carousel "{title}"', array('{title}' => $model->title));

      case 'delete':
        return Yii::t('admin.content.titles', 'Delete Carousel "{title}"', array('{title}' => $model->title));

      case 'mass':
        return Yii::t('admin.content.titles', 'Mass action with carousels');
    }

    return '';
  }

  protected function getActionSuccessMessage($action, $params = array())
  {
    extract($params);
    switch ($action) {
      case 'create':
        return Yii::t('admin.content.messages', 'Carousel "{title}" has been successfully created', array('{title}' => $model->title));

      case 'update':
        return Yii::t('admin.content.messages', 'Carousel "{title}" has been successfully updated', array('{title}' => $model->title));

      case 'enable':
        return Yii::t('admin.content.messages', 'Carousel "{title}" has been successfully published', array('{title}' => $model->title));

      case 'disable':
        return Yii::t('admin.content.messages', 'Carousel "{title}" has been successfully hidden', array('{title}' => $model->title));

      case 'delete':
        return Yii::t('admin.content.messages', 'Carousel "{title}" has been successfully removed', array('{title}' => $model->title));
    }
    parent::getActionSuccessMessage($action, $params);
  }

  public function getActionConfirmMessage($action, $params = array())
  {
    extract($params);
    switch ($action) {
      case 'enable':
        return Yii::t('admin.content.messages', 'Are you sure to publish carousel "{title}"?', array('{title}' => $model->title));

      case 'disable':
        return Yii::t('admin.content.messages', 'Are you sure to hide carousel "{title}"?', array('{title}' => $model->title));

      case 'delete':
        return Yii::t('admin.content.messages', 'Are you sure to delete carousel "{title}"?', array('{title}' => $model->title));
    }

    return parent::getActionConfirmMessage($action, $params);
  }

  public function getMassActionConfirmMessage($action, $params = array())
  {
    extract($params);
    switch ($action) {
      case 'enable':
        return Yii::t('admin.content.messages', 'Are you sure to publish selected carousels?');

      case 'disable':
        return Yii::t('admin.content.messages', 'Are you sure to hide selected carousels?');

      case 'delete':
        return Yii::t('admin.content.messages', 'Are you sure to delete selected carousels?');
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
          'label' => Yii::t('common', 'Publish'),
          'icon' => 'icon-ok-circle',
          'htmlOptions' => array(
            'name' => 'enable',
            'data-loading-text' => '<i class="icon-spinner icon-spin"></i> ' . Yii::t('common', 'Saving...'),
          ),
        );

      case 'disable':
        return array(
          'type' => 'inverse',
          'buttonType' => 'submit',
          'label' => Yii::t('common', 'Hide'),
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
        return Yii::t('admin.content.messages', 'Carousels has been successfully published');

      case 'disable':
        return Yii::t('admin.content.messages', 'Carousels has been successfully hidden');

      case 'delete':
        return Yii::t('admin.content.messages', 'Carousels has been successfully removed');
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
    return Yii::t('admin.content.errors', 'Carousel not found');
  }

  public function getModelTitle($model)
  {
    return $model->getTitle();
  }

  public function getModel($scenario = 'search')
  {
    $model = new VCarousel($scenario);

    if (in_array($scenario, array('create'))) {
      $model->siteID = Yii::app()->site->id;
    }

    return $model;
  }

  public function getPlainModel($scenario)
  {
    return VCarousel::model();
  }

  protected function preprocessData(&$data)
  {
    $data = array_merge($data, array(
      'languages' => VLanguageHelper::getLanguages(),
    ));
  }

  protected function beforeUpdateModel(&$model, $action = null)
  {
    if (parent::beforeUpdateModel($model, $action)) {
      $action = $action ? $action : $this->action->id;
      if (in_array($action, array('create', 'update'))) {
        $model->populateL10nModels(Yii::app()->request);

        return $model->validate() && $model->validateL10nModels();
      }

      return true;
    }

    return false;
  }

  protected function afterUpdateModel($model, $action = null)
  {
    parent::afterUpdateModel($model, $action);

    $action = $action ? $action : $this->action->id;
    if (in_array($action, array('create', 'update'))) {
      $r = Yii::app()->request;
      $model->saveL10nModels();
      $removed = $r->getParam('removed', array());
      $uploaded = Yii::app()->user->getState(self::UPLOADED_IMAGES_KEY, array());
      $existing = array();

      foreach ($removed as $id) {
        if (strpos($id, 'new') === 0) {
          $id = str_replace('new-', '', $id);
          $image = VContentImage::model()->findByPk($uploaded[$id]);
          if ($image instanceof VContentImage) {
            $image->setScenario('auto');
            $image->delete();
          }
        }
        else {
          $image = VCarouselImage::model()->findByPk($id);
          if ($image instanceof VCarouselImage) {
            $image->delete();
          }
        }
      }

      $images = $this->getImages($model);
      foreach ($images as $image) {
        if ($image->isNewRecord) {
          $image->id = $image->getGuid();
        }
        if ($image->image) {
          $image->image->siteID = $model->siteID;
          $image->image->className = get_class($model);
          $image->image->primaryKey = $model->getPrimaryKey();
          $image->image->save(false);
        }
        $image->save(false);
        $image->saveL10nModels(false);
        $existing[] = $image->id;
      }

      $criteria = new CDbCriteria;
      $criteria->compare('carouselID', $model->id);
      $criteria->addNotInCondition('id', $existing);

      foreach (VCarouselImage::model()->findAll($criteria) as $image) {
        $image->delete();
      }

      Yii::app()->user->setState(self::UPLOADED_IMAGES_KEY, array());
    }
  }

  public function getImages($model)
  {
    $images = array();

    $uploaded = Yii::app()->user->getState(self::UPLOADED_IMAGES_KEY, array());
    $removed = Yii::app()->request->getParam('removed', array());
    if (!empty($uploaded)) {
      foreach ($uploaded as $imageID) {
        if (in_array($imageID, $removed)) {
          continue;
        }

        $upload = new VCarouselImage();
        $upload->setAttributes(array(
          'carouselID' => $model->id,
          'imageID' => $imageID,
          ), false);
        $this->setCaptionAttributes($upload, $imageID);

        $images[] = $upload;
      }
    }

    foreach ($model->images as $image) {
      if ($image->image) {
        if (in_array($image->image->id, $removed)) {
          continue;
        }
        $this->setCaptionAttributes($image);
        $images[] = $image;
      }
    }

    return $images;
  }

  protected function setCaptionAttributes(&$model, $id = null)
  {
    if ($id === null) {
      $id = $model->imageID;
    }

    $update = Yii::app()->request->getParam('image', array());

    foreach (VLanguageHelper::getLanguages() as $language) {
      $l10n = $model->getL10nModel($language->id, false);
      if (!empty($update[$id]['caption'][$language->id])) {
        $l10n->setAttributes($update[$id]['caption'][$language->id]);
      }
    }

    $model->position = empty($update[$id]['position']) ? '0' : $update[$id]['position'];
  }
}
