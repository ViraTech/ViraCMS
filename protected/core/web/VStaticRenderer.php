<?php
/**
 * ViraCMS Static Block Renderer Component
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VStaticRenderer extends VApplicationComponent
{
  /**
   * @var string content
   */
  private $_content;

  /**
   * Class constructor
   * @param string $data content
   */
  public function __construct($data)
  {
    $this->_content = $data;
  }

  /**
   * Is content block dynamic?
   * @return boolean
   */
  public function getIsDynamic()
  {
    return false;
  }

  /**
   * Render block contents and return output
   * @return string
   */
  public function render()
  {
    return $this->_content;
  }
}
