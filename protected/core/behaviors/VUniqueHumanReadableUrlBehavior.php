<?php
/**
 * ViraCMS Model Human Readable Unique URL Behavior
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VUniqueHumanReadableUrlBehavior extends CModelBehavior
{
  const CONDITION_PREFIX = ':depAttr';
  const URL_MAX_LENGTH = 255;
  const URL_WORDS_DELIMITER_CHAR = '-';

  public $urlAttributeName = 'url';
  public $dependOnAttributes = array();
  protected $_attributesFiltered;

  public function beforeValidate($event)
  {
    $value = $this->owner[$this->urlAttributeName];
    if ($value && !preg_match('/^[A-z0-9\-]*$/', $value)) {
      $this->owner->addError($this->urlAttributeName, Yii::t('admin.content.errors', 'URL can only contain latin characters, numbers and sign "-".'));
    }
  }

  public function afterValidate($event)
  {
    if (!$this->owner->hasErrors($this->urlAttributeName)) {
      $pks = $this->owner->primaryKey();

      if (!is_array($pks)) {
        $pks = array($pks);
      }
      $condition = '';

      if (!$this->owner->isNewRecord) {
        $pkCondition = array();
        foreach ($pks as $i => $pk) {
          $pkCondition[] = $this->owner->quoteColumn($pk) . '<>:pk' . $i;
        }

        $condition .= '(' . implode(' AND ', $pkCondition) . ') AND ';
      }

      $condition .= $this->owner->quoteColumn($this->urlAttributeName) . '=:url';
      $condition .= $this->getDependCondition();

      $params = $this->getDependParams();
      $params[':url'] = $this->owner[$this->urlAttributeName];

      if (!$this->owner->isNewRecord) {
        foreach ($pks as $i => $pk) {
          $params[':pk' . $i] = $this->owner->getAttribute($pk);
        }
      }

      $modelClass = get_class($this->owner);
      $class = new $modelClass();
      if ($class->count($condition, $params) > 0) {
        $this->owner->addError('url', Yii::t('admin.content.errors', 'URL is already taken by another record.'));
      }
    }
  }

  protected function getDependCondition()
  {
    $condition = '';

    foreach ($this->getDependAttributes() as $i => $attributeName) {
      $condition .= ' AND ' . $this->owner->quoteColumn($attributeName) . '=' . self::CONDITION_PREFIX . $i;
    }

    return $condition;
  }

  protected function getDependParams()
  {
    $params = array();

    foreach ($this->getDependAttributes() as $i => $attributeName) {
      $params[self::CONDITION_PREFIX . $i] = $this->owner[$attributeName];
    }

    return $params;
  }

  protected function getDependAttributes()
  {
    if ($this->_attributesFiltered === null) {
      $this->_attributesFiltered = array();

      if (is_array($this->dependOnAttributes) && count($this->dependOnAttributes)) {
        foreach ($this->dependOnAttributes as $attributeName) {
          if ($this->owner->hasAttribute($attributeName)) {
            $this->_attributesFiltered[] = $attributeName;
          }
        }
      }
    }

    return $this->_attributesFiltered;
  }

  public function formatHumanReadableUrl($sourceText, $maxLength = self::URL_MAX_LENGTH)
  {
    $url = '';

    if ($sourceText) {
      $url = $this->translit($sourceText);
      if (strlen($url) > $maxLength) {
        $url = substr($url, 0, $maxLength);
      }
    }

    $tempUrl = $url;
    $number = 2;
    $modelClass = get_class($this->owner);
    $class = new $modelClass();
    while ($class->countByAttributes(array($this->urlAttributeName => $url)) > 0) {
      $url = $tempUrl . '-' . $number;
      $number++;
    }

    return $url;
  }

  protected function translit($input)
  {
    // case conversion
    $input = mb_convert_case($input, MB_CASE_LOWER, Yii::app()->charset);

    // cyrillic to ascii conversion
    $input = strtr($input, array(
      'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo',
      'Ж' => 'Zh', 'З' => 'Z', 'И' => 'I', 'Й' => 'Iy', 'К' => 'K', 'Л' => 'L', 'М' => 'M',
      'Н' => 'N', 'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U',
      'Ф' => 'F', 'Х' => 'Kh', 'Ц' => 'Tz', 'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch',
      'Ь' => '', 'Ы' => 'Y', 'Ъ' => '', 'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
      'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo',
      'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'iy', 'к' => 'k', 'л' => 'l', 'м' => 'm',
      'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u',
      'ф' => 'f', 'х' => 'kh', 'ц' => 'tz', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
      'ь' => '', 'ы' => 'y', 'ъ' => '', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
    ));

    // another chars to ascii conversion
    $input = iconv(Yii::app()->charset, 'ASCII//TRANSLIT', $input);

    // convert spaces to delimiters
    $input = preg_replace('/[^\w\d]/', self::URL_WORDS_DELIMITER_CHAR, $input);

    // remove double delimiters
    $input = preg_replace('/[' . preg_quote(self::URL_WORDS_DELIMITER_CHAR) . ']+/', self::URL_WORDS_DELIMITER_CHAR, $input);

    // trim input
    $input = trim($input, ' ,.-');

    return $input;
  }
}
