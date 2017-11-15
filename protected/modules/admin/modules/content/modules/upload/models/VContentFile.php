<?php
/**
 * ViraCMS Storage File Model
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
 * @property string $mime file MIME type
 * @property integer $size file size
 * @property string $path file path stored to
 * @property string $comment file' comment
 */
class VContentFile extends VContentActiveRecord
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
          'createMessage' => 'File [{id}] "{file}" has been created',
          'createParams' => array('{id}' => '$this->id', '{file}' => '$this->filename'),
          'updateMessage' => 'File [{id}] "{file}" has been updated',
          'updateParams' => array('{id}' => '$this->id', '{file}' => '$this->filename'),
          'deleteMessage' => 'File [{id}] "{file}" has been removed',
          'deleteParams' => array('{id}' => '$this->id', '{file}' => '$this->filename'),
          'translateCategory' => 'admin.content.events',
        ),
      ));
    }
  }

  /**
   * @param type $className
   * @return VContentFile
   */
  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }

  public function tableName()
  {
    return '{{core_content_file}}';
  }

  public function rules()
  {
    return array(
      array('id', 'unsafe', 'except' => 'create'),
      array('id,siteID', 'length', 'is' => 36),
      array('filename,path,mime', 'required', 'on' => 'update'),
      array('upload', 'required', 'on' => 'create'),
      array('filename,path,comment', 'length', 'max' => 1022),
      array('className,primaryKey,mime', 'length', 'max' => 255),
      array('path', 'length', 'max' => 4094),
      array('size', 'numerical', 'integerOnly' => true),
      array('path,mime,size', 'unsafe'),
      array('id,siteID,className,primaryKey,filename,path,comment', 'safe', 'on' => 'search'),
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
      'upload' => Yii::t('admin.content.labels', 'Select File'),
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
    $criteria->compare('t.id', $this->id, true);
    $criteria->compare('t.className', $this->className);
    if (is_array($this->primaryKey)) {
      $this->primaryKey = implode('_', $this->primaryKey);
    }
    $criteria->compare('t.primaryKey', $this->primaryKey);
    $criteria->compare('t.filename', $this->filename, true);
    $criteria->compare('t.path', $this->path, true);
    $criteria->compare('t.comment', $this->comment, true);
    $criteria->compare('t.size', $this->size, true);
    $criteria->compare('t.mime', $this->mime, true);

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
   * @return string file URL
   */
  public function getUrl($absolute = false)
  {
    return Yii::app()->storage->getFileUrl($this->path, $absolute);
  }
}
