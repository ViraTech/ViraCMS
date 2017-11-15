<?php
/**
 * ViraCMS Advanced Site Menu Widget Configuration Form
 *
 * @package vira.core.core
 * @subpackage vira.core.widgets
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class AdvancedSiteMenuWidgetParams extends VWidgetBaseParams
{
  public $menuID;
  public $parentID;
  public $children = false;
  public $plain = true;
  public $cssClass = 'nav';

  public function rules()
  {
    return array(
      array('cssClass', 'length', 'max' => 255),
      array('plain,children', 'boolean'),
      array('menuID,parentID', 'length', 'max' => 36),
    );
  }

  public function attributeLabels()
  {
    return array(
      'menuID' => Yii::t('common', 'Custom Menu'),
      'parentID' => Yii::t('common', 'Parent Menu Item'),
      'children' => Yii::t('common', 'Add Children Items'),
      'plain' => Yii::t('common', 'Plain Menu'),
      'cssClass' => Yii::t('common', 'CSS Class'),
    );
  }

  public function attributeHints()
  {
    return array(
      'menuID' => Yii::t('common', 'Select either custom menu or parent menu item'),
      'parentID' => Yii::t('common', 'Item from which menu will be build'),
      'plain' => Yii::t('common', 'Render plain one-level menu'),
      'children' => Yii::t('common', 'Render chilren menu items as well'),
    );
  }

  public function getAttributeHint($attribute)
  {
    $hints = $this->attributeHints();

    return isset($hints[$attribute]) ? $hints[$attribute] : '';
  }
}
