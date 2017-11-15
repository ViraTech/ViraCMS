<?php
/**
 * ViraCMS File Helper Function Class
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VFileHelper extends CFileHelper
{
  /**
   * Copy file with checking of existance
   * @param string $file source file (full path)
   * @param string $dir destination directory
   * @param integer $mode the file access mode
   * @param integer $dirMode the directory access mode
   * @return boolean the operation result
   */
  public static function copyFile($file, $dir, $mode = 0644, $dirMode = 0755)
  {
    $name = basename($file);

    if (!file_exists($file)) {
      return false;
    }

    if (!file_exists($dir)) {
      mkdir($dir, $dirMode, true);
    }

    if (copy($file, $dir . DIRECTORY_SEPARATOR . $name)) {
      chmod($dir . DIRECTORY_SEPARATOR . $name, $mode);
      return true;
    }

    return false;
  }

  /**
   * Copy list of files
   * @param array $files the files list (can be stripped)
   * @param string $dstDir the destination directory
   * @param integer $mode the file access mode
   * @return boolean the operation result (false if at least one of files cannot be copied)
   */
  public static function copyFiles($files, $dstDir, $srcDir = null, $mode = 0644)
  {
    $result = true;

    foreach ($files as $file) {
      $dir = $dstDir . DIRECTORY_SEPARATOR . dirname($file);
      $file = realpath($srcDir . DIRECTORY_SEPARATOR . $file);
      $result &= self::copyFile($file, $dir, $mode);
    }

    return $result;
  }

  /**
   * UTF-8 compatible path info
   * @param string $path file path
   * @param integer $info see PATHINFO constants
   * @return mixed
   */
  public static function getPathInfo($path, $info = 0)
  {
    preg_match('%^(.*?)[\\\\/]*(([^/\\\\]*?)(\.([^\.\\\\/]+?)|))[\\\\/\.]*$%imu', $path, $matches);
    $return = array(
      'dirname' => isset($matches[1]) ? $matches[1] : '',
      'basename' => isset($matches[2]) ? $matches[2] : '',
      'extension' => isset($matches[5]) ? $matches[5] : '',
      'filename' => isset($matches[3]) ? $matches[3] : '',
    );

    switch ($info) {
      case PATHINFO_DIRNAME:
        return $return['dirname'];
      case PATHINFO_BASENAME:
        return $return['basename'];
      case PATHINFO_EXTENSION:
        return $return['extension'];
      case PATHINFO_FILENAME:
        return $return['filename'];
    }

    return $return;
  }

  /**
   * Removes directory and any files found into it
   * @param string $dir Directory to delete
   */
  public static function deleteDirectory($dir)
  {
    if (file_exists($dir) && is_dir($dir)) {
      self::deleteDirectoryRecursive($dir);
    }
  }

  /**
   * Recursive function for directory removal
   * @param type $dir
   */
  protected static function deleteDirectoryRecursive($dir)
  {
    $dir = rtrim($dir, ' /\\');
    $contents = glob($dir . DIRECTORY_SEPARATOR . '*');
    foreach ($contents as $object) {
      if (is_dir($object)) {
        self::deleteDirectoryRecursive($object);
      }
      else {
        @unlink($object);
      }
    }
    @rmdir($dir);
  }

  /**
   * Checks if directory is empty
   * @param string $dir directory path
   * @return null|boolean true if directory exists, readable and empty, false if directory is not empty, null otherwise
   */
  public static function isDirEmpty($dir)
  {
    if (!file_exists($dir) || !is_dir($dir) || !is_readable($dir)) {
      return null;
    }

    $content = glob(rtrim($dir, ' /\\') . DIRECTORY_SEPARATOR . '*');

    if ($content && is_array($content) && count($content)) {
      return false;
    }

    return true;
  }

  /**
   * Check if directory writeable
   * @param string $dir directory path
   * @return null|boolean true if directory exists and writeable, false if not writeable, null otherwise
   */
  public static function isDirWriteable($dir)
  {
    if (!file_exists($dir) || !is_dir($dir) || !is_readable($dir)) {
      return null;
    }

    $tmpFile = $dir . DIRECTORY_SEPARATOR . '.tmp';
    @touch($tmpFile);
    $writeable = file_exists($tmpFile) && is_writable($tmpFile);
    @unlink($tmpFile);

    return $writeable;
  }

  /**
   * Find the files and strip source directory from each file
   * @param string $dir the source directory
   * @return array
   */
  public static function findFilesStripDir($dir)
  {
    $files = VFileHelper::findFiles($dir);

    if (!empty($files)) {
      foreach ($files as &$file) {
        $file = ltrim(str_replace($dir, '', $file), DIRECTORY_SEPARATOR);
      }
      arsort($files);
    }

    return is_array($files) ? $files : array();
  }

  /**
   * Check for file conflicts
   * @param string $srcDir the source directory
   * @param string $dstDir the destination directory
   * @param array $whiteList the whitelisted files
   * @return array conflicted files
   */
  public static function checkFilesConflicts($srcDir, $dstDir, $whiteList = array())
  {
    $conflicts = array();
    $dstDir = rtrim($dstDir, ' ' . DIRECTORY_SEPARATOR);

    foreach (self::findFilesStripDir($srcDir) as $file) {
      if (in_array($file, $whiteList)) {
        continue;
      }

      $dstFile = $dstDir . DIRECTORY_SEPARATOR . $file;

      if (file_exists($dstFile)) {
        $conflicts[] = $dstFile;
      }
    }

    return $conflicts;
  }

  /**
   *
   * @param string $dir the directory when php files will be searched
   * @return type
   */
  public static function checkPhpFilesSyntax($dir)
  {
    $failed = array();

    // check syntax of php files
    if (function_exists('exec')) {
      // find interpreter
      $php = '';
      $ext = stristr(PHP_OS, 'WIN') != '' ? '.exe' : '';
      foreach (array('php', 'php5', 'php5.1', 'php5.2', 'php5.3', 'php5.4', 'php5.5', 'php5.6') as $binary) {
        $interpreter = PHP_BINDIR . DIRECTORY_SEPARATOR . $binary . $ext;
        if (@file_exists($interpreter) && @is_readable($interpreter) && @is_executable($interpreter)) {
          $php = $interpreter;
          break;
        }
      }
      if ($php && exec($php . ' -h') == 0) {
        foreach (self::findFiles($dir, array('fileTypes' => array('php'))) as $file) {
          $output = '';
          $success = 0;
          exec($php . ' -l ' . $file . ' 2>&1', $output, $success);
          if ($success != 0) {
            if (is_array($output)) {
              $output = implode(' ', $output);
            }
            $failed[$file] = $output;
          }
        }
      }
    }

    return $failed;
  }

  /**
   * Check for write access to files and directories
   * @param string $srcDir the source directory
   * @param string $dstDir the destination directory
   * @return array the files/directories write has been denied
   */
  public static function checkFilesAccess($srcDir, $dstDir)
  {
    $denied = array();
    $dstDir = rtrim($dstDir, ' ' . DIRECTORY_SEPARATOR);
    $dirs = array();

    foreach (self::findFilesStripDir($srcDir) as $file) {
      $dstFile = $dstDir . DIRECTORY_SEPARATOR . $file;
      if (file_exists($dstFile)) {
        if (!is_writable($dstFile)) {
          $denied[] = $dstFile;
        }
      }
      $dirs[] = dirname($dstFile);
    }

    foreach (array_unique($dirs) as $dir) {
      while (!file_exists($dir)) {
        $dir = dirname($dir);
      }
      if (!is_writable($dir)) {
        $denied[] = $dir;
      }
    }

    arsort($denied);

    return $denied;
  }

  /**
   * Compares two lists of files for it's names and contents
   * @param array $list1 the first list (list must be stripped from parent directory)
   * @param array $list2 the second list (list must be stripped from parent directory)
   * @param string $list1Dir the directory which contain files from the first list
   * @param string $list2Dir the directory which contain files from the second list
   * @return array the list of difference set in 'added', 'changed' and 'absent' subarrays
   */
  public static function compareFiles($list1, $list2, $list1Dir, $list2Dir)
  {
    $difference = array(
      'added' => array(),
      'changed' => array(),
      'absent' => array(),
    );

    foreach ($list1 as $file) {
      $srcPath = realpath($list1Dir . DIRECTORY_SEPARATOR . $file);

      if (!$srcPath) {
        continue;
      }

      if (in_array($file, $list2)) {
        $dstPath = realpath($list2Dir . DIRECTORY_SEPARATOR . $file);

        if (($srcPath && !$dstPath) || md5_file($srcPath) != md5_file($dstPath)) {
          $difference['changed'][] = $file;
        }
      }
      else {
        $difference['added'][] = $file;
      }
    }

    foreach ($list2 as $file) {
      $srcPath = realpath($list2Dir . DIRECTORY_SEPARATOR . $file);

      if (!$srcPath) {
        continue;
      }

      if (!in_array($file, $list1)) {
        $difference['absent'][] = $file;
      }
    }

    return $difference;
  }

  /**
   * Send file helper
   * @param mixed $object One of VContentFile, VContentMedia or VContentImage classes object, or file path, or file resource
   * @param string $filename (optional) forced download file name
   */
  public static function sendFile($object, $filename = null)
  {
    if ($object instanceof VContentFile || $object instanceof VContentMedia || $object instanceof VContentImage) {
      $file = Yii::app()->storage->getFilePath($object->path);
      $filepath = $file;
      $filename = $filename == null ? $object->filename : $filename;
      $filesize = filesize($file);
      $mime = $object->mime;
    }
    else {
      $file = $object;

      if (is_resource($file)) {
        $metadata = stream_get_meta_data($file);
        if (empty($metadata['uri'])) {
          return;
        }
        $filepath = $metadata['uri'];
      }
      else {
        if (!file_exists($file)) {
          return;
        }
        $filepath = $file;
      }

      $filename = $filename == null ? basename($filepath) : $filename;
      $filesize = filesize($filepath);
      $mime = self::getMimeTypeByExtension($filename);
    }

    if (!is_resource($file)) {
      $file = fopen($file, 'rb');
    }

    $range = 0;
    if (isset($_SERVER['HTTP_RANGE'])) {
      $range = str_replace('bytes=', '', $_SERVER['HTTP_RANGE']);
      list($range, $end) = explode('-', $range);
      if ($range > 0) {
        fseek($file, $range);
      }
    }
    else {
      rewind($file);
    }

    header($_SERVER['SERVER_PROTOCOL'] . ($range ? ' 206 Partial Content' : ' 200 OK'));
    header('Content-Disposition: attachment; filename=' . $filename);
    header('Last-Modified: ' . date('D, d M Y H:i:s T', filemtime($filepath)));
    header('Accept-Ranges: bytes');
    header('Content-Length: ' . ($filesize - $range));
    if ($range) {
      header('Content-Range: bytes ' . $range . '- ' . ($filesize - 1) . '/' . $filesize);
    }
    header('Content-Type: ' . $mime);

    fpassthru($file);
    fclose($file);
  }

  /**
   * Unlink files and remove empty directories
   * Note that file paths must be relative to application root (parent of 'protected' folder)
   * @param array $files files list
   * @param string $baseDir the base directory (if files list is stripped)
   */
  public static function unlinkFiles($files, $baseDir = null)
  {
    // unlink files
    foreach ($files as $file) {
      $file = realpath($baseDir . DIRECTORY_SEPARATOR . $file);
      @unlink($file);
    }

    // unlink empty directories
    foreach (array_reverse($files) as $file) {
      $dir = realpath(dirname($baseDir . DIRECTORY_SEPARATOR . $file));
      while (file_exists($dir) && is_dir($dir) && self::isDirEmpty($dir)) {
        rmdir($dir);
        $dir = dirname($dir);
      }
    }
  }

  /**
   * Verifies ZIP file signature
   * @param string $filename the file name
   * @return boolean
   */
  public static function isZipArchive($filename)
  {
    $f = fopen($filename, 'rb');
    $signature = fread($f, 4);
    fclose($f);

    return $signature === "\x50\x4b\x3\x4";
  }
}
