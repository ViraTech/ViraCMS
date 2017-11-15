<?php
/**
 * ViraCMS Exception Page Renderer Component
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VExceptionRenderer extends VApplicationComponent
{
  /**
   * @var integer exception (return error) code
   */
  private $_code;

  /**
   * @var string message
   */
  private $_message;

  /**
   * @var mixed error type
   */
  private $_type;

  /**
   * @var string file where error has occurred
   */
  private $_file;

  /**
   * @var string line of the file where error has occurred
   */
  private $_line;

  /**
   * Class constructor
   * @param CErrorHandler $error error handler object
   */
  public function __construct($error)
  {
    $this->_code = $error['code'];
    $this->_message = $error['message'];
    $this->_type = $error['type'];
    $this->_file = $error['file'];
    $this->_line = $error['line'];
  }

  /**
   * Is this content dynamic?
   * @return boolean
   */
  public function getIsDynamic()
  {
    return true;
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
