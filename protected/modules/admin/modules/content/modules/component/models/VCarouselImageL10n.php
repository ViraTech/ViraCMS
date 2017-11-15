<?php
/**
 * ViraCMS Core Carousel Image Localization Model
 *
 * @package vira.core.core
 * @subpackage vira.core.bootstrap
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 *
 * @property string $imageID carousel image identifier
 * @property string $languageID language identifier
 * @property string $title image title
 * @property string $caption image text
 * @property string $pageID page identifier image linked to
 * @property string $url url linked to
 */
class VCarouselImageL10n extends VActiveRecord
{
  /**
   * @param string $className
   * @return VCarouselImageL10n
   */
  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }

  public function tableName()
  {
    return '{{core_carousel_image_l10n}}';
  }

  public function primaryKey()
  {
    return array(
      'imageID',
      'languageID',
    );
  }

  public function relations()
  {
    return array(
      'page' => array(self::BELONGS_TO, 'VPage', 'pageID', 'with' => array('l10n' => array('alias' => 'pageL10n'))),
    );
  }

  public function rules()
  {
    return array(
      array('imageID,languageID', 'required'),
      array('imageID', 'length', 'is' => 36),
      array('pageID', 'length', 'is' => 36, 'allowEmpty' => true),
      array('languageID', 'length', 'max' => 2),
      array('title', 'length', 'max' => 255),
      array('caption', 'length', 'max' => 1022),
      array('url', 'length', 'max' => 4094),
    );
  }
}
