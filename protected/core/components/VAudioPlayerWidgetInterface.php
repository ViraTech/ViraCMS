<?php
/**
 * ViraCMS Audio Player Widget Interface
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
interface VAudioPlayerWidgetInterface
{
  /**
   * Sets the audio file URL
   * @param string $url the URL
   */
  public function setAudioUrl($url);

  /**
   * Sets autoplay flag
   * @param boolean $autoplay the value
   */
  public function setAudioAutoplay($autoplay);
}
