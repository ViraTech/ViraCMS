<?php
/**
 * ViraCMS Image Resize And Crop Functions Controller
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class ImageController extends VPublicController
{
  public $layout = null;
  private $_emptyImageMime = 'image/gif';
  private $_emptyImageContents = array(
    71, 73, 70, 56, 57, 97, 1, 0, 1, 0, 128, 0, 0, 0, 0, 0, 0, 0, 0, 33, 249,
    4, 1, 0, 0, 0, 0, 44, 0, 0, 0, 0, 1, 0, 1, 0, 0, 2, 2, 68, 1, 0, 59
  );

  /**
   * @var string image cache directory
   */
  protected $cacheDir;

  /**
   * @var CHttpRequest shortcut to request
   */
  protected $r;

  public function init()
  {
    $this->cacheDir = Yii::getPathOfAlias('webroot.cache');
    $this->r = Yii::app()->request;
  }

  /**
   * Render current theme placeholder image if exists
   */
  public function actionPlaceholder()
  {
    $placeholder = Yii::app()->theme->getPlaceholderFile();

    // placeholder image width
    $width = intval($this->r->getParam('width'));
    // placeholder image height
    $height = intval($this->r->getParam('height'));
    // security hash
    $hash = $this->r->getParam('hash');
    // mime type
    $mime = VFileHelper::getMimeTypeByExtension($placeholder);

    if ($width && $height && file_exists($placeholder) && $hash == Yii::app()->image->generateHash($width, $height, $mime)) {
      $image = Yii::app()->image->loadImage($placeholder);
      $params = Yii::app()->image->getImageFileInfo($placeholder, 'all');
      $originalRatio = $params[0] / $params[1];
      $expectedRatio = $width / $height;
      if ($originalRatio >= $expectedRatio) {
        $resizeWidth = 0;
        $resizeHeight = $height;
      }
      else {
        $resizeWidth = $width;
        $resizeHeight = 0;
      }
      $image = Yii::app()->image->resizeImage($image, $resizeWidth, $resizeHeight);
      $image = Yii::app()->image->cropImage($image, $width, $height, 'center', 'middle');

      header((isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0') . ' 200 OK', true, 200);
      header('Content-Type: ' . $mime);

      $baseDir = $this->cacheDir . DIRECTORY_SEPARATOR . $hash;
      $this->checkDirExist($baseDir);

      $saveFileName = $baseDir . DIRECTORY_SEPARATOR . 'ph-w' . $width . '-h' . $height . '-' . basename($placeholder);
      Yii::app()->image->saveImage($image, $saveFileName, $params[2]);
      readfile($saveFileName);
    }
    else {
      $this->renderEmptyImage();
    }
  }

  /**
   * Crop image
   * @param boolean $save need to save image
   */
  public function actionCrop($save = true)
  {
    $width = intval($this->r->getParam('width'));
    $height = intval($this->r->getParam('height'));
    $hash = $this->r->getParam('hash');
    $filename = $this->fixFilename($this->r->getParam('filename'));
    $filepath = Yii::app()->storage->getFilePath($filename);
    $mime = VFileHelper::getMimeTypeByExtension($filename);
    $cropHorizontalPos = $this->r->getParam('hpos');
    $cropVerticalPos = $this->r->getParam('vpos');

    if ($hash && Yii::app()->image->generateHash($width, $height, $mime, $cropHorizontalPos, $cropVerticalPos) == $hash && $width && $height && $filepath && file_exists($filepath)) {
      $image = Yii::app()->image->loadImage($filepath);
      $params = Yii::app()->image->getImageFileInfo($filepath, 'all');
      $originalRatio = $params[0] / $params[1];
      $expectedRatio = $width / $height;
      if ($originalRatio >= $expectedRatio) {
        $resizeWidth = 0;
        $resizeHeight = $height;
      }
      else {
        $resizeWidth = $width;
        $resizeHeight = 0;
      }
      $image = Yii::app()->image->resizeImage($image, $resizeWidth, $resizeHeight);
      $cropParams = array(
        $image,
        $width,
        $height,
      );
      if ($cropHorizontalPos && $cropVerticalPos) {
        $cropParams[] = $cropHorizontalPos;
        $cropParams[] = $cropVerticalPos;
      }
      $image = call_user_func_array(array(Yii::app()->image, 'cropImage'), $cropParams);
      $filePrefix = 'cr-w' . $width . '-h' . $height . '-';
      if ($cropHorizontalPos && $cropVerticalPos) {
        $filePrefix .= $cropHorizontalPos . '-' . $cropVerticalPos . '-';
      }

      header((isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0') . ' 200 OK', true, 200);
      header('Content-Type: ' . $mime);

      if ($save) {
        $baseDir = $this->cacheDir . DIRECTORY_SEPARATOR . $hash;
        $this->checkDirExist($baseDir);
        $saveFileName = $baseDir . DIRECTORY_SEPARATOR . $filePrefix . str_replace('/', '_', $filename);
        Yii::app()->image->saveImage($image, $saveFileName, $params[2]);
        readfile($saveFileName);
      }
      else {
        Yii::app()->image->showImage($image, $params[2]);
      }
    }
    else {
      $this->renderEmptyImage();
    }
  }

  /**
   * Resize image and save result to image cache
   */
  public function actionResize()
  {
    $width = intval($this->r->getParam('width'));
    $height = intval($this->r->getParam('height'));
    $hash = $this->r->getParam('hash');
    $filename = $this->fixFilename($this->r->getParam('filename'));
    $filepath = Yii::app()->storage->getFilePath($filename);
    $mime = VFileHelper::getMimeTypeByExtension($filename);

    if ($hash && Yii::app()->image->generateHash($width, $height, $mime) == $hash && ($width || $height) && $filepath && file_exists($filepath)) {
      $filePrefix = 'rs';
      if ($width) {
        $filePrefix .= '-w' . $width;
      }
      if ($height) {
        $filePrefix .= '-h' . $height;
      }
      $filePrefix .= '-';

      $baseDir = $this->cacheDir . DIRECTORY_SEPARATOR . $hash;
      $this->checkDirExist($baseDir);

      $image = Yii::app()->image->loadImage($filepath);
      $type = Yii::app()->image->getImageFileInfo($filepath);
      $image = Yii::app()->image->resizeImage($image, $width, $height);
      $saveFileName = $baseDir . DIRECTORY_SEPARATOR . $filePrefix . str_replace('/', '_', $filename);
      Yii::app()->image->saveImage($image, $saveFileName, $type);

      header((isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0') . ' 200 OK', true, 200);
      header('Content-Type: ' . $mime);
      readfile($saveFileName);
    }
    else {
      $this->renderEmptyImage();
    }
  }

  /**
   * Temporary image cropping (for preview only)
   */
  public function actionTemp()
  {
    $this->actionCrop(false);
  }

  /**
   * Render transparent 1x1 GIF image
   */
  public function actionEmpty()
  {
    $this->renderEmptyImage();
  }

  /**
   * Check if directory exists or create it otherwise
   * @param string $dir directory path
   */
  protected function checkDirExist($dir)
  {
    if (!file_exists($dir)) {
      @mkdir($dir, 0777);
      @chmod($dir, 0777);
    }
  }

  /**
   * Empty (transparent 1x1) image renderer
   */
  protected function renderEmptyImage()
  {
    header('Content-type: ' . $this->_emptyImageMime);
    echo implode('', array_map('chr', $this->_emptyImageContents));
  }

  /**
   * Remove non-alphanumeric characters from image filename
   * @param string $filename file name
   * @return string
   */
  protected function fixFilename($filename)
  {
    if (preg_match('/([a-f0-9]{32})_(.+)/', $filename, $filenameParts)) {
      array_shift($filenameParts);
      $filename = implode('/', $filenameParts);
    }

    return $filename;
  }
}
