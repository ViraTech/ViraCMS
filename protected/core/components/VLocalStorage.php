<?php
/**
 * ViraCMS Local Files Storage Component
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VLocalStorage extends VApplicationComponent
{
  /**
   * @var string storage root directory
   */
  public $directory = '';

  /**
   * @var boolean use subdirectories to store files
   */
  public $subDirectory = true;

  /**
   * @var integer subdirectories structure level
   */
  public $subDirectoryLevel = 2;

  /**
   * @var integer how many symbols of file name will be in one level chunk
   */
  public $subDirectoryChunk = 2;

  /**
   * Extensions rewrite
   * @var array
   */
  public $rewriteExtensions = array(
    'pl' => 'txt',
    'php' => 'txt',
    'php3' => 'txt',
    'php4' => 'txt',
    'php5' => 'txt',
    'phtml' => 'txt',
  );

  /**
   * Component init
   */
  public function init()
  {
    if ($this->subDirectoryChunk < 1) {
      $this->subDirectoryChunk = 1;
    }

    $this->directory = Yii::getPathOfAlias($this->directory);

    parent::init();
  }

  /**
   * Adds a file to storage
   * @param string $fileName Full path to file which need to store
   * @return string new file name
   * @throws CException
   */
  public function addFile($filePath, $originalName = null, $deleteOriginal = true)
  {
    if (empty($originalName)) {
      $originalName = $this->getName($filePath) . '.' . $this->getExtension($filePath);
    }

    if (!file_exists($filePath) || !is_file($filePath)) {
      throw new CException(Yii::t('common', "File {file} isn't found", array('{file}' => $filePath)));
    }

    if (!is_readable($filePath)) {
      throw new CException(Yii::t('common', "File {file} isn't accessible", array('{file}' => $filePath)));
    }

    $newFileName = $this->createName($originalName);
    $newDir = $this->getFileDir($newFileName);
    $newPath = $newDir . $newFileName;

    $result = false;
    if ($deleteOriginal) {
      $result = rename($filePath, $newPath);
    }
    if (!$result) {
      @copy($filePath, $newPath);
    }
    @chmod($newPath, 0666);
    if ($deleteOriginal && file_exists($filePath)) {
      @unlink($filePath);
    }

    if (!file_exists($newPath) || !is_readable($newPath)) {
      throw new CException(Yii::t('common', "New file {file} into directory {dir} isn't accessible", array('{file}' => basename($newPath), '{dir}' => dirname($newPath))));
    }

    $this->onAddFile(new CEvent($this, array(
      'name' => $newFileName,
      'original' => $originalName,
    )));

    return $newFileName;
  }

  /**
   * Check if file with given name is exists in storage
   * @param string $fileName File name generated by VStorage::addFile
   * @return boolean
   */
  public function fileExists($fileName)
  {
    return file_exists($this->getFilePath($fileName));
  }

  /**
   * Removes file from storage
   * @param string $fileName File name generated by VStorage::addFile
   * @return boolean File was successfully removed
   */
  public function deleteFile($fileName)
  {
    if ($fileName) {
      $dir = $this->getFileDir($fileName);

      if (file_exists($dir . $fileName)) {
        if (is_writable($dir) && is_writable($dir . $fileName)) {
          @unlink($dir . $fileName);
          @rmdir($dir . VFileHelper::getPathInfo($fileName, PATHINFO_DIRNAME));
        }
        else {
          return false;
        }
      }
      for ($i = 0; $i < $this->subDirectoryLevel; $i++) {
        if (VFileHelper::isDirEmpty($dir)) {
          @rmdir($dir);
        }
        $dir = dirname($dir);
      }

      $this->onDeleteFile(new CEvent($this, array(
        'name' => $fileName,
      )));
    }

    return true;
  }

  /**
   * Event fires after file has been added to storage
   * @param mixed $event event
   */
  public function onAddFile($event)
  {
    $this->raiseEvent('onAddFile', $event);
  }

  /**
   * Event fires after file has been removed from storage
   * @param mixed $event event
   */
  public function onDeleteFile($event)
  {
    $this->raiseEvent('onDeleteFile', $event);
  }

  /**
   * Returns a storage' directory path to file
   * @param string $fileName file name
   * @return string directory path
   */
  public function getFileDir($fileName)
  {
    $dir = $this->directory . DIRECTORY_SEPARATOR;
    $this->checkDir($dir);

    if ($this->subDirectory) {
      for ($i = 0; $i < $this->subDirectoryLevel; $i++) {
        $chunk = mb_substr($fileName, $i * $this->subDirectoryChunk, $this->subDirectoryChunk, Yii::app()->charset);
        $dir .= $chunk . DIRECTORY_SEPARATOR;
        $this->checkDir($dir);
      }
    }

    $this->checkDir(dirname($dir . DIRECTORY_SEPARATOR . $fileName));

    return $dir;
  }

  /**
   * Returns direct URL to file
   * @param string $fileName file name on disk
   * @param boolean $absolute return absolute url
   * @return string file URL
   */
  public function getFileUrl($fileName, $absolute = false)
  {
    $baseUrl = $this->getFileDir($fileName);
    $baseUrl = str_replace(Yii::getPathOfAlias('webroot'), '', $baseUrl);
    $baseUrl = str_replace(DIRECTORY_SEPARATOR, '/', $baseUrl);

    return ($absolute ? Yii::app()->request->getHostInfo() : '') . $baseUrl . $fileName;
  }

  /**
   * Returns full file path
   * @param string $fileName file name
   * @return string
   */
  public function getFilePath($fileName)
  {
    return $this->getFileDir($fileName) . $fileName;
  }

  /**
   * Returns uploaded file size
   * @param string $fileName file name
   * @return integer
   */
  public function getFileSize($fileName)
  {
    $file = $this->getFileDir($fileName) . $fileName;
    return file_exists($file) ? filesize($file) : 0;
  }

  /**
   * Creates new file name for storage (may be with part of the path)
   * @param string $originalName
   * @return string
   */
  protected function createName($originalName)
  {
    return md5(time() . $originalName) . DIRECTORY_SEPARATOR . $this->getName($originalName) . '.' . $this->getExtension($originalName);
  }

  /**
   * Get file name exposed from it's path
   * @param string $filePath file path
   * @return string
   */
  protected function getName($filePath)
  {
    return VFileHelper::getPathInfo($filePath, PATHINFO_FILENAME);
  }

  /**
   * Get file extension exposed from it's path
   * @param string $filePath file path
   * @return string
   */
  protected function getExtension($filePath)
  {
    $ext = VFileHelper::getExtension($filePath);

    if (isset($this->rewriteExtensions[$ext])) {
      $ext = $this->rewriteExtensions[$ext];
    }

    return empty($ext) ? '' : mb_convert_case($ext, MB_CASE_LOWER, Yii::app()->charset);
  }

  /**
   * Checks directory for exists and writeable
   * @param string $dir directory path
   * @return boolean
   * @throws CException
   */
  protected function checkDir($dir)
  {
    if (!file_exists($dir)) {
      @mkdir($dir, 0777);
      @chmod($dir, 0777);
    }

    if (!file_exists($dir) || !is_dir($dir)) {
      throw new CException(Yii::t('common', "Storage directory {dir} isn't found and can't created", array('{dir}' => $dir)));
    }

    if (!is_writable($dir)) {
      @chmod($dir, 0777);
    }

    if (!is_writable($dir)) {
      throw new CException(Yii::t('common', "Storage directory {dir} isn't writeable", array('{dir}' => $dir)));
    }

    return true;
  }
}
