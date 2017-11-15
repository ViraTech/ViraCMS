<?php
/**
 * ViraCMS Core Event Handlers
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VCoreEventHandler extends VEventHandler
{
  /**
   * Tracking cookie name
   */
  const TRACK_COOKIE_NAME = 'XViraTrack';

  /**
   * @var boolean unique visitor trigger
   */
  static $_unique = true;

  /**
   * Fire for public controllers, set site language
   * @param CEvent $event
   */
  public function setSiteLanguage($event)
  {
    if (($language = Yii::app()->request->getParam('lang', Yii::app()->getComponent('user')->getState('Vira.Current.Language'))) != null) {
      $language = substr($language, 0, 2);
      Yii::app()->setLanguage($language);
      Yii::app()->user->setState('Vira.Current.Language', $language);
    }
  }

  /**
   * Fire when database connection has opened
   * @param CEvent $event
   */
  public function setSqlTimeZone($event)
  {
    if (isset($event->params['driver']) && $event->params['driver'] == 'mysql' && $event->params['pdo'] instanceof PDO) {
      $event->params['pdo']->exec("SET time_zone = '" . Yii::app()->format->formatTimezoneOffset() . "';");
    }
  }

  /**
   * Show applicaiton license key in the server headers
   * @param CEvent $event event
   */
  public function showLicenseKey($event)
  {
    header('X-Vira-License:' . Yii::app()->licenseKey);
  }

  /**
   * Determine if visitor is unique for today
   * @param CEvent $event event
   */
  public function determineUniqueVisitor($event)
  {
    $r = Yii::app()->request;
    self::$_unique = isset($r->cookies[self::TRACK_COOKIE_NAME]) ? false : true;
    if (self::$_unique) {
      $cookie = new CHttpCookie(self::TRACK_COOKIE_NAME, md5(time() . rand(1, 100)));
      $cookie->path = '/';
      $cookie->httpOnly = true;
      $cookie->expire = strtotime(gmdate('Y-m-d 23:59:59'));
      $r->cookies[self::TRACK_COOKIE_NAME] = $cookie;
    }
  }

  /**
   * Update database to log current request
   * @param CEvent $event event
   */
  public function logRequest($event)
  {
    $r = Yii::app()->request;
    $siteID = Yii::app()->site->id;
    $logged = true;

    try {
      $set = 't.requests=t.requests+1';
      if (!self::$_unique) {
        $set .= ',t.users=t.users+1';
      }
      $cmd = Yii::app()->db->createCommand("UPDATE {{core_request_stat}} t SET {$set} WHERE t.siteID=:siteID AND t.date=:date");
      $logged = @$cmd->execute(array(
          ':date' => date('Y-m-d'),
          ':siteID' => Yii::app()->site->id,
        )) > 0;
    }
    catch (Exception $e) {
      $logged = false;
    }

    if (!$logged) {
      try {
        $params = array(
          'siteID' => $siteID,
          'date' => date('Y-m-d'),
          'users' => self::$_unique ? 1 : 0,
          'requests' => 1,
        );
        Yii::app()->db->createCommand()->insert('{{core_request_stat}}', $params);
      }
      catch (Exception $e) {
      }
    }
  }
}
