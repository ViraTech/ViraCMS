<?php
/**
 * ViraCMS Password Generator Component
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VPasswordGenerator extends VApplicationComponent
{
  public $minLength = 3;
  public $maxLength = 8;
  public $enableDigits = true;
  public $enableCapitals = true;
  public $enableSymbols = true;
  private $passwordSourceChars = 'abcdefghijklmnopqrstuvwxyz';
  private $_digits = '0123456789';
  private $_capitals = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  private $_symbols = '_-!@#$%^&*()=+/|\?.,`';

  public function init()
  {
    parent::init();

    if ($this->enableCapitals) {
      $this->passwordSourceChars .= $this->_capitals;
    }

    if ($this->enableDigits) {
      $this->passwordSourceChars .= $this->_digits;
    }

    if ($this->enableSymbols) {
      $this->passwordSourceChars .= $this->_symbols;
    }

    if (!empty(Yii::app()->params['passwordLengthMin'])) {
      $this->minLength = Yii::app()->params['passwordLengthMin'];
    }

    if (!empty(Yii::app()->params['passwordLengthMax'])) {
      $this->maxLength = Yii::app()->params['passwordLengthMax'];
    }
  }

  public function generate($length = null)
  {
    $password = '';

    if ($length == null) {
      $length = mt_rand(min($this->minLength, $this->maxLength), max($this->minLength, $this->maxLength));
    }

    $sourceLength = strlen($this->passwordSourceChars);

    if ($sourceLength > 0) {
      for ($i = 0; $i < $length; $i++) {
        $password .= $this->passwordSourceChars[mt_rand(0, $sourceLength - 1)];
      }
    }

    return $password;
  }
}
