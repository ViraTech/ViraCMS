<?php
/**
 * ViraCMS Custom Menu Widget Configuration Form
 *
 * @package vira.core.core
 * @subpackage vira.core.widgets
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VCustomMenuWidgetParams extends VWidgetBaseParams
{
  public $menuID;
  public $menuType = VCustomMenuWidget::DEFAULT_MENU_TYPE;

  public function rules()
  {
    return array(
      array('menuID', 'exist', 'className' => 'VCustomMenu', 'attributeName' => 'id', 'allowEmpty' => true),
      array('menuType', 'in', 'range' => array_keys($this->getMenuTypes()), 'message' => Yii::t('common', 'Invalid value selected.')),
    );
  }

  public function attributeLabels()
  {
    return array(
      'menuID' => Yii::t('common', 'Custom Menu'),
      'menuType' => Yii::t('common', 'Menu Type'),
    );
  }

  public function getMenuTypes()
  {
    return array(
      VCustomMenuWidget::MENU_TYPE_NAV => Yii::t('common', 'Nav'),
      VCustomMenuWidget::MENU_TYPE_NAV_LIST => Yii::t('common', 'Nav List'),
      VCustomMenuWidget::MENU_TYPE_NAV_PILLS => Yii::t('common', 'Nav Pills'),
      VCustomMenuWidget::MENU_TYPE_NAV_PILLS_STACKED => Yii::t('common', 'Stacked Pills'),
      VCustomMenuWidget::MENU_TYPE_NAV_TABS => Yii::t('common', 'Nav Tabs'),
      VCustomMenuWidget::MENU_TYPE_NAV_TABS_STACKED => Yii::t('common', 'Stacked Tabs'),
    );
  }
}
