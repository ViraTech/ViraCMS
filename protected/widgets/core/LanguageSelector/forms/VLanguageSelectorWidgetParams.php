<?php
/**
 * ViraCMS Site Language Selector Widget Configuration Form
 *
 * @package vira.core.core
 * @subpackage vira.core.widgets
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VLanguageSelectorWidgetParams extends VWidgetBaseParams
{
  public $align = VLanguageSelectorWidget::DEFAULT_CONTENT_ALIGN;

  public function rules()
  {
    return array(
      array('align', 'in', 'range' => array_keys($this->getAligns()), 'message' => Yii::t('common', 'Invalid value selected.')),
    );
  }

  public function attributeLabels()
  {
    return array(
      'align' => Yii::t('common', 'Align Widget Content'),
    );
  }

  public function getAligns()
  {
    return array(
      VLanguageSelectorWidget::CONTENT_ALIGN_LEFT => Yii::t('common', 'To the left'),
      VLanguageSelectorWidget::CONTENT_ALIGN_RIGHT => Yii::t('common', 'To the right'),
      VLanguageSelectorWidget::CONTENT_ALIGN_CENTER => Yii::t('common', 'Centered'),
    );
  }
}
