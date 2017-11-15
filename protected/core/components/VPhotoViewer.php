<?php
/**
 * ViraCMS Photo Viewer Component
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VPhotoViewer extends VApplicationComponent
{
  const DEFAULT_WIDTH = 380;
  const DEFAULT_HEIGHT = 380;

  /**
   * @var string the photo widget class name
   */
  public $photoWidgetClass;

  /**
   * @var string the photo widget additional params
   */
  public $photoWidgetParams = array();

  /**
   * @inheritdoc
   */
  public function init()
  {
    parent::init();
    $this->createWidget();
  }

  /**
   * Renders single image or the gallery for multiple images
   * @param mixed $owner the widget's caller object reference
   * @param array $images the images list
   * @param integer $width the thumbnail width
   * @param integer $height the thumbnail height
   * @param array $params additional widget params
   */
  public function renderImages($owner, $images, $width = self::DEFAULT_WIDTH, $height = self::DEFAULT_HEIGHT, $params = array())
  {
    $this->photoWidgetParams = CMap::mergeArray($this->photoWidgetParams, $params);
    $widget = $this->createWidget();

    if ($widget instanceof VPhotoViewerWidgetInterface) {
      $widget->setOwner($owner);
      $widget->setImages($this->formatImages($widget, $images, $width, $height));
      $widget->run();
    }
  }

  /**
   * Formatting the images to widget format. Tries to determine type of the list
   * @param mixed $widget the widget
   * @param array $images the images list
   * @param integer $width the thumbnail width
   * @param integer $height the thumbnail height
   * @return array
   */
  protected function formatImages($widget, $images, $width, $height)
  {
    $items = array();

    foreach ($images as $image) {
      $item = array(
        'title' => '',
        'description' => '',
      );

      if (is_string($image)) {
        $item['url'] = $image;
        $item['thumbnail'] = $image;
      }
      elseif (is_array($image) && isset($image['url'])) {
        $item = CMap::mergeArray($item, $image);
      }
      elseif ($image instanceof VContentImage) {
        $item['url'] = $image->getUrl();
        $item['thumbnail'] = $image->getUrl($width, $height, true);
        $item['title'] = $image->comment;
      }
      elseif ($image instanceof VActiveRecord && $image->hasRelation('image') && $image->image) {
        $item['thumbnail'] = $image->image->getUrl($width, $height, true);

        if (method_exists($image, 'createUrl')) {
          $item['url'] = $image->createUrl();
          $item['image'] = $image->image->getUrl();
        }
        else {
          $item['url'] = $image->image->getUrl();
        }

        if (!empty($image->title)) {
          $item['title'] = $image->title;
        }

        if (!empty($image->description)) {
          $item['description'] = $image->description;
        }
      }

      $items[] = $item;
    }

    return $items;
  }

  /**
   * Creates the widget
   * @return mixed
   */
  protected function createWidget()
  {
    $widget = null;

    if ($this->photoWidgetClass) {
      $config = $this->photoWidgetParams;
      $config['class'] = $this->photoWidgetClass;

      $widget = Yii::createComponent($config);
    }

    if (is_object($widget) && method_exists($widget, 'init')) {
      $widget->init();
    }

    return $widget;
  }
}
