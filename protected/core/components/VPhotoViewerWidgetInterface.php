<?php
/**
 * ViraCMS Photo Viewer Widget Interface
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
interface VPhotoViewerWidgetInterface
{
  /**
   * The widget caller's object
   * @param mixed $owner the object reference
   */
  public function setOwner($owner);

  /**
   * Sets the images list
   * @param array $images the images list
   * The format:
   * <pre>
   * array(
   *   array(
   *     'url' => 'images/image.jpg', // full size image url
   *     'thumbnail' => 'images/thumbnail.jpg', // image thumbnail url
   *     'title' => 'Image Title', // image title
   *     'description' => 'Image Description Text', // image description
   *   ),
   *   [...]
   * )
   * </pre>
   */
  public function setImages($images);
}
