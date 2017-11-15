<?php
/**
 * ViraCMS Photo Gallery Image Model
 *
 * @package vira.core.core
 * @subpackage vira.core.bootstrap
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 *
 * @property string $ownerID the owner model identifier
 * @property string $imageID the uploaded image identifier
 * @property string $title the image title
 * @property integer $sort the sorting index
 */
class VPhotoImage extends VActiveRecord
{
  /**
   * @var boolean delete flag
   */
  public $deleteFlag = false;

  /**
   * @param string $className
   * @return VPhotoImage
   */
  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }

  /**
   * @inheritdoc
   */
  public function tableName()
  {
    return '{{core_photo_image}}';
  }

  /**
   * @inheritdoc
   */
  public function primaryKey()
  {
    return array(
      'ownerID',
      'imageID'
    );
  }

  /**
   * @inheritdoc
   */
  public function relations()
  {
    return array(
      'photo' => array(self::BELONGS_TO, 'VPhoto', 'ownerID'),
      'image' => array(self::BELONGS_TO, 'VContentImage', 'imageID'),
    );
  }

  /**
   * @inheritdoc
   */
  protected function afterDelete()
  {
    parent::afterDelete();

    if ($this->image) {
      $this->image->delete();
    }
  }

  /**
   * @inheritdoc
   */
  public function rules()
  {
    return array(
      array('ownerID,imageID', 'required'),
      array('ownerID,imageID', 'length', 'min' => 36, 'max' => 36),
      array('sort', 'numerical', 'integerOnly' => true),
      array('title', 'length', 'max' => 1022),
      array('deleteFlag', 'boolean'),
    );
  }
}
