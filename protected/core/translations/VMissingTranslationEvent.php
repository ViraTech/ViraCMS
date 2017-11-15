<?php
/**
 * ViraCMS Message Translation Component
 *
 * Missing Translation Event (@link CMessageSource::onMissingTranslation onMissingTranslation}
 *
 * @package vira.core.core
 * @subpackage vira.core.translate
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VMissingTranslationEvent extends CEvent
{
  /**
   * @var string the message to be translated
   */
  public $message;

  /**
   * @var string the category that the message belongs to
   */
  public $category;

  /**
   * @var string the ID of the language that the message is to be translated to
   */
  public $language;

  /**
   * @var string module id
   */
  public $module;

  /**
   * Constructor.
   * @param mixed $sender sender of this event
   * @param string $module module id
   * @param string $category the category that the message belongs to
   * @param string $message the message to be translated
   * @param string $language the ID of the language that the message is to be translated to
   */
  public function __construct($sender, $module, $category, $message, $language)
  {
    parent::__construct($sender);
    $this->module = $module;
    $this->category = $category;
    $this->language = $language;
    $this->message = $message;
  }
}
