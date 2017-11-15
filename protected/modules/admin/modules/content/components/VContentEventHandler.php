<?php
/**
 * ViraCMS Content Event Handlers
 *
 * @package vira.core.core
 * @subpackage vira.core.content
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VContentEventHandler extends VEventHandler
{
  /**
   * Delete all of site related content models
   * @param CEvent $event the event
   */
  public function deleteSiteContent($event)
  {
    $this->deleteRelated($event, 'siteID', array(
      'VCustomMenu',
      'VSiteLayout',
      'VLayoutArea',
      'VPage',
      'VPageRow',
      'VSystemPage',
      'VContentFile',
      'VContentImage',
      'VContentMedia',
    ));

    $list = Yii::app()->db->
      createCommand()->
      select('*')->
      from('{{core_site_admin_access}}')->
      queryAll();
    $admins = array();

    foreach ($list as $record) {
      $admins[$record['adminID']][] = $record['siteID'];
    }

    foreach ($admins as $adminID => $sites) {
      if (in_array($event->sender->id, $sites) && count($sites) < 2) {
        $admin = VSiteAdmin::model()->findByPk($adminID);
        if ($admin != null) {
          $admin->delete();
        }
      }
    }

    VCacheHelper::flushAssetsCache();
  }

  /**
   * Delete all of page related content models
   * @param CEvent $event the event
   */
  public function deletePageContent($event)
  {
    $this->deleteRelated($event, 'pageID', array(
      'VPageRow',
      'VPageBlock',
    ));
  }

  /**
   * Delete layout related models
   * @param CEvent $event the event
   */
  public function deleteLayoutContent($event)
  {
    $this->deleteRelated($event, array(
      'siteID => siteID',
      'layoutID => id',
    ), array(
      'VPageRow',
      'VPageBlock',
    ));

    $this->deleteRelated($event, array(
      'id => bodyBackgroundImage',
    ), array(
      'VContentImage',
    ));
  }

  /**
   * Delete page area related models
   * @param CEvent the event
   */
  public function deletePageArea($event)
  {
    $this->deleteRelated($event, 'pageAreaID', array(
      'VPageRow',
    ));
  }

  /**
   * Delete page row related models
   * @param CEvent the event
   */
  public function deletePageRow($event)
  {
    if (!empty($event->sender->template)) {
      if (preg_match_all('/###VIRA#(\w{8}-\w{4}-\w{4}-\w{4}-\w{12})###/', $event->sender->template, $blocks)) {
        foreach (VPageBlock::model()->findAllByPk($blocks[1]) as $block) {
          $block->delete();
        }
      }
    }
  }

  /**
   * Delete related models
   * @param CEvent the event
   * @param string $attributes attributes to compare sender's primary key
   * @param array $related class names
   * @param string $prefix table prefix, defaults to 't'
   */
  protected function deleteRelated($event, $attributes, $related, $prefix = 't')
  {
    $criteria = new CDbCriteria();
    $primaryKey = $event->sender->getPrimaryKey();
    $prefix = $prefix ? $prefix . '.' : '';

    if (is_array($attributes)) {
      foreach ($attributes as $remoteAttribute => $localAttribute) {
        if ($event->sender->hasAttribute($localAttribute)) {
          $criteria->compare(
            $prefix . $remoteAttribute, $event->sender->getAttribute($localAttribute)
          );
        }
      }
    }
    else {
      $criteria->compare($prefix . $attributes, $primaryKey);
    }

    foreach ($related as $className) {
      $class = new $className();
      foreach ($class->findAll($criteria) as $record) {
        $record->delete();
      }
    }
  }
}
