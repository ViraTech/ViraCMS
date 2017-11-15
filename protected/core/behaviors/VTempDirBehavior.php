<?php

/**
 * ViraCMS Temporary Directory & Files Management Behavior
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VTempDirBehavior extends CBehavior
{

  /**
   * @var string the base directory where temporary directories will be created
   */
  public $baseDir;

  /**
   * @var string the temporary directory name will be started with
   */
  public $delimiter = 'tmp';

  /**
   * @var string the created temporary directory
   */
  private $_tempDir;

  /**
   * Behavior initialization
   */
  function __construct()
  {
    if (empty($this->baseDir)) {
      $this->baseDir = Yii::app()->runtimePath;
    }

    Yii::app()->attachEventHandler('onEndRequest', array($this, 'unsetTempDir'));
  }

  /**
   * Creates new temporary directory
   * @return string created temporary directory path
   */
  protected function createTempDir()
  {
    do {
      $tempDir = Yii::app()->runtimePath . DIRECTORY_SEPARATOR . $this->delimiter . substr(md5(time()), 2, 8);
    } while (file_exists($tempDir));

    mkdir($tempDir, 0777);

    return $tempDir;
  }

  /**
   * Returns temporary directory path
   * @return string
   */
  public function getTempDir()
  {
    if ($this->_tempDir === null) {
      $this->_tempDir = $this->createTempDir();
    }

    return $this->_tempDir;
  }

  /**
   * Manually sets temporary directory path. The directory must exist.
   * @param string $dir the temporary directory path
   * @return string the current directory path
   */
  public function setTempDir($dir)
  {
    $this->unsetTempDir();
    if (file_exists($dir) && is_dir($dir)) {
      $this->_tempDir = $dir;
    }

    return $this->_tempDir;
  }

  /**
   * Removes temporary directory with it's contents
   * @return boolean
   */
  public function deleteTempDir()
  {
    if ($this->_tempDir !== null) {
      VFileHelper::deleteDirectory($this->_tempDir);
      $this->_tempDir = null;

      return true;
    }

    return false;
  }

  /**
   * The CWebApplication onEndRequest event handler. Please do not execute directly.
   * @param CEvent $event the event
   */
  public function unsetTempDir($event)
  {
    $this->deleteTempDir();
  }

  /**
   * Returns temporary file name inside temporary directory
   * @param string $filename the file name
   * @return string
   */
  public function getTempFile($filename)
  {
    return $this->getTempDir() . DIRECTORY_SEPARATOR . $filename;
  }
}
