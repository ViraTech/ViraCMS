<?php
/**
 * ViraCMS Base Widget Configuration Class
 *
 * @package vira.core.core
 * @subpackage vira.core.widgets
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VWidgetBaseParams extends CFormModel
{
  public function attributeHints()
  {
    return array();
  }

  public function getAttributeHint($attribute)
  {
    $hints = $this->attributeHints();
    return isset($hints[$attribute]) ? $hints[$attribute] : '';
  }
}
