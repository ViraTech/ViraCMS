<?php
/**
 * ViraCMS CAPTCHA Action Handler
 * Based On Yii Framework CCaptchaAction Class
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VCaptchaAction extends CCaptchaAction
{
  /**
   * Render CAPTCHA with GD library. @see CCaptchaAction::renderImageGD for details.
   */
  protected function renderImageGD($code)
  {
    $image = imagecreatetruecolor($this->width, $this->height);
    $backRed = (int) ($this->backColor % 0x1000000 / 0x10000);
    $backGreen = (int) ($this->backColor % 0x10000 / 0x100);
    $backBlue = $this->backColor % 0x100;

    if ($this->transparent) {
      $backColor = imagecolorallocatealpha($image, $backRed, $backGreen, $backBlue, 127);
      imagealphablending($image, true);
      imagesavealpha($image, true);
    }
    else {
      $backColor = imagecolorallocate($image, $backRed, $backGreen, $backBlue);
    }
    imagefill($image, 0, 0, $backColor);
    imagecolordeallocate($image, $backColor);

    $foreColor = imagecolorallocate($image, (int) ($this->foreColor % 0x1000000 / 0x10000), (int) ($this->foreColor % 0x10000 / 0x100), $this->foreColor % 0x100);

    if ($this->fontFile === null) {
      $this->fontFile = $this->getDefaultFontFile();
    }

    $length = strlen($code);
    $box = imagettfbbox(30, 0, $this->fontFile, $code);
    $w = $box[4] - $box[0] + $this->offset * ($length - 1);
    $h = $box[1] - $box[5];
    $scale = min(($this->width - $this->padding * 2) / $w, ($this->height - $this->padding * 2) / $h);
    $x = 10;
    $y = round($this->height * 27 / 40);
    for ($i = 0; $i < $length; ++$i) {
      $fontSize = (int) (rand(26, 32) * $scale * 0.8);
      $angle = rand(-10, 10);
      $letter = $code[$i];
      $box = imagettftext($image, $fontSize, $angle, $x, $y, $foreColor, $this->fontFile, $letter);
      $x = $box[2] + $this->offset;
    }

    imagecolordeallocate($image, $foreColor);

    ob_start();
    imagepng($image);
    $this->displayImage(ob_get_clean());
    imagedestroy($image);
  }

  /**
   * Render CAPTCHA with ImagicK library. @see CCaptcha::renderImageImagick for details.
   */
  protected function renderImageImagick($code)
  {
    $backColor = new ImagickPixel('#' . ($this->transparent ? 'FF' : '00') . substr("000000" . dechex($this->backColor), -6));
    $foreColor = new ImagickPixel('#' . substr("000000" . dechex($this->foreColor), -6));

    $image = new Imagick();
    $image->newImage($this->width, $this->height, $backColor);

    if ($this->fontFile === null) {
      $this->fontFile = $this->getDefaultFontFile();
    }

    $draw = new ImagickDraw();
    $draw->setFont($this->fontFile);
    $draw->setFontSize(30);
    $fontMetrics = $image->queryFontMetrics($draw, $code);

    $length = strlen($code);
    $w = (int) ($fontMetrics['textWidth']) - 8 + $this->offset * ($length - 1);
    $h = (int) ($fontMetrics['textHeight']) - 8;
    $scale = min(($this->width - $this->padding * 2) / $w, ($this->height - $this->padding * 2) / $h);
    $x = 10;
    $y = round($this->height * 27 / 40);
    for ($i = 0; $i < $length;  ++$i) {
      $draw = new ImagickDraw();
      $draw->setFont($this->fontFile);
      $draw->setFontSize((int) (rand(26, 32) * $scale * 0.8));
      $draw->setFillColor($foreColor);
      $image->annotateImage($draw, $x, $y, rand(-10, 10), $code[$i]);
      $fontMetrics = $image->queryFontMetrics($draw, $code[$i]);
      $x += (int) ($fontMetrics['textWidth']) + $this->offset;
    }

    $image->setImageFormat('png');
    $this->displayImage($image);
  }

  /**
   * Render image contents to the output
   * @param mixed $image image object or resource
   * @param string $mime image mime type
   */
  protected function displayImage($image, $mime = 'image/png')
  {
    header('Pragma: public');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Content-Transfer-Encoding: binary');
    header('Content-type: ' . $mime);
    echo $image;
  }

  /**
   * Get default font file
   * @return string path to the TTF file
   */
  protected function getDefaultFontFile()
  {
    return Yii::getPathOfAlias('system.web.widgets.captcha') . DIRECTORY_SEPARATOR . 'SpicyRice.ttf';
  }
}
