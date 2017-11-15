<?php
/**
 * ViraCMS Core Photo Widget
 *
 * @package vira.core.core
 * @subpackage vira.core.widgets
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VPhotoWidget extends VWidget
{
  const DEFAULT_WIDTH = 370;
  const DEFAULT_HEIGHT = 210;

  public $model;
  public $photoID;
  public $rows;
  public $limit;
  public $imageWidth;
  public $imageHeight;

  public function run()
  {
    $this->render('photo');
  }

  public function getPhoto()
  {
    if ($this->model) {
      return $this->model;
    }

    if ($this->photoID) {
      $criteria = new CDbCriteria();
      $criteria->compare('t.id', $this->photoID);

      $criteria->with = array(
        'images',
      );

      return VPhoto::model()->find($criteria);
    }

    return null;
  }

  public function getImages()
  {
    $images = array();

    if (($model = $this->getPhoto()) != null && count($model->images) > 0) {
      $c = 1;
      foreach ($model->images as $image) {
        if ($image->image) {
          $images[] = $image->image;

          if ($this->limit && ++$c > $this->limit) {
            break;
          }
        }
      }
    }

    return $images;
  }

  public function getWidth()
  {
    return ($width = intval($this->imageWidth)) > 0 ? $width : self::DEFAULT_WIDTH;
  }

  public function getHeight()
  {
    return ($height = intval($this->imageHeight)) > 0 ? $height : self::DEFAULT_HEIGHT;
  }

  public function getCacheKey()
  {
    return 'Vira.Widget.Photo.' . crc32($this->photoID);
  }

  public function getCacheParams()
  {
    return array(
      'varyByLanguage' => true,
    );
  }

  public function getCacheDependency()
  {
    return new VTaggedCacheDependency('Vira.Content.Core.Photo', YII_DEBUG ? 1 : 86400);
  }

  public function getParamsModel()
  {
    Yii::import($this->baseAlias . '.Photo.forms.PhotoWidgetParams');
    return new PhotoWidgetParams();
  }

  public function getConfigView()
  {
    return $this->baseAlias . '.Photo.views.configure';
  }
}
