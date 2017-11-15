<?php
/**
 * ViraCMS CSS/JS Script Handler
 * Based On Yii Framework CClientScript Class
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VClientScript extends CClientScript
{
  /**
   * @var array storage for additional HTML meta tags
   */
  protected $htmlMetaTags = array();

  /**
   * @var array theme related CSS files (CSS URL => media type).
   */
  protected $themeCssFiles = array();

  /**
   * Clean up everything
   */
  public function reset()
  {
    $this->htmlMetaTags = array();
    parent::reset();
  }

  /**
   * Format full scripts, stylesheets, meta and link tags as array
   * @return array
   */
  public function getContentPack()
  {
    $pack = array();

    $parts = array(
      'hasScripts',
      'coreScripts',
      'cssFiles',
      'themeCssFiles',
      'css',
      'scriptFiles',
      'scripts',
      'metaTags',
      'linkTags',
      'htmlMetaTags',
    );

    foreach ($parts as $part) {
      if (!empty($this->$part)) {
        $pack[$part] = $this->$part;
      }
    }

    return $pack;
  }

  /**
   * Set scripts, stylesheets, meta and link tags to current page.
   * @see VClientScript::getContentPack
   * @param array $pack pack of objects returned by getContentPack()
   */
  public function setContentPack($pack)
  {
    if (!empty($pack) && is_array($pack)) {
      foreach ($pack as $attribute => $value) {
        if (property_exists($this, $attribute)) {
          $this->$attribute = $value;
        }
      }
    }
  }

  /**
   * Return list of stylesheet files registered
   * @return array
   */
  public function getCssFiles()
  {
    return $this->cssFiles;
  }

  /**
   * Return list of stylesheet files registered for current theme
   * @return array
   */
  public function getThemeCssFiles()
  {
    return $this->themeCssFiles;
  }

  /**
   * Add one more HTML meta tag to current page
   * @param string $html meta tag as HTML code
   */
  public function addHtmlMetaTag($html)
  {
    $this->htmlMetaTags[] = trim($html);
  }

  /**
   * Inserts the scripts in the head section.
   * @param string $output the output to be inserted with scripts.
   */
  public function renderHead(&$output)
  {
    $html = '';
    foreach ($this->metaTags as $meta) {
      $html .= CHtml::metaTag($meta['content'], null, null, $meta) . "\n";
    }

    foreach ($this->htmlMetaTags as $meta) {
      $html .= $meta . "\n";
    }

    foreach ($this->linkTags as $link) {
      $html .= CHtml::linkTag(null, null, null, null, $link) . "\n";
    }

    foreach ($this->cssFiles as $url => $media) {
      $html .= CHtml::cssFile($url, $media) . "\n";
    }

    foreach ($this->themeCssFiles as $url => $media) {
      $html .= CHtml::cssFile($url, $media) . "\n";
    }

    foreach ($this->css as $css) {
      $html .= CHtml::css($css[0], $css[1]) . "\n";
    }

    if ($this->enableJavaScript) {
      if (isset($this->scriptFiles[self::POS_HEAD])) {
        foreach ($this->scriptFiles[self::POS_HEAD] as $scriptFile) {
          $html .= CHtml::scriptFile($scriptFile) . "\n";
        }
      }

      if (isset($this->scripts[self::POS_HEAD])) {
        $html .= CHtml::script(implode("\n", $this->scripts[self::POS_HEAD])) . "\n";
      }
    }

    if ($html !== '') {
      $count = 0;
      $output = preg_replace('/(<title\b[^>]*>|<\\/head\s*>)/is', '<###head###>$1', $output, 1, $count);
      if ($count) {
        $output = str_replace('<###head###>', $html, $output);
      }
      else {
        $output = $html . $output;
      }
    }
  }

  /**
   * Registers theme related CSS file
   * @param string $url URL of the CSS file
   * @param string $media media that the CSS file should be applied to. If empty, it means all media types.
   * @return CClientScript the CClientScript object itself (to support method chaining, available since version 1.1.5).
   */
  public function registerThemeCssFile($url, $media = '')
  {
    $this->hasScripts = true;
    $this->themeCssFiles[$url] = $media;
    $params = func_get_args();
    $this->recordCachingAction('clientScript', 'registerThemeCssFile', $params);
    return $this;
  }
}
