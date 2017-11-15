<?php
/**
 * ViraCMS Administrator Account Event Handlers
 *
 * @package vira.core.core
 * @subpackage vira.core.registry
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VAdminAccountEventHandler extends VEventHandler
{
  /**
   * Password restore request event handler
   * Send mail to account' e-mail address and add this action to the security log
   * @param CEvent $event the event
   */
  public function restorePasswordRequest($event)
  {
    if (!empty($event->params['account'])) {
      $params = array(
        '{siteName}' => Yii::app()->site->name,
        '{userName}' => empty($event->params['account']->name) ? Yii::t('common', 'unknown user') : $event->params['account']->name,
        '{url}' => Yii::app()->createAbsoluteUrl('/admin/auth/restore', array('_' => $event->sender->id)),
        '{siteAdminEmail}' => Yii::app()->params['adminEmail'],
        '{siteUrl}' => Yii::app()->createAbsoluteUrl('/site/index'),
        '{activeBefore}' => Yii::app()->format->formatDatetime($event->sender->expire),
      );
      if (($template = Yii::app()->mailer->getTemplate('registry', 'restorePassword')) !== null) {
        Yii::app()->mailer->send($event->sender->email, $template['subject'], $template['body'], $params, $template['isHtml']);
      }
      Yii::app()->systemLog->logAuth(VAuthLogTypeCollection::ACCESS_REQUESTED, true, VAccountTypeCollection::ADMINISTRATOR, $event->params['account']->id);
    }
  }

  /**
   * Change password with restore function
   * Send mail with new password to account' e-mail and add this action to the security log
   * @param CEvent $event the event
   */
  public function restorePasswordChange($event)
  {
    if (!empty($event->params['password'])) {
      $params = array(
        '{siteName}' => Yii::app()->site->name,
        '{userName}' => empty($event->sender->name) ? Yii::t('common', 'unknown user') : $event->sender->name,
        '{url}' => Yii::app()->createAbsoluteUrl('/admin/auth/login'),
        '{username}' => $event->sender->username,
        '{password}' => $event->params['password'],
        '{siteUrl}' => Yii::app()->createAbsoluteUrl('/site/index'),
        '{siteAdminEmail}' => Yii::app()->params['adminEmail'],
      );
      if (($template = Yii::app()->mailer->getTemplate('registry', 'newPassword')) !== null) {
        Yii::app()->mailer->send($event->sender->email, $template['subject'], $template['body'], $params, $template['isHtml']);
      }
      Yii::app()->systemLog->logAuth(VAuthLogTypeCollection::ACCESS_GRANTED, true, VAccountTypeCollection::ADMINISTRATOR, $event->sender->id);
    }
  }

  /**
   * Fired when error occurred while password restoration
   * Add this fact to the security log
   * @param CEvent $event event
   */
  public function restorePasswordChangeError($event)
  {
    Yii::app()->systemLog->logAuth(VAuthLogTypeCollection::ACCESS_GRANTED, false, VAccountTypeCollection::ADMINISTRATOR, $event->sender->id);
  }

  /**
   * Add successful login event to the security log
   * @param CEvent $event event
   */
  public function login($event)
  {
    Yii::app()->systemLog->logAuth(VAuthLogTypeCollection::LOGIN, true);
  }

  /**
   * Add login unsuccessful attempt to the security log (only when provided existing account' e-mail address or username)
   * @param CEvent $event event
   */
  public function loginError($event)
  {
    if (!empty($event->params['account'])) {
      Yii::app()->systemLog->logAuth(VAuthLogTypeCollection::LOGIN, false, VAccountTypeCollection::ADMINISTRATOR, $event->params['account']->id);
    }
  }

  /**
   * Add successful logout event to the security log
   * @param CEvent $event event
   */
  public function logout($event)
  {
    Yii::app()->systemLog->logAuth(VAuthLogTypeCollection::LOGOUT, true);
  }
}
