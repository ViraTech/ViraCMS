<?php
/**
 * GUID v4 generator component
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VGuidComponent extends VApplicationComponent
{
  /**
   * Generate straight GUID
   * @return string GUID
   */
  public function straight()
  {
    $guid = '';
    list($micro, $time) = explode(' ', microtime());
    $guid .= implode('', array_map('chr', array_reverse(unpack('C*', pack('L', $time)))));
    $guid .= implode('', array_map('chr', array_reverse(unpack('C*', pack('f', $micro)))));

    for ($i = 0; $i < 8; $i++) {
      $guid .= chr(mt_rand(0, 255));
    }

    return $this->format($guid);
  }

  /**
   * Generate random GUID
   * @return string GUID
   */
  public function random()
  {
    $guid = '';

    for ($i = 0; $i < 16; $i++) {
      $guid .= chr(mt_rand(0, 255));
    }

    return $this->format($guid);
  }

  /**
   * Format GUID to string
   * @param string $guid binary string representing generated GUID
   * @return string standard string representation of the GUIDs
   */
  private function format($guid)
  {
    $guid[6] = chr(ord($guid[6]) & 0x0f | 0x40); // set version to 0100
    $guid[8] = chr(ord($guid[8]) & 0x3f | 0x80); // set bits 6-7 to 10

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($guid), 4));
  }
}
