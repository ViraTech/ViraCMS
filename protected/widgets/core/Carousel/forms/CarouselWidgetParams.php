<?php
/**
 * ViraCMS Core Carousel Widget Configuration Form
 *
 * @package vira.core.core
 * @subpackage vira.core.widgets
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class CarouselWidgetParams extends VWidgetBaseParams
{
  public $showLatestCarousel = true;
  public $carouselID;
  public $width;
  public $height;
  public $cacheEnabled = true;

  public function rules()
  {
    return array(
      array('showLatestCarousel', 'boolean'),
      array('carouselID', 'length', 'is' => 36, 'allowEmpty' => true),
      array('width,height', 'numerical', 'integerOnly' => true),
      array('cacheEnabled', 'boolean'),
    );
  }

  public function attributeLabels()
  {
    return array(
      'showLatestCarousel' => Yii::t('common', 'Show Latest Carousel'),
      'carouselID' => Yii::t('common', 'Select Carousel'),
      'width' => Yii::t('common', 'Width of the Images (px)'),
      'height' => Yii::t('common', 'Height of the Images (px)'),
      'cacheEnabled' => Yii::t('common', 'Enable automatic cacheing of the widget output'),
    );
  }

  public function attributeHints()
  {
    return array(
      'showLatestCarousel' => Yii::t('common', 'Choose either latest carousel or specify carousel in the drop down field below'),
    );
  }

  protected function afterValidate()
  {
    parent::afterValidate();
    Yii::app()->cache->deleteTag('Vira.Content.Core.Carousel');
  }
}
