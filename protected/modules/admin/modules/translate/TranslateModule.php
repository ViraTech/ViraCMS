<?php
/**
 * ViraCMS Translations Module
 *
 * @package vira.core.core
 * @subpackage vira.core.translate
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class TranslateModule extends VSystemWebModule
{
  public $defaultController = 'language';

  public function init()
  {
    parent::init();
    Yii::import('application.modules.admin.modules.translate.components.*');
  }
}
