<?php
/**
 * ViraCMS Core Photo Widget Configuration Form
 *
 * @package vira.core.core
 * @subpackage vira.core.widgets
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class PhotoWidgetParams extends VWidgetBaseParams
{
  const ROWS_1 = 'span12';
  const ROWS_2 = 'span6';
  const ROWS_3 = 'span4';
  const ROWS_4 = 'span3';
  const ROWS_6 = 'span2';
  const ROWS_12 = 'span1';
  const DEFAULT_ROWS = 'span3';

  public $photoID;
  public $rows = self::DEFAULT_ROWS;
  public $limit;
  public $imageWidth;
  public $imageHeight;

  public function rules()
  {
    return array(
      array('photoID', 'exist', 'className' => 'VPhoto', 'attributeName' => 'id'),
      array('imageWidth,imageHeight,limit', 'numerical', 'integerOnly' => true),
      array('rows', 'in', 'range' => array_keys($this->getRows())),
    );
  }

  public function attributeLabels()
  {
    return array(
      'photoID' => Yii::t('common', 'Select Photo'),
      'imageWidth' => Yii::t('common', 'Width of the Images (px)'),
      'imageHeight' => Yii::t('common', 'Height of the Images (px)'),
      'limit' => Yii::t('common', 'Max. number of the Images'),
      'rows' => Yii::t('common', 'Number of the Images in a row'),
    );
  }

  protected function afterValidate()
  {
    parent::afterValidate();
    Yii::app()->cache->deleteTag('Vira.Content.Core.Photo');
  }

  public function getRows()
  {
    return array(
      self::ROWS_1 => Yii::t('admin.widgets', '1 in a row'),
      self::ROWS_2 => Yii::t('admin.widgets', '2 in a row'),
      self::ROWS_3 => Yii::t('admin.widgets', '3 in a row'),
      self::ROWS_4 => Yii::t('admin.widgets', '4 in a row'),
      self::ROWS_6 => Yii::t('admin.widgets', '6 in a row'),
      self::ROWS_12 => Yii::t('admin.widgets', '12 in a row'),
    );
  }
}
