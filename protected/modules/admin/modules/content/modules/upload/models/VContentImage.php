<?php
/**
 * ViraCMS Storage Image Model
 *
 * @package vira.core.core
 * @subpackage vira.core.content
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 *
 * @property string $id primary key
 * @property string $siteID site identifier
 * @property string $className model class name file belongs to
 * @property string $primaryKey model primary key
 * @property string $filename original file name
 * @property string $mime image MIME type
 * @property integer $size image file size
 * @property integer $width image width
 * @property integer $height image height
 * @property string $path file path stored to
 * @property string $comment image comment
 */
class VContentImage extends VContentActiveRecord
{
  /**
   * Initialize model
   */
  public function init()
  {
    parent::init();

    // attach administrative CRUD behaviours only when created inside the system CRUD controller
    if (is_a(Yii::app()->getController(), 'VSystemController')) {
      $this->attachBehaviors(array(
        'SystemLogBehavior' => array(
          'class' => 'VSystemLogBehavior',
          'createMessage' => 'Image [{id}] "{file}" has been created',
          'createParams' => array('{id}' => '$this->id', '{file}' => '$this->filename'),
          'updateMessage' => 'Image [{id}] "{file}" has been updated',
          'updateParams' => array('{id}' => '$this->id', '{file}' => '$this->filename'),
          'deleteMessage' => 'Image [{id}] "{file}" has been removed',
          'deleteParams' => array('{id}' => '$this->id', '{file}' => '$this->filename'),
          'translateCategory' => 'admin.content.events',
        ),
      ));
    }
  }

  /**
   * @param string $className
   * @return VContentImage
   */
  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }

  public function tableName()
  {
    return '{{core_content_image}}';
  }

  public function rules()
  {
    return array(
      array('id', 'unsafe', 'except' => 'create'),
      array('id,siteID', 'length', 'is' => 36),
      array('filename,path', 'required', 'on' => 'update'),
      array('upload', 'required', 'on' => 'create'),
      array('upload', 'file', 'types' => Yii::app()->params['allowImageTypes'], 'skipOnError' => true, 'allowEmpty' => true),
      array('filename,comment', 'length', 'max' => 1022),
      array('path', 'length', 'max' => 4094),
      array('className,primaryKey,mime', 'length', 'max' => 255),
      array('width,height,size', 'numerical', 'integerOnly' => true),
      array('id,siteID,className,primaryKey,filename,path,comment', 'safe', 'on' => 'search'),
      array('path,size,mime', 'unsafe'),
    );
  }

  public function attributeLabels()
  {
    return array(
      'id' => Yii::t('common', 'ID'),
      'siteID' => Yii::t('admin.content.labels', 'Site'),
      'filename' => Yii::t('admin.content.labels', 'File Name'),
      'mime' => Yii::t('admin.content.labels', 'MIME Type'),
      'width' => Yii::t('admin.content.labels', 'Image Width'),
      'height' => Yii::t('admin.content.labels', 'Image Height'),
      'size' => Yii::t('admin.content.labels', 'File Size'),
      'path' => Yii::t('admin.content.labels', 'Storage Path'),
      'comment' => Yii::t('admin.content.labels', 'Comment'),
      'upload' => Yii::t('admin.content.labels', 'Select Image'),
      'url' => Yii::t('admin.content.labels', 'Public URL'),
    );
  }

  /**
   * Performs search over records
   * @return CActiveDataProvider
   */
  public function search()
  {
    $criteria = new CDbCriteria;
    $criteria->compare('t.id', $this->id);
    $criteria->compare('t.className', $this->className);
    if (is_array($this->primaryKey)) {
      $this->primaryKey = implode('_', $this->primaryKey);
    }
    $criteria->compare('t.primaryKey', $this->primaryKey);
    $criteria->compare('t.filename', $this->filename, true);
    $criteria->compare('t.width', $this->width, true);
    $criteria->compare('t.height', $this->height, true);
    $criteria->compare('t.comment', $this->comment, true);
    $criteria->compare('t.size', $this->size, true);

    $this->addSiteCondition('siteID', $criteria);

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'pagination' => array(
        'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params['defaultPageSize']),
        'pageVar' => 'page',
      ),
    ));
  }

  /**
   * Return image URL
   * @param integer $width (optional) image width
   * @param integer $height (optional) image height
   * @param boolean $crop (optional) image have to be cropped
   * @param boolean $tmp (optional) do not save image to the cache
   * @param string $cropHorizontalPos (optional) if $crop set to true, you can specify image horizontal position; valid is 'left', 'center' and 'right' values
   * @param string $cropVerticalPos (optional) if $crop set to true, you can specify image vertical position; valid is 'top', 'middle' and 'bottom' values
   * @return string
   */
  public function getUrl($width = 0, $height = 0, $crop = false, $tmp = false, $cropHorizontalPos = 'center', $cropVerticalPos = 'middle')
  {
    $url = Yii::app()->storage->getFileUrl($this->path);
    if (empty($this->filename)) {
      $url = Yii::app()->createUrl('/image/empty');
    }
    else {
      if ($width || $height) {
        $hash = $crop ?
          Yii::app()->image->generateHash($width, $height, VFileHelper::getMimeTypeByExtension($this->path), $cropHorizontalPos, $cropVerticalPos) :
          Yii::app()->image->generateHash($width, $height, VFileHelper::getMimeTypeByExtension($this->path));
        $params = array(
          'hash' => $hash,
          'width' => $width ? $width : null,
          'height' => $height ? $height : null,
          'filename' => strtr($this->path, array(
            '/' => '_',
          )),
        );
        if ($crop && $cropHorizontalPos && $cropVerticalPos) {
          $params['hpos'] = $cropHorizontalPos;
          $params['vpos'] = $cropVerticalPos;
        }
        $url = strtr(Yii::app()->createUrl($tmp ? '/image/temp' : ($crop ? '/image/crop' : '/image/resize'), array_filter($params)), array(
          '+' => '%20',
        ));
      }
    }

    return $url;
  }
}
