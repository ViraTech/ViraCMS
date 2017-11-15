<?php
/**
 * ViraCMS Page Area & Site Layout Relationship Model
 *
 * @package vira.core.core
 * @subpackage vira.core.content
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 *
 * @property string $pageAreaID page area identifier
 * @property string $siteID site identifier
 * @property string $layoutID layout identifier
 */
class VLayoutArea extends VActiveRecord
{
  /**
   * @param string $className
   * @return VLayoutArea
   */
  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }

  public function tableName()
  {
    return '{{core_layout_area}}';
  }

  public function primaryKey()
  {
    return array(
      'pageAreaID',
      'siteID',
      'layoutID',
    );
  }

  public function relations()
  {
    return array(
      'site' => array(self::BELONGS_TO, 'VSite', 'siteID'),
      'pageArea' => array(self::BELONGS_TO, 'VPageArea', 'pageAreaID'),
      'layout' => array(self::BELONGS_TO, 'VSiteLayout', 'layoutID'),
    );
  }

  public function findArea($siteID, $layoutID)
  {
    $criteria = new CDbCriteria();
    $criteria->compare('t.siteID', $siteID);
    $criteria->compare('t.layoutID', $layoutID);
    $criteria->with = array(
      'pageArea',
    );
    $criteria->order = $this->quoteColumn('pageArea.position') . ' ASC';

    return self::model()->findAll($criteria);
  }
}
