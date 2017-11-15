<?php
/**
 * Table renderer for console commands behavior
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VConsoleTableBehavior extends CBehavior
{
  /**
   * Render table in console commands
   * @param array $cellsWidth column widths
   * @param array $headerTitles column header titles
   * @param array $tableData table body data
   * @param boolean $renderFooter enable footer rendering
   */
  public function renderTable($cellsWidth, $headerTitles, $tableData, $renderFooter = true)
  {
    $this->renderTableHeader($cellsWidth, $headerTitles);

    foreach ($tableData as &$data) {
      $lines = 0;

      foreach ($cellsWidth as $i => $width) {
        if (isset($data[$i])) {
          $data[$i] = explode("\n", $this->wordwrap($data[$i], $width, "\n", true));
          $lines = max($lines, count($data[$i]));
        }
      }

      for ($l = 0; $l < $lines; $l++) {
        echo ' ';
        foreach ($cellsWidth as $i => $width) {
          echo $this->strpad(isset($data[$i][$l]) ? $data[$i][$l] : '', $width);
          if ($i + 1 < count($cellsWidth)) {
            echo ' | ';
          }
        }
        echo PHP_EOL;
      }
    }

    if ($renderFooter) {
      $this->renderTableFooter($cellsWidth);
    }
  }

  /**
   * Render table header
   * @param array $cellsWidth column widths
   * @param array $headerTitles column header titles
   */
  public function renderTableHeader($cellsWidth, $headerTitles)
  {
    $this->renderTableLine($cellsWidth);
    echo ' ';
    foreach ($cellsWidth as $i => $width) {
      $title = isset($headerTitles[$i]) ? $headerTitles[$i] : '';
      echo $this->strpad(mb_strlen($title, Yii::app()->charset) > $width ? mb_strcut($title, 0, $width, Yii::app()->charset) : $title, $width);
      if ($i + 1 < count($cellsWidth)) {
        echo ' | ';
      }
    }
    echo PHP_EOL;
    $this->renderTableLine($cellsWidth);
  }

  /**
   * Render table delimiter line
   * @param array $cellsWidth column widths
   */
  public function renderTableLine($cellsWidth)
  {
    echo '-';
    foreach ($cellsWidth as $i => $width) {
      echo $this->strpad('', $width, '-');
      if ($i + 1 < count($cellsWidth)) {
        echo '-+-';
      }
    }
    echo PHP_EOL;
  }

  /**
   * Render table footer
   * @param array $cellsWidth column widths
   */
  public function renderTableFooter($cellsWidth)
  {
    $this->renderTableLine($cellsWidth);
  }

  /**
   * Pad string to provided length
   * @param string $string input stirng
   * @param integer $length length to be padded
   * @param string $character padding character (default is space)
   * @return string padded string
   */
  public function strpad($string, $length, $character = ' ')
  {
    $length -= mb_strlen($string, Yii::app()->charset);
    return $string . ($length > 0 ? str_pad('', $length, $character, STR_PAD_RIGHT) : '');
  }

  /**
   * Wrap text words
   * @param string $string input string
   * @param integer $lineLength max. length of the line
   * @param string $character delimit by character (default is caret return sign)
   * @param boolean $cut words can be cutted
   * @return string wrapped string
   */
  public function wordwrap($string, $lineLength = 80, $character = "\n", $cut = false)
  {
    $lines = explode($character, $string);

    foreach ($lines as &$line) {
      $line = rtrim($line);
      if (mb_strlen($line, Yii::app()->charset) <= $lineLength) {
        continue;
      }
      $words = explode(' ', $line);
      $line = '';
      $actual = '';
      foreach ($words as $word) {
        if (mb_strlen($actual . $word, Yii::app()->charset) <= $lineLength) {
          $actual .= $word . ' ';
        }
        else {
          if ($actual != '') {
            $line .= rtrim($actual) . $character;
          }
          $actual = $word;
          if ($cut) {
            while (mb_strlen($actual, Yii::app()->charset) > $lineLength) {
              $line .= mb_substr($actual, 0, $lineLength, Yii::app()->charset) . $character;
              $actual = mb_substr($actual, $lineLength, null, Yii::app()->charset);
            }
          }
          $actual .= ' ';
        }
      }
      $line .= trim($actual);
    }

    return implode($character, $lines);
  }
}
