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
class VCustomNavbarWidgetParams extends VWidgetBaseParams
{
  public $menuID;
  public $position;
  public $fixed;
  public $container;
  public $brand = false;
  public $brandName;
  public $brandImageUrl;

  public function rules()
  {
    return array(
      array('menuID', 'exist', 'className' => 'VCustomMenu', 'attributeName' => 'id', 'allowEmpty' => true),
      array('position', 'in', 'range' => array_keys($this->getPositionOptions()), 'message' => Yii::t('common', 'Invalid value selected.')),
      array('fixed', 'in', 'range' => array_keys($this->getFixedOptions()), 'message' => Yii::t('common', 'Invalid value selected.')),
      array('container', 'in', 'range' => array_keys($this->getContainerOptions()), 'message' => Yii::t('common', 'Invalid value selected.')),
      array('brand', 'boolean'),
      array('brandName', 'length', 'max' => 30),
      array('brandImageUrl', 'url'),
    );
  }

  public function attributeLabels()
  {
    return array(
      'menuID' => Yii::t('common', 'Custom Menu'),
      'brand' => Yii::t('common', 'Show Brand Item'),
      'brandName' => Yii::t('common', 'Brand Name'),
      'brandImageUrl' => Yii::t('common', 'Brand Image URL'),
    );
  }

  public function getFixedOptions()
  {
    return array(
      '' => Yii::t('common', 'None'),
      'navbar-fixed-top' => Yii::t('common', 'Top'),
      'navbar-fixed-bottom' => Yii::t('common', 'Bottom'),
    );
  }

  public function getContainerOptions()
  {
    return array(
      'container' => Yii::t('common', 'Fixed Container'),
      'container-fluid' => Yii::t('common', 'Fluid Container'),
    );
  }

  public function getPositionOptions()
  {
    return array(
      '' => Yii::t('common', 'Container Inner Width'),
      'navbar-block-level' => Yii::t('common', 'Full (100%) Width'),
    );
  }
}
