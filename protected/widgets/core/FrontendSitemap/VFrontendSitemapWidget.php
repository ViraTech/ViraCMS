<?php
/**
 * ViraCMS Frontend Site Map Widget
 *
 * @package vira.core.core
 * @subpackage vira.core.widgets
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VFrontendSitemapWidget extends VWidget
{
  public function run()
  {
    $this->render('site-map');
  }

  public function getCacheKey()
  {
    return 'Vira.Widget.SiteMap';
  }

  public function getCacheParams()
  {
    return array(
      'varyByExpression' => 'Yii::app()->site->id',
      'varyByLanguage' => true,
    );
  }

  public function getCacheDependency()
  {
    return new VTaggedCacheDependency('Vira.Pages', 86400);
  }

  public function getParamsModel()
  {
    return null;
  }

  public function getConfigView()
  {
    return '';
  }
}
