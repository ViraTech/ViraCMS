<?php
/**
 * ViraCMS Content Formatter Component
 * Based On Yii Framework CFormatter Class
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VFormatter extends CFormatter
{
  /**
   * Default time zone
   * @var string
   */
  public $timeZone = 'UTC';

  const FORMAT_UPPERCASED = 0;
  const FORMAT_LOWERCASED = 1;
  const FORMAT_CAPITALIZED = 2;

  /**
   * Parse size provided as a string
   * @param string $value size as a string value
   * @return integer size as an integer value
   */
  public function parseSize($value)
  {
    $value = strtoupper(str_replace(',', '.', trim($value)));
    $suffix = trim(str_replace(array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '.', 'B'), '', $value));
    $value = floatval(preg_replace('/\D/', '', $value));
    switch ($suffix) {
      case 'K':
        $power = 1;
        break;
      case 'M':
        $power = 2;
        break;
      case 'G':
        $power = 3;
        break;
      case 'T':
        $power = 4;
        break;
      default:
        $power = 0;
    }

    return round($value * pow($this->sizeFormat['base'], $power));
  }

  /**
   * Formatting value to defined date format
   * @param mixed $value source value
   * @return string formatted date
   */
  public function formatDate($value)
  {
    if (empty($value)) {
      return '';
    }

    return parent::formatDate($value);
  }

  /**
   * Formatting value to defained date and time format
   * @param mixed $value source value
   * @return string formatted date and time
   */
  public function formatDatetime($value)
  {
    if (empty($value)) {
      return '';
    }

    return parent::formatDatetime($value);
  }

  /**
   * Format input integer as IPv4 address
   * @param integer $address IPv4 address as integer
   * @return string IPv4 address as string
   */
  public function formatIp4Address($address)
  {
    return long2ip($address);
  }

  /**
   * Format text as HTML paragraphs
   * @param string $source source text
   * @param string $htmlTag HTML tag
   * @param array $htmlOptions HTML options for top tag
   * @return string
   */
  public function formatParagraph($source, $htmlTag = 'p', $htmlOptions = array())
  {
    return CHtml::tag($htmlTag, $htmlOptions, implode(CHtml::closeTag($htmlTag) . CHtml::openTag($htmlTag), array_map('CHtml::encode', explode("\n", strtr($source, array("\r\n" => "\n"))))));
  }

  /**
   * Format name of the month
   * @param integer $month month number
   * @param integer $case case
   * @param mixed $short return short name of month (length can be specified, 3 letters by default)
   * @return string
   */
  public function formatMonth($month, $case = null, $short = false)
  {
    $monthName = $this->getMonthName($month);
    if ($case !== null) {
      $monthName = mb_convert_case($monthName, $case, Yii::app()->charset);
    }

    return $short ? mb_strcut($monthName, 0, $short === true ? 3 : $short, Yii::app()->charset) : $monthName;
  }

  /**
   * Return localized name of the month
   * @param integer $month month number
   * @return string
   */
  protected function getMonthName($month)
  {
    $names = array(
      1 => Yii::t('common', 'January'),
      2 => Yii::t('common', 'February'),
      3 => Yii::t('common', 'March'),
      4 => Yii::t('common', 'April'),
      5 => Yii::t('common', 'May'),
      6 => Yii::t('common', 'June'),
      7 => Yii::t('common', 'July'),
      8 => Yii::t('common', 'August'),
      9 => Yii::t('common', 'September'),
      10 => Yii::t('common', 'October'),
      11 => Yii::t('common', 'November'),
      12 => Yii::t('common', 'December'),
    );

    return isset($names[$month]) ? $names[$month] : $names[1];
  }

  /**
   * Format current time zone offset relative to UTC
   * @param string $timezone time zone
   * @return string formatted value, i.e. '+01:00'
   */
  public function formatTimezoneOffset($timezone = null)
  {
    $return = '+00:00';

    if ($timezone == null) {
      $timezone = $this->timeZone;
    }

    if ($timezone != 'UTC' && $this->getIsValidTimezone($timezone)) {
      $timezone = new DateTimeZone($timezone);

      if ($timezone) {
        $utcTime = new DateTime('now', new DateTimeZone('UTC'));
        $offset = (int) $timezone->getOffset($utcTime);
        $sign = $offset < 0 ? '-' : '+';
        $hours = round(abs($offset / 3600));
        $mins = round((abs($offset) - $hours * 3600) / 60);
        $return = sprintf('%s%02d:%02d', $sign, $hours, $mins);
      }
    }

    return $return;
  }

  /**
   * Timezone check for validity
   * @param string $timezone time zone string
   */
  protected function getIsValidTimezone($timezone)
  {
    $timezones = array_flip(timezone_identifiers_list());

    return isset($timezones[$timezone]);
  }
}
