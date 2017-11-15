<?php
/**
 * ViraCMS Administrator Dashboard Controller
 *
 * @package vira.core.core
 * @subpackage vira.core.admin
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class DefaultController extends VSystemController
{
  public $layout = 'dashboard';

  public function accessRules()
  {
    return CMap::mergeArray(
        array(
        array(
          'allow',
          'actions' => array(
            'index',
          ),
          'roles' => array_keys(Yii::app()->authManager->getAdminRoles()),
        ),
        ), parent::accessRules()
    );
  }

  public function actionIndex()
  {
    $r = Yii::app()->request;
    if ($r->isAjaxRequest) {
      if ($r->getParam('range')) {
        switch ($r->getParam('range')) {
          case 'year':
            $period = array(time() - 31536000, time());
            break;
          case 'halfyear':
            $period = array(time() - 15768000, time());
            break;
          case 'month':
            $period = array(time() - 2678400, time());
            break;
          default:
            $period = null;
        }
        echo CJSON::encode(array(
          'stats' => array_values($this->generateStats($period)),
        ));
      }
      Yii::app()->end();
    }

    $this->setPageTitle(Yii::t('admin.titles', 'Dashboard'));
    $this->render('index', array(
      'stats' => $this->generateStats(),
      'events' => $this->getEvents(),
    ));
  }

  private function getEvents()
  {
    $criteria = new CDbCriteria;
    $criteria->compare('t.siteID', Yii::app()->site->id);
    $criteria->order = 't.time DESC';
    $criteria->limit = 10;

    return VLogEvent::model()->findAll($criteria);
  }

  private function generateStats($period = null)
  {
    if ($period === null) {
      $period[0] = time() - 604800;
      $period[1] = time();
    }

    $cacheKey = 'Vira.Statistic.' . Yii::app()->site->id . '.' . $period[0] . '.' . $period[1];
    $stats = Yii::app()->cache->get($cacheKey);
    if ($stats === false) {
      $stats = array();

      if ($period[1] - $period[0] > 2678400) {
        // half-year, year
        for ($i = $period[0]; $i <= $period[1]; $i += 2628000) {
          $month = date('n', $i);
          $stats[date('Y-m', $i)]['period'] = Yii::app()->format->formatMonth($month, VFormatter::FORMAT_LOWERCASED) . ' ' . date('Y', $i);
          $stats[date('Y-m', $i)]['requests'] = 0;
          $stats[date('Y-m', $i)]['visitors'] = 0;
        }

        $command = Yii::app()->db->createCommand();
        $command->
          select('DATE_FORMAT(t.date,"%Y-%m") AS _dt,SUM(t.requests) AS _c,SUM(t.users) AS _u')->
          from('{{core_request_stat}} t')->
          where("t.date BETWEEN '" . date('Y-m-d', $period[0]) . "' AND '" . date('Y-m-d', $period[1]) . "' AND t.siteID=:siteID", array(
            ':siteID' => Yii::app()->site->id,
          ))->
          group('_dt');

        $query = $command->queryAll();
        foreach ($query as $row) {
          $stats[$row['_dt']]['requests'] = $row['_c'];
          $stats[$row['_dt']]['visitors'] = $row['_u'];
        }
      }
      else {
        // week, month
        for ($i = $period[0]; $i <= $period[1]; $i += 86400) {
          $stats[date('Y-m-d', $i)]['period'] = Yii::t('common', substr(date('D', $i), 0, 2)) . ', ' . Yii::app()->format->formatDate($i);
          $stats[date('Y-m-d', $i)]['requests'] = 0;
          $stats[date('Y-m-d', $i)]['visitors'] = 0;
        }

        $command = Yii::app()->db->createCommand();
        $command->
          select('t.date AS _dt,SUM(t.requests) AS _c,SUM(t.users) AS _u')->
          from('{{core_request_stat}} t')->
          where("t.date BETWEEN '" . date('Y-m-d', $period[0]) . "' AND '" . date('Y-m-d', $period[1]) . "' AND t.siteID=:siteID", array(
            ':siteID' => Yii::app()->site->id,
          ))->
          group('_dt');

        $query = $command->queryAll();
        foreach ($query as $row) {
          $stats[$row['_dt']]['requests'] = $row['_c'];
          $stats[$row['_dt']]['visitors'] = $row['_u'];
        }
      }

      Yii::app()->cache->set($cacheKey, $stats, Yii::app()->params['defaultCacheDuration']);
    }

    return $stats;
  }
}
