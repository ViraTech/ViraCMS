<?php
/**
 * ViraCMS Core Images Model
 *
 * @package vira.core.core
 * @subpackage vira.core.bootstrap
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 *
 * @property string $id primary key
 * @property string $carouselID carousel identifier
 * @property string $imageID image identifier
 * @property integer $position position
 */
class VCarouselImage extends VActiveRecord
{
  /**
   * @param string $className
   * @return VCarouselImage
   */
  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }

  public function behaviors()
  {
    return CMap::mergeArray(
        array(
        'LocalizationBehavior' => array(
          'class' => 'VLocalizationBehavior',
        ),
        'GuidBehavior' => array(
          'class' => 'VGuidBehavior',
        ),
        ), parent::behaviors()
    );
  }

  public function tableName()
  {
    return '{{core_carousel_image}}';
  }

  public function rules()
  {
    return array(
      array('id', 'unsafe', 'except' => 'create'),
      array('carouselID,imageID', 'required'),
      array('id,carouselID,imageID', 'length', 'is' => 36),
      array('position', 'numerical', 'integerOnly' => true),
    );
  }

  public function relations()
  {
    return array(
      'image' => array(self::BELONGS_TO, 'VContentImage', 'imageID'),
      'l10n' => array(self::HAS_MANY, 'VCarouselImageL10n', 'imageID'),
      'currentL10n' => array(
        self::HAS_ONE,
        'VCarouselImageL10n',
        'imageID',
        'on' => 'currentL10n.languageID=:currentLanguage',
        'params' => array(
          ':currentLanguage' => Yii::app()->getLanguage()
        ),
      ),
    );
  }

  protected function afterDelete()
  {
    parent::afterDelete();

    if ($this->image) {
      $this->image->setScenario('auto');
      $this->image->delete();
    }

    if ($this->l10n) {
      foreach ($this->l10n as $l10n) {
        $l10n->delete();
      }
    }
  }
}
