<?php
/**
 * ViraCMS Site Language Selector Widget
 *
 * @package vira.core.core
 * @subpackage vira.core.widgets
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VLanguageSelectorWidget extends VWidget
{
  const CONTENT_ALIGN_LEFT = 'left';
  const CONTENT_ALIGN_RIGHT = 'right';
  const CONTENT_ALIGN_CENTER = 'center';
  const DEFAULT_CONTENT_ALIGN = self::CONTENT_ALIGN_LEFT;

  public $align = self::DEFAULT_CONTENT_ALIGN;

  public function run()
  {
    $this->render('language');
  }

  public function getCacheKey()
  {
    return 'Vira.Widget.LanguageSelector';
  }

  public function getCacheParams()
  {
    return array(
      'varyByLanguage' => true,
    );
  }

  public function getCacheDependency()
  {
    return new VTaggedCacheDependency('Vira.Languages', 86400);
  }

  public function getParamsModel()
  {
    Yii::import($this->baseAlias . '.LanguageSelector.forms.VLanguageSelectorWidgetParams');
    return new VLanguageSelectorWidgetParams();
  }

  public function getConfigView()
  {
    return $this->baseAlias . '.LanguageSelector.views.configure';
  }
}
