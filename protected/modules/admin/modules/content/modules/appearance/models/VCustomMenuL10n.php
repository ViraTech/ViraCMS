<?php
/**
 * ViraCMS Custom Menu Item Localization Model
 *
 * @package vira.core.core
 * @subpackage vira.core.content
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 *
 * @property string $itemID menu item identifier
 * @property string $languageID language identifier
 * @property string $title item title
 */
class VCustomMenuL10n extends VActiveRecord
{
  /**
   * @param string $className
   * @return VCustomMenuL10n
   */
  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }

  public function tableName()
  {
    return '{{core_custom_menu_l10n}}';
  }

  public function primaryKey()
  {
    return array(
      'itemID',
      'languageID',
    );
  }

  public function rules()
  {
    return array(
      array('itemID,languageID', 'required'),
      array('itemID', 'length', 'is' => 36),
      array('title', 'length', 'max' => 1022),
      array('languageID', 'length', 'max' => 2),
    );
  }

  public function attributeLabels()
  {
    return array(
      'title' => Yii::t('admin.content.labels', 'Item Title'),
    );
  }
}
