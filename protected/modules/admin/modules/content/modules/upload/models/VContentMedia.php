<?php
/**
 * ViraCMS Storage Mediafile Model
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
 * @property string $mime media MIME type
 * @property integer $size file size
 * @property string $path file path stored to
 * @property string $comment media file comment
 */
class VContentMedia extends VContentActiveRecord
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
          'createMessage' => 'Media file [{id}] "{file}" has been created',
          'createParams' => array('{id}' => '$this->id', '{file}' => '$this->filename'),
          'updateMessage' => 'Media file [{id}] "{file}" has been updated',
          'updateParams' => array('{id}' => '$this->id', '{file}' => '$this->filename'),
          'deleteMessage' => 'Media file [{id}] "{file}" has been removed',
          'deleteParams' => array('{id}' => '$this->id', '{file}' => '$this->filename'),
          'translateCategory' => 'admin.content.events',
        ),
      ));
    }
  }

  /**
   * @param string $className
   * @return VContentMedia
   */
  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }

  public function tableName()
  {
    return '{{core_content_media}}';
  }

  public function rules()
  {
    return array(
      array('id', 'unsafe', 'except' => 'create'),
      array('id,siteID', 'length', 'is' => 36),
      array('upload', 'required', 'on' => 'create'),
      array('filename,path,mime', 'required', 'on' => 'update'),
      array('upload', 'file', 'types' => implode(',', array(Yii::app()->params['allowAudioTypes'], Yii::app()->params['allowVideoTypes'], Yii::app()->params['allowFlashTypes'])), 'skipOnError' => true, 'allowEmpty' => true),
      array('filename,comment', 'length', 'max' => 1022),
      array('path', 'length', 'max' => 4094),
      array('className,primaryKey', 'length', 'max' => 255),
      array('mime', 'length', 'max' => 64),
      array('size', 'numerical', 'integerOnly' => true),
      array('path,mime,size', 'unsafe', 'except' => 'search'),
      array('id,siteID,className,primaryKey,mime,filename,path,comment', 'safe', 'on' => 'search'),
    );
  }

  public function attributeLabels()
  {
    return array(
      'id' => Yii::t('common', 'ID'),
      'siteID' => Yii::t('admin.content.labels', 'Site'),
      'filename' => Yii::t('admin.content.labels', 'File Name'),
      'mime' => Yii::t('admin.content.labels', 'MIME Type'),
      'size' => Yii::t('admin.content.labels', 'File Size'),
      'path' => Yii::t('admin.content.labels', 'Storage Path'),
      'comment' => Yii::t('admin.content.labels', 'Comment'),
      'upload' => Yii::t('admin.content.labels', 'Select Media File'),
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
    $criteria->compare('t.mime', $this->mime);
    $criteria->compare('t.filename', $this->filename, true);
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
   * Returns a file URL
   * @return string file URL
   */
  public function getUrl($absolute = false)
  {
    return Yii::app()->storage->getFileUrl($this->path, $absolute);
  }

  /**
   * Returns the player component media file can be played with
   * @return mixed
   */
  public function getPlayer()
  {
    $ext = pathinfo($this->filename, PATHINFO_EXTENSION);

    if (in_array($ext, explode(',', Yii::app()->params['allowVideoTypes']))) {
      return Yii::app()->videoPlayer;
    }

    if (in_array($ext, explode(',', Yii::app()->params['allowAudioTypes']))) {
      return Yii::app()->audioPlayer;
    }

    return false;
  }
}
