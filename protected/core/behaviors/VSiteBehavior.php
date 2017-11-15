<?php
/**
 * ViraCMS Multiple Sites Behavior
 * Used in both VWebApplication & VConsoleApplication
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VSiteBehavior extends CBehavior
{
  /**
   * @var VSite current site
   */
  private $_site;

  /**
   * Set provided site as current
   * @param mixed $site site model or site primary key (identifier)
   */
  public function setSite($site)
  {
    if ($site instanceof VSite) {
      $this->_site = $site;
    }
    else {
      $site = VSite::model()->findByPk($site);
      if ($site) {
        $this->_site = $site;
      }
    }
  }

  /**
   * Return current site
   * @return VSite site model
   * @throws CHttpException
   */
  public function getSite()
  {
    if (empty($this->_site)) {
      $host = isset($_SERVER['HTTP_HOST']) ? strtolower($_SERVER['HTTP_HOST']) : null;

      $hosts = Yii::app()->hasComponent('cache') ? Yii::app()->cache->get('Vira.SiteHosts') : false;
      if ($hosts === false) {
        $hosts = array();
        foreach (VSite::model()->findAll() as $site) {
          foreach (explode("\n", $site->domains) as $domain) {
            $hosts[$domain] = $site->id;
          }
          if ($site->host) {
            $hosts[$site->host] = $site->id;
          }
        }
        if (Yii::app()->hasComponent('cache')) {
          Yii::app()->cache->set(
            'Vira.SiteHosts', $hosts, Yii::app()->params['defaultCacheDuration'],
            new VTaggedCacheDependency('Vira.Site', Yii::app()->params['defaultCacheTagDuration'])
          );
        }
      }

      $model = VSite::model()->cache(
        Yii::app()->params['defaultCacheDuration'], new VTaggedCacheDependency(
        'Vira.Site', Yii::app()->params['defaultCacheTagDuration']
        )
      );

      if (isset($hosts[$host])) {
        $this->_site = $model->findByPk($hosts[$host]);
      }
      else {
        $this->_site = $model->findByAttributes(array('default' => true));
      }

      if ($this->_site) {
        if (!isset($hosts[$host]) && $this->_site->redirect) {
          Yii::app()->getController()->redirect('http://' . $this->_site->host . '/');
        }

        $webroot = Yii::getPathOfAlias('webroot');
        $updated = false;
        if (!$this->_site->default && !$this->_site->host) {
          $this->_site->host = $host;
          $updated = true;
        }
        if (!$this->_site->webroot || $webroot != $this->_site->webroot) {
          $this->_site->webroot = $webroot;
          $updated = true;
        }
        if ($updated) {
          $this->_site->setScenario('auto');
          $this->_site->save();
        }
      }
      else {
        throw new CHttpException(400, 'No sites defined.');
      }
    }

    return $this->_site;
  }
}
