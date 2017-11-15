<?php
/**
 * ViraCMS System Page View Model
 *
 * @package vira.core.core
 * @subpackage vira.core.content
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 *
 * @property string $module module name
 * @property string $controller controller name
 * @property string $view view name
 * @property string $title title
 * @property string $translate translate category
 */
class VSystemView extends VActiveRecord
{
  /**
   * @param string $className
   * @return VSystemView
   */
  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }

  public function tableName()
  {
    return '{{core_system_view}}';
  }

  public function primaryKey()
  {
    return array(
      'module',
      'controller',
      'view',
    );
  }

  public function getTitle()
  {
    return $this->translate ? Yii::t($this->translate, $this->title) : $this->title;
  }
}
