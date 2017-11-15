<?php
/**
 * ViraCMS Page's Title & Breadcrumbs Widget Configuration Form
 *
 * @package vira.core.core
 * @subpackage vira.core.widgets
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VBreadcrumbsWidgetParams extends VWidgetBaseParams
{
  public $showPageTitle = false;
  public $pageTitleTag = VBreadcrumbsWidget::DEFAULT_PAGE_TITLE_TAG;
  public $pageTitleClass;
  public $pageTitlePosition = VBreadcrumbsWidget::PAGE_TITLE_POSITION_ABOVE;

  public function rules()
  {
    return array(
      array('showPageTitle', 'boolean'),
      array('pageTitleTag', 'in', 'range' => array('h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'p', 'span'), 'message' => Yii::t('common', 'Must be a valid tag name.')),
      array('pageTitleClass', 'length', 'max' => 255),
      array('pageTitlePosition', 'in', 'range' => array_keys($this->getTitlePositions()), 'message' => Yii::t('common', 'Invalid value selected.')),
    );
  }

  public function attributeLabels()
  {
    return array(
      'showPageTitle' => Yii::t('common', 'Show Page Title'),
      'pageTitleTag' => Yii::t('common', 'Page Title Tag Name'),
      'pageTitleClass' => Yii::t('common', 'Page Title Tag Class'),
      'pageTitlePosition' => Yii::t('common', 'Page Title Position'),
    );
  }

  public function getTitlePositions()
  {
    return array(
      VBreadcrumbsWidget::PAGE_TITLE_POSITION_ABOVE => Yii::t('common', 'Above Breadcrumbs'),
      VBreadcrumbsWidget::PAGE_TITLE_POSITION_BELOW => Yii::t('common', 'Below Breadcrumbs'),
    );
  }
}
