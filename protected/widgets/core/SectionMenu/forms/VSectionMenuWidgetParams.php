<?php
/**
 * ViraCMS Site Section Menu Widget Configuration Form
 *
 * @package vira.core.core
 * @subpackage vira.core.widgets
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VSectionMenuWidgetParams extends VWidgetBaseParams
{
  public $addParentPage = false;
  public $parentPageID;
  public $menuType = VSectionMenuWidget::DEFAULT_MENU_TYPE;

  public function rules()
  {
    return array(
      array('addParentPage', 'boolean'),
      array('parentPageID', 'exist', 'className' => 'VPage', 'attributeName' => 'id'),
      array('menuType', 'in', 'range' => array_keys($this->getMenuTypes()), 'message' => Yii::t('common', 'Invalid value selected.')),
    );
  }

  public function attributeLabels()
  {
    return array(
      'addParentPage' => Yii::t('common', 'Add Parent Page to the Menu'),
      'parentPageID' => Yii::t('common', 'Parent Page'),
      'menuType' => Yii::t('common', 'Menu Type'),
    );
  }

  public function getMenuTypes()
  {
    return array(
      VSectionMenuWidget::MENU_TYPE_NAV => Yii::t('common', 'Nav'),
      VSectionMenuWidget::MENU_TYPE_NAV_LIST => Yii::t('common', 'Nav List'),
      VSectionMenuWidget::MENU_TYPE_NAV_PILLS => Yii::t('common', 'Nav Pills'),
      VSectionMenuWidget::MENU_TYPE_NAV_PILLS_STACKED => Yii::t('common', 'Stacked Pills'),
      VSectionMenuWidget::MENU_TYPE_NAV_TABS => Yii::t('common', 'Nav Tabs'),
      VSectionMenuWidget::MENU_TYPE_NAV_TABS_STACKED => Yii::t('common', 'Stacked Tabs'),
    );
  }
}
