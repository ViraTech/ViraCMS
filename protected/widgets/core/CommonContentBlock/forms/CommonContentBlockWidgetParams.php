<?php
/**
 * ViraCMS Shared Content Block Widget Configuration Form
 *
 * @package vira.core.core
 * @subpackage vira.core.widgets
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class CommonContentBlockWidgetParams extends VWidgetBaseParams
{
  public $contentID;

  public function rules()
  {
    return array(
      array('contentID', 'required'),
      array('contentID', 'length', 'is' => 36, 'allowEmpty' => true),
    );
  }

  public function attributeLabels()
  {
    return array(
      'contentID' => Yii::t('common', 'Shared Content Block'),
    );
  }
}
