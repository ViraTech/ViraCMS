<?php
/**
 * ViraCMS Core Carousel Widget
 *
 * @package vira.core.core
 * @subpackage vira.core.widgets
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VCarouselWidget extends VWidget
{
  const DEFAULT_WIDTH = 1200;
  const DEFAULT_HEIGHT = 675;

  public $showLatestCarousel = true;
  public $carouselID;
  public $width;
  public $height;

  public function run()
  {
    $this->registerScripts();
    $this->render('carousel');
  }

  public function getCarousel()
  {
    $criteria = new CDbCriteria();

    if ($this->showLatestCarousel) {
      $criteria->compare('t.public', '>0');
      $criteria->order = 't.id DESC';
    }
    else {
      $criteria->compare('t.id', $this->carouselID);
    }

    $criteria->with = array(
      'images',
      'images.currentL10n',
      'images.currentL10n.page',
      'images.image',
    );

    return VCarousel::model()->find($criteria);
  }

  public function getWidth()
  {
    return ($width = intval($this->width)) > 0 ? $width : self::DEFAULT_WIDTH;
  }

  public function getHeight()
  {
    return ($height = intval($this->height)) > 0 ? $height : self::DEFAULT_HEIGHT;
  }

  public function registerScripts()
  {
    $this->registerScript("$('#{$this->id}').carousel();");
  }

  protected function beforeRegisterScript(&$script, &$position, &$htmlOptions)
  {
    $bootstrap = Yii::app()->getComponent('bootstrap');

    if ($bootstrap) {
      $bootstrap->registerJS();
      return true;
    }

    return false;
  }

  public function getCacheKey()
  {
    return 'Vira.Widget.Carousel.' . crc32((int) $this->showLatestCarousel . $this->carouselID);
  }

  public function getCacheParams()
  {
    return array(
      'varyByLanguage' => true,
    );
  }

  public function getCacheDependency()
  {
    return new VTaggedCacheDependency('Vira.Content.Core.Carousel', YII_DEBUG ? 1 : 86400);
  }

  public function getParamsModel()
  {
    Yii::import($this->baseAlias . '.Carousel.forms.CarouselWidgetParams');
    return new CarouselWidgetParams();
  }

  public function getConfigView()
  {
    return $this->baseAlias . '.Carousel.views.configure';
  }
}
