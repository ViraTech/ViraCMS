<?php
/**
 * ViraCMS Site Search Widget
 *
 * @package vira.core.core
 * @subpackage vira.core.widgets
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VSiteSearchWidget extends VWidget
{
  public $size = 'medium';

  public function run()
  {
    $this->render('site-search');
  }

  public function getCacheKey()
  {
    return 'Vira.Widget.SiteSearch';
  }

  public function getCacheParams()
  {
    return array(
      'varyByLanguage' => true,
    );
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
