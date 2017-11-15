<?php
/**
 * ViraCMS URI Download Behavior
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VDownloadBehavior extends CBehavior
{
  /**
   * Retrieves and returns requested content as JSON
   * @param string $url the URL
   * @return mixed JSON decoded content (usually array)
   */
  public function asJson($url)
  {
    return CJSON::decode($this->asText($url));
  }

  /**
   * Retrieves and returns requested URL content as text
   * @param string $url URL
   * @return mixed the content downloaded
   */
  public function asText($url)
  {
    $tmp = tempnam(Yii::app()->runtimePath, 'udl');
    $this->asFile($url, $tmp);
    $content = file_get_contents($tmp);
    @unlink($tmp);

    return $content;
  }

  /**
   * Downloads file by the first available method and save it
   * @param string $url the URL to download
   * @param string $dstFile the path to the local file
   * @return boolean the operation result
   */
  public function asFile($url, $dstFile)
  {
    set_time_limit(30);
    $success = false;

    $license = 'X-Vira-License: ' . (Yii::app()->licenseKey ? Yii::app()->licenseKey : 'None');

    if (ini_get('allow_url_fopen')) {
      $context = stream_context_create(array(
        'http' => array(
          'method' => "GET",
          'header' => $license . "\r\n",
        ),
      ));

      if (($src = @fopen($url, 'rb', false, $context)) && ($dst = @fopen($dstFile, 'wb'))) {
        stream_copy_to_stream($src, $dst);
        fclose($src);
        fclose($dst);
        $success = true;
      }
    }

    if (!$success && function_exists('curl_init')) {
      $src = curl_init();
      $dst = @fopen($dstFile, 'wb');
      curl_setopt_array($src, array(
        CURLOPT_URL => $url,
        CURLOPT_FILE => $dst,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_HEADER => array(
          $license,
        ),
      ));
      if (curl_exec($src)) {
        $success = true;
      }
      curl_close($src);
      fclose($dst);
    }

    if (!$success && function_exists('fsockopen')) {
      $host = parse_url($url, PHP_URL_HOST);
      if (($src = @fsockopen($host, 80, $errno, $errstr, 20)) && ($dst = @fopen($dstFile, "wb"))) {
        $uri = parse_url($url, PHP_URL_PATH);
        $args = parse_url($url, PHP_URL_QUERY);
        if ($args) {
          $uri .= '?' . $args;
        }

        fwrite($src, "GET  " . $uri . " HTTP/1.0\r\n" .
          "Host: " . $host . "\r\n" .
          $license . "\r\n" .
          "Connection: close\r\n\r\n"
        );

        $eoh = false; // end of headers
        while (!feof($src)) {
          if ($eoh) {
            fwrite($dst, fread($src, 16384), 16384);
          }
          else {
            if (fgets($src) == "\r\n") {
              $eoh = true;
            }
          }
        }
        fclose($src);
        fclose($dst);
      }
    }

    if (file_exists($dstFile)) {
      @chmod($dstFile, 0666);
    }

    return $success;
  }
}
