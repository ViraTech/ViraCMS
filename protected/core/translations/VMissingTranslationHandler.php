<?php
/**
 * ViraCMS Message Translation Component
 *
 * Missing Translation Event Handler (@link CMessageSource::onMissingTranslation onMissingTranslation}
 *
 * @package vira.core.core
 * @subpackage vira.core.translate
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VMissingTranslationHandler
{
  /**
   * Handles the missing translation event
   * @param CEvent $event the event
   * @return string the message
   */
  static function missingTranslation($event)
  {
    $source = new VTranslateSource('auto');
    $source->module = $event->module;
    $source->category = $event->category;
    $source->source = $event->message;
    $source->save();

    return $event->message;
  }
}
