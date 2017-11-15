<?php
/**
 * ViraCMS Stemmer Factory Class
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VStemFactory extends VApplicationComponent
{
  /**
   * @var array stemmers cache
   */
  private $_stemmerCache = array();

  /**
   * Return stemmer object for selected language
   * @param string $languageID language identifier
   * @return mixed
   */
  public function getStemmer($languageID = null)
  {
    if (!isset($this->_stemmerCache[ $languageID ])) {
      if ($languageID === null) {
        $languageID = Yii::app()->language;
      }

      $languageID = mb_convert_case($languageID, MB_CASE_TITLE, Yii::app()->charset);

      $stemmerClass = 'VSearchStemmer' . $languageID;
      Yii::import('application.components.stemmers.' . $stemmerClass);
      if (@class_exists($stemmerClass)) {
        $stemmer = new $stemmerClass;
        if ($this->isStemmer($stemmer)) {
          $this->_stemmerCache[ $languageID ] = $stemmer;
        }
      }
    }

    return isset($this->_stemmerCache[ $languageID ]) ? $this->_stemmerCache[ $languageID ] : false;
  }

  /**
   * Check if provided object is valid stemmer class
   * @param mixed $object object
   * @return boolean
   */
  protected function isStemmer($object)
  {
    if (is_object($object)) {
      $reflection = new ReflectionClass($object);
      if ($reflection->implementsInterface('VSearchStemmerInterface')) {
        return true;
      }
    }

    return false;
  }
}

/**
 * ViraCMS Stemmer Interface
 */
interface VSearchStemmerInterface
{
  /**
   * Returns the word's stem
   * @param string $word the word
   */
  public function stem($word);
}
