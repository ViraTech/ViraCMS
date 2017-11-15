<?php
/**
 * ViraCMS Core Carousel Localization Model
 *
 * @package vira.core.core
 * @subpackage vira.core.bootstrap
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 *
 * @property string $carouselID carousel identifier
 * @property string $languageID language identifier
 * @property string $title carousel title
 */
class VCarouselL10n extends VActiveRecord
{
  /**
   * @param string $className
   * @return VCarouselL10n
   */
  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }

  public function tableName()
  {
    return '{{core_carousel_l10n}}';
  }

  public function primaryKey()
  {
    return array(
      'carouselID',
      'languageID',
    );
  }

  public function rules()
  {
    return array(
      array('carouselID,languageID', 'required'),
      array('title', 'required', 'message' => Yii::t('admin.content.errors', '{attribute} must be set for the language "{language}".', array('{language}' => VLanguageHelper::getLanguageTitle($this->languageID)))),
      array('carouselID', 'length', 'is' => 36),
      array('languageID', 'length', 'max' => 2),
      array('title', 'length', 'max' => 255),
    );
  }

  public function attributeLabels()
  {
    return array(
      'title' => Yii::t('admin.content.labels', 'Title'),
    );
  }
}
