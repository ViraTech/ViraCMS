<?php
/**
 * ViraCMS HTTP Request Statistics Model
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 *
 * @property string $siteID site identifier
 * @property string $date date
 * @property integer $users visitors counter
 * @property integer $requests page views counter
 */
class VRequestStat extends VActiveRecord
{
  /**
   * @param string $className
   * @return VRequestStat
   */
  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }

  public function tableName()
  {
    return '{{core_request_stat}}';
  }

  public function primaryKey()
  {
    return array(
      'siteID',
      'date',
    );
  }
}
