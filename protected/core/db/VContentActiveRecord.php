<?php
/**
 * ViraCMS Storage Base Model
 *
 * @package vira.core.core
 * @subpackage vira.core.content
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VContentActiveRecord extends VActiveRecord
{
  /**
   * @var CUploadedFile file upload variable
   */
  public $upload;

  public function relations()
  {
    return array(
      'site' => array(self::BELONGS_TO, 'VSite', 'siteID'),
    );
  }

  public function behaviors()
  {
    return CMap::mergeArray(
        array(
        'GuidBehavior' => array(
          'class' => 'VGuidBehavior',
        ),
        ), parent::behaviors()
    );
  }

  /**
   * Fired when the file has been uploaded
   * @param CEvent $event event
   */
  public function onFileUpload($event)
  {
    $this->raiseEvent('onFileUpload', $event);
  }

  /**
   * Fired when an error occurred while uploading the file
   * @param type $event
   */
  public function onFileUploadError($event)
  {
    $this->raiseEvent('onFileUploadError', $event);
  }

  /**
   * Fired when an error occurred while deleting the file
   * @param type $event
   */
  public function onObjectDeleteError($event)
  {
    $this->raiseEvent('onObjectDeleteError', $event);
  }

  /**
   * @inheritdoc
   */
  protected function beforeValidate()
  {
    if (parent::beforeValidate()) {
      if (is_array($this->primaryKey)) {
        $this->primaryKey = implode(',', $this->primaryKey);
      }

      return true;
    }

    return false;
  }

  /**
   * @ineritdoc
   */
  protected function beforeSave()
  {
    if (parent::beforeSave()) {
      if ($this->hasAttribute('mime') && empty($this->mime)) {
        $this->mime = $this->getMimeType(Yii::app()->storage->getFilePath($this->path));
      }

      if ($this->hasAttribute('size')) {
        $this->size = Yii::app()->storage->getFileSize($this->path);
      }

      if ($this->hasAttribute('width') && $this->hasAttribute('height')) {
        list($this->width, $this->height) = $this->getImageDimensions();
      }

      return true;
    }

    return false;
  }

  protected function afterDelete()
  {
    parent::afterDelete();

    if (!Yii::app()->storage->deleteFile($this->path)) {
      $this->onObjectDeleteError(new CEvent($this));
    }
  }

  /**
   * Return array with width and height of the image file
   * @param string $fileName (optional) file name, if empty will be used local file from path variable
   * @return array
   */
  public function getImageDimensions($fileName = null)
  {
    if ($fileName === null) {
      $fileName = Yii::app()->storage->getFilePath($this->path);
    }

    $params = getimagesize($fileName);

    return array($params[0], $params[1]);
  }

  /**
   * Return MIME type of the file
   * @param string $fileName (optional) file name, if empty will be used local file from path variable
   * @return array
   */
  public function getMimeType($fileName = null)
  {
    if ($fileName === null) {
      $fileName = Yii::app()->storage->getFilePath($this->path);
    }

    $info = finfo_open(FILEINFO_MIME_TYPE);

    return finfo_file($info, $fileName);
  }
}
