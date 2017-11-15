<?php
/**
 * ViraCMS Video Player Widget Interface
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
interface VVideoPlayerWidgetInterface
{
  /**
   * Sets the video file URL
   * @param string $url the URL
   */
  public function setVideoUrl($url);

  /**
   * Sets the video splash screen (image) file URL
   * @param string $url the URL
   */
  public function setImageUrl($url);

  /**
   * Sets the video width
   * @param integer $width the width value
   */
  public function setVideoWidth($width);

  /**
   * Sets the video height
   * @param integer $height the height value
   */
  public function setVideoHeight($height);

  /**
   * Sets autoplay flag
   * @param boolean $autoplay the value
   */
  public function setVideoAutoplay($autoplay);
}
