<?php
/**
 * ViraCMS Cache Control Helper Function Class
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VCacheHelper
{
  /**
   * Flush all of cache sections
   */
  public static function flush()
  {
    self::flushOpcodeCache();
    self::flushAppCache();
    self::flushConfigCache();
    self::flushImageCache();
    self::flushAssetsCache();
  }

  /**
   * Flushes PHP opcode cache
   */
  public static function flushOpcodeCache()
  {
    // apc
    if (function_exists('apc_clear_cache')) {
      apc_clear_cache();
    }
    // zend
    if (function_exists('opcache_reset')) {
      opcache_reset();
    }
    // xcache
    if (function_exists('xcache_clear_cache')) {
      xcache_clear_cache();
    }
  }

  /**
   * Flushes application cache (@see CCache)
   */
  public static function flushAppCache()
  {
    if (Yii::app()->hasComponent('cache')) {
      Yii::app()->cache->flush();
    }
  }

  /**
   * Unlink application configuration cache files (runtimePath / *.cache)
   */
  public static function flushConfigCache()
  {
    $files = glob(Yii::app()->runtimePath . DIRECTORY_SEPARATOR . '*.cache');
    if (is_array($files) && count($files)) {
      foreach ($files as $file) {
        if (file_exists($file) && is_file($file) && is_writable($file)) {
          @unlink($file);
        }
      }
    }
  }

  /**
   * Clear image cache files (webroot/cache)
   */
  public static function flushImageCache()
  {
    self::cleanDirectory(Yii::getPathOfAlias('webroot.cache'));
  }

  /**
   * Clear published assets cache (webroot/assets)
   */
  public static function flushAssetsCache()
  {
    if (Yii::app()->hasComponent('assetManager')) {
      $baseDir = Yii::app()->assetManager->basePath;
    }
    else {
      $baseDir = Yii::getPathOfAlias('webroot.assets');
    }

    self::cleanDirectory($baseDir);
  }

  /**
   * Totally clean provided directory if it's exist
   * @param string $dir directory full path
   */
  protected static function cleanDirectory($dir)
  {
    if ($dir && file_exists($dir) && is_dir($dir)) {
      $files = glob($dir . DIRECTORY_SEPARATOR . '*');
      if (is_array($files) && count($files)) {
        foreach ($files as $filename) {
          if (is_link($filename) || is_file($filename)) {
            @unlink($filename);
          }
          elseif (is_dir($filename)) {
            VFileHelper::deleteDirectory($filename);
          }
        }
      }
    }
  }
}
