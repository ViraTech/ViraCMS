<?php
/**
 * ViraCMS Images Manipulation Component
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VImage extends VApplicationComponent
{
  const DEFAULT_HASH_SIZE = 8;

  /**
   * @var string hash generator salt
   */
  public $salt = '0123456789';

  /**
   * @var integer hash size
   */
  public $hashSize = self::DEFAULT_HASH_SIZE;

  /**
   * @var array cache variable for parameters
   */
  protected $_fileParamsCache = array();

  /**
   * Check for file is really image
   * @param string $filepath path to image file
   * @return boolean
   */
  public function isImageFile($filepath)
  {
    $info = $this->getImageFileInfo($filepath);
    return !empty($info);
  }

  /**
   * Load image file to resource
   * @param string $filepath path to image file
   * @return resource
   */
  public function loadImage($filepath)
  {
    $image = null;

    switch ($this->getImageFileInfo($filepath)) {
      case IMAGETYPE_GIF:
        $image = imagecreatefromgif($filepath);
        break;

      case IMAGETYPE_PNG:
        $image = imagecreatefrompng($filepath);
        break;

      case IMAGETYPE_JPEG:
        $image = imagecreatefromjpeg($filepath);
        break;

      case IMAGETYPE_BMP:
        $image = imagecreatefromwbmp($filepath);
        break;
    }

    return $image;
  }

  /**
   * Resizes image and return resulting resource
   * @param resource $image source image
   * @param integer $maxWidth maximum image width
   * @param integer $maxHeight maximum image height
   * @param integer $minWidth minimum image width
   * @param integer $minHeight minimum image height
   * @param boolean $proportional need to do proportional resize
   * @param boolean $crop crop image
   * @return resource
   */
  public function resizeImage($image, $maxWidth, $maxHeight, $minWidth = 0, $minHeight = 0, $proportional = true, $crop = false)
  {
    $resizedImage = null;

    if (is_resource($image)) {
      $currentWidth = imagesx($image);
      $currentHeight = imagesy($image);

      if ($currentWidth == $maxWidth && $currentHeight == $maxHeight) {
        $resizedImage = $image;
      }
      else {
        if ($maxWidth == 0 || $maxHeight == 0 || $minWidth || $minHeight) {
          list($maxWidth, $maxHeight) = $this->fixDimensions($currentWidth, $currentHeight, $maxWidth, $maxHeight, $minWidth, $minHeight, $crop);
        }
        elseif ($proportional) {
          $wRatio = $maxWidth / $currentWidth;
          $hRatio = $maxHeight / $currentHeight;

          // upscale or downgrade? (min for downgrade)
          $multiplier = call_user_func($currentWidth > $maxWidth || $currentHeight > $maxHeight ? 'min' : 'max', $wRatio, $hRatio);
          $maxWidth = floor($currentWidth * $multiplier);
          $maxHeight = floor($currentHeight * $multiplier);
        }
        $resizedImage = $this->createImage($maxWidth, $maxHeight);
        imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $maxWidth, $maxHeight, $currentWidth, $currentHeight);
      }
    }

    return $resizedImage;
  }

  /**
   * Crop image and return resulting resource
   * @param resource $image source image
   * @param integer $width new image width
   * @param integer $height new image height
   * @param mixed $positionX exact value in pixels or 'left', 'right' or 'center' shortcuts
   * @param mixed $positionY exact value in pixels or 'top', 'bottom' or 'middle' shortcuts
   * @return resource
   */
  public function cropImage($image, $width, $height, $positionX = 'center', $positionY = 'middle')
  {
    $croppedImage = null;

    if (is_resource($image)) {
      $currentWidth = imagesx($image);
      $currentHeight = imagesy($image);
      list($width, $height) = $this->fixDimensions($currentWidth, $currentHeight, $width, $height, $width, $height, true);
      $croppedImage = $this->createImage($width, $height, true);

      switch ($positionX) {
        case 'left':
          $left = 0;
          break;

        case 'right':
          $left = abs($currentWidth - $width);
          break;

        case 'center':
          $left = round(abs($currentWidth - $width) / 2);
          break;

        default:
          $left = round(floatval($positionX));
      }

      switch ($positionY) {
        case 'top':
          $top = 0;
          break;

        case 'bottom':
          $top = abs($currentHeight - $height);
          break;

        case 'middle':
          $top = round(abs($currentHeight - $height) / 2);
          break;

        default:
          $top = round(floatval($positionY));
      }
      imagecopy($croppedImage, $image, 0, 0, $left, $top, $width, $height);
    }

    return $croppedImage;
  }

  /**
   * Create new true color image with transparent background optionally
   * @param integer $width image width
   * @param integer $height image height
   * @param boolean $transparent need to create transparent background
   * @return resource
   */
  public function createImage($width, $height, $transparent = true)
  {
    $image = imagecreatetruecolor($width, $height);

    if ($transparent) {
      $alpha = imagecolorallocatealpha($image, 255, 255, 255, 127);
      imagealphablending($image, false);
      imagesavealpha($image, true);
      imagefilledrectangle($image, 0, 0, $width, $height, $alpha);
    }

    return $image;
  }

  /**
   * Save image to file
   * @param resource $image image
   * @param string $filepath image file path
   * @param integer $type image type (see image constants like IMAGETYPE_GIF etc)
   * @param integer $quality image quality, has sense only for PNG and JPG image types
   * @return boolean
   */
  public function saveImage($image, $filepath, $type = IMAGETYPE_PNG, $quality = 9)
  {
    if (is_resource($image)) {
      switch ($type) {
        case IMAGETYPE_GIF:
          return imagegif($image, $filepath);
          break;

        case IMAGETYPE_JPEG:
          return imagejpeg($image, $filepath, $quality < 10 ? $quality * 10 : $quality);
          break;

        case IMAGETYPE_BMP:
          return imagewbmp($image, $filepath);
          break;

        default:
          return imagepng($image, $filepath, $quality);
          break;
      }
      @chmod($filepath, 0666);
    }
  }

  /**
   * Echo image to stdout
   * @param resource $image image
   * @param integer $type image type (see IMAGETYPE_ constants)
   * @param integer $quality image quality, make sense only for PNG and JPG image types
   * @return boolean
   */
  public function showImage($image, $type = IMAGETYPE_PNG, $quality = 9)
  {
    if (is_resource($image)) {
      switch ($type) {
        case IMAGETYPE_GIF:
          return imagegif($image);
          break;

        case IMAGETYPE_JPEG:
          return imagejpeg($image, null, $quality < 10 ? $quality * 10 : $quality);
          break;

        case IMAGETYPE_BMP:
          return imagewbmp($image);
          break;

        default:
          return imagepng($image, null, $quality);
          break;
      }
    }
  }

  /**
   * Returns requested info about image
   * @param string $filepath image file path
   * @param string $param optional, if not set return all image info as array, exact parameter otherwise
   * @return mixed
   */
  public function getImageFileInfo($filepath, $param = 'type')
  {
    if (file_exists($filepath)) {
      if (empty($this->_fileParamsCache[$filepath])) {
        $this->_fileParamsCache[$filepath] = getimagesize($filepath);
      }

      switch ($param) {
        case 'width':
          return $this->_fileParamsCache[$filepath][0];

        case 'height':
          return $this->_fileParamsCache[$filepath][1];

        case 'type':
          return $this->_fileParamsCache[$filepath][2];

        case 'attr':
          return $this->_fileParamsCache[$filepath][3];

        case 'all':
        default:
          return $this->_fileParamsCache[$filepath];
      }
    }
  }

  /**
   * Fixes image dimensions and return new width and height as array
   * @param integer $currentWidth current image width
   * @param integer $currentHeight current image height
   * @param integer $maxWidth maximum image width
   * @param integer $maxHeight maximum image height
   * @param integer $minWidth minimum image width
   * @param integer $minHeight minimum image height
   * @param boolean $crop need to crop image
   * @return array
   */
  public function fixDimensions($currentWidth, $currentHeight, $maxWidth, $maxHeight, $minWidth, $minHeight, $crop = false)
  {
    $width = $maxWidth;
    $height = $maxHeight;

    if ($width == 0) {
      $width = round($currentWidth / $currentHeight * $maxHeight);
    }
    elseif ($height == 0) {
      $height = round($currentHeight / $currentWidth * $maxWidth);
    }

    if ($crop) {
      if ($minWidth > 0 && $width < $minWidth) {
        $width = $minWidth;
        $height = round($currentHeight / $currentWidth * $minWidth);
      }
      elseif ($minHeight > 0 && $height < $minHeight) {
        $height = $minHeight;
        $width = round($currentWidth / $currentHeight * $minHeight);
      }
    }

    return array($width, $height);
  }

  /**
   * Generate security hash for anti-DDoS protection
   * @param integer $width image width
   * @param integer $height image height
   * @param string $mime image mime-type
   * @param string $cropVerticalPos image crop vertical position
   * @param string $cropHorizontalPos image crop horizontal position
   * @return string
   */
  public function generateHash($width, $height, $mime, $cropVerticalPos = null, $cropHorizontalPos = null)
  {
    $hash = md5($this->salt . $width . $height . $mime);
    if ($cropVerticalPos && $cropHorizontalPos) {
      $hash = md5($hash . $cropVerticalPos . $cropHorizontalPos);
    }
    return substr($hash, 0, min(strlen($hash), $this->hashSize));
  }
}
