<?php
/**
 * ViraCMS Language Helper Functions
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VLanguageHelper
{
  /**
   * @var array languages cache
   */
  protected static $_languages;

  /**
   * Return languages set defined in the application
   * @param boolean $onlyActive return only active languages
   * @return VLanguage[] list of found languages
   */
  public static function getLanguages($onlyActive = true)
  {
    if (self::$_languages == null) {
      self::$_languages = VLanguage::model()->defaultOrder()->cache(VLanguage::DEFAULT_LANGUAGES_CACHE_DURATION)->findAll();
    }

    return $onlyActive ? self::getActiveLanguages(self::$_languages) : self::$_languages;
  }

  /**
   * Return current or specified language model found by it's short code (i.e. 2-letter ISO code)
   * @param string $code (optional) language code
   * @return VLanguage language found or new model
   */
  public static function getLanguage($code = null)
  {
    if ($code == null) {
      $code = Yii::app()->getLanguage();
    }

    $languages = self::getLanguages();

    foreach ($languages as $language) {
      if ($language->id == $code || $language->locale == $code) {
        return $language;
      }
    }

    return new VLanguage;
  }

  /**
   * Set current application language found by it's code
   * @param string $code language 2-letter ISO code
   */
  public static function setLanguage($code)
  {
    $language = self::getLanguage($code);

    if (!$language->isNewRecord) {
      Yii::app()->setLanguage($language->id);
    }
  }

  /**
   * Return current or specified language title
   * @param integer $languageID (optional) language ID
   * @return string language title
   */
  public static function getLanguageTitle($languageID = null)
  {
    return self::getLanguageAttribute('title', $languageID);
  }

  /**
   * Return language attribute for current or specified language
   * @param string $attribute attribute name
   * @param string $code (optional) language ISO code
   * @return mixed attribute value if exists, null otherwise
   */
  public static function getLanguageAttribute($attribute = null, $code = null)
  {
    $language = $code == null ? self::getLanguage() : self::getLanguage($code);
    return $language->getAttribute($attribute == null ? 'id' : $attribute);
  }

  /**
   * Converts language identifier to internal format
   * @param string $id language identifier or code (e.g. ietf)
   * @return string guessed internal language identifier
   */
  public static function formatLanguageId($id)
  {
    return substr($id, 0, 2);
  }

  /**
   * Return only active languages list
   * @param array $languages languages list
   */
  static function getActiveLanguages($languages)
  {
    $return = array();

    foreach ($languages as $language) {
      if ($language->active) {
        $return[] = $language;
      }
    }

    return $return;
  }
}
