<?php
/**
 * ViraCMS Custom Menu Item Model
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
 * @property string $menuID menu model identifier
 * @property string $parentID parent item identifier
 * @property string $pageID site page identifier
 * @property string $url menu item URL
 * @property string $target item link target attribute
 * @property string $anchor the anchor
 * @property integer $position position in the list
 */
class VCustomMenuItem extends VActiveRecord
{
  /**
   * @param string $className
   * @return VCustomMenuItem
   */
  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }

  public function tableName()
  {
    return '{{core_custom_menu_item}}';
  }

  public function relations()
  {
    return array(
      'menu' => array(self::BELONGS_TO, 'VCustomMenu', 'menuID'),
      'page' => array(self::BELONGS_TO, 'VPage', 'pageID'),
      'l10n' => array(self::HAS_MANY, 'VCustomMenuL10n', 'itemID'),
    );
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

  protected function afterDelete()
  {
    parent::afterDelete();

    if ($this->l10n) {
      foreach ($this->l10n as $l10n) {
        $l10n->delete();
      }
    }
  }
}
