<?php
/**
 * ViraCMS Page's Title & Breadcrumbs Widget
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VBreadcrumbsWidget extends VWidget
{
  const DEFAULT_PAGE_TITLE_TAG = 'h1';
  const PAGE_TITLE_POSITION_ABOVE = 'above';
  const PAGE_TITLE_POSITION_BELOW = 'below';

  public $cacheEnabled = false;
  public $showPageTitle = false;
  public $pageTitleTag = self::DEFAULT_PAGE_TITLE_TAG;
  public $pageTitleClass;
  public $pageTitlePosition = self::PAGE_TITLE_POSITION_ABOVE;

  public function run()
  {
    $this->render('breadcrumbs');
  }

  public function getCacheKey()
  {
    return 'Vira.Widget.Breadcrumbs';
  }

  public function getCacheParams()
  {
    return array(
      'varyByLanguage' => true,
      'varyByRoute' => true,
      'varyByParam' => array(
        'url',
        'id',
        'page',
      ),
      'varyByExpression' => 'Yii::app()->site->id',
    );
  }

  public function getCacheDependency()
  {
    return new VTaggedCacheDependency('Vira.Pages', 86400);
  }

  public function getParamsModel()
  {
    Yii::import($this->baseAlias . '.Breadcrumbs.forms.VBreadcrumbsWidgetParams');
    return new VBreadcrumbsWidgetParams();
  }

  public function getConfigView()
  {
    return $this->baseAlias . '.Breadcrumbs.views.configure';
  }
}
