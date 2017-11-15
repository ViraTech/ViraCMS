<?php
/**
 * ViraCMS Message Translation Component
 *
 * It's a hybrid based on CDbMessageSource, CPhpMessageSource and CMessageSource,
 * rewritten accordingly to ViraCMS hierarchical modules structure
 *
 * @package vira.core.core
 * @subpackage vira.core.translate
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VMessageSource extends VApplicationComponent
{
  const CACHE_KEY_PREFIX_FILE = 'Vira.MessageSource.File';
  const CACHE_KEY_PREFIX_DB = 'Vira.MessageSource.DB';

  /**
   * @var integer the time in seconds that the messages can remain valid in cache
   * Defaults to 0, meaning the caching is disabled
   */
  public $cachingDuration = 0;

  /**
   * @var string the ID of the cache application component that is used to cache the messages
   * Defaults to 'cache' which refers to the primary cache application component
   * Set this property to false if you want to disable caching the messages
   */
  public $cacheID = 'cache';

  /**
   * @var boolean whether to force message translation when the source and target languages are the same
   * Defaults to false, meaning translation is only performed when source and target languages are different
   */
  public $forceTranslation = false;

  /**
   * @var string the ID of the database connection application component
   */
  public $connectionID = 'db';

  /**
   * @var string Source messages model class name
   */
  public $sourceMessageTable = '{{core_translate_source}}';

  /**
   * @var string Message translations model class name
   */
  public $translateMessageTable = '{{core_translate}}';

  /**
   * @var string Dominant storage replaces identical messages from other storage type. Can be 'db' or 'file'
   * Defaults to 'db'
   */
  public $dominantStorage = 'file';

  /**
   * @var CDbConnection database connection pointer
   */
  private $_db;

  /**
   * @var string source language
   */
  private $_language;

  /**
   * @var array translated messages cache
   */
  private $_messages = array();

  /**
   * @var array file names cache
   */
  private $_files = array();

  /**
   * Determine file name and path depends on module name (if any), category name, language
   *
   * @param string $category category name
   * @param string $language language
   * @return string the message file path
   */
  protected function getMessageFile($module, $category, $language)
  {
    $path = empty($module) ? 'application.messages' : $this->getModuleMessagesPath($module);
    if (!isset($this->_files[$path][$category][$language])) {
      $this->_files[$path][$category][$language] = Yii::getPathOfAlias("$path.$language.$category") . '.php';
    }

    return $this->_files[$path][$category][$language];
  }

  /**
   * Loads the message translation for the specified language and category.
   * First, tries to locate message file, then checks database.
   *
   * @param string $language the target language
   * @param string $category the message category
   * @param string $module module ID
   * @return array the loaded messages
   */
  protected function loadMessages($language, $category, $module = null)
  {
    $dependency = null;
    $cache = empty($this->cacheID) ? null : Yii::app()->getComponent($this->cacheID);
    $key = ($module ? $module . '.' : '') . $category . '.' . $language;

    if ($this->cachingDuration > 0 && $cache !== null) {
      $fileData = $cache->get(self::CACHE_KEY_PREFIX_FILE . $key);
    }

    if (empty($fileData)) {
      $fileData = array();
      $messageFile = $this->getMessageFile($module, $category, $language);
      if (is_file($messageFile)) {
        $fileData = include($messageFile);
        $fileDependency = new CFileCacheDependency($messageFile);
        if (is_array($fileData) && count($fileData) && $this->cachingDuration > 0 && $cache !== null) {
          $cache->set(self::CACHE_KEY_PREFIX_FILE . $key, $fileData, $this->cachingDuration, $fileDependency);
        }
      }
    }

    $dbData = $this->cachingDuration > 0 && $cache !== null ? $cache->get(self::CACHE_KEY_PREFIX_DB . $key) : false;

    if ($dbData === false) {
      $dbData = array();

      $db = $this->getDbConnection();
      $language = VLanguageHelper::getLanguage($language);
      if ($db !== null && $language !== null) {
        $cmd = $db->createCommand()->
          select('t1.source AS source,t2.translate AS translate')->
          from($this->sourceMessageTable . ' t1')->
          leftJoin($this->translateMessageTable . ' t2', array(
            'AND',
            't2.module=t1.module',
            't2.category=t1.category',
            't2.hash=t1.hash',
            't2.languageID=:languageID'
            ), array(':languageID' => $language->id))->
          where(array(
          'AND',
          't1.module=:module',
          't1.category=:category',
          ), array(
          ':module' => $module,
          ':category' => $category,
        ));

        foreach ($cmd->queryAll() as $row) {
          $dbData[$row['source']] = $row['translate'];
        }

        if (count($dbData) && $this->cachingDuration > 0 && $cache !== null) {
          $cache->set(self::CACHE_KEY_PREFIX_DB . $key, $dbData, $this->cachingDuration);
        }
      }
    }

    return $this->dominantStorage == 'db' ? CMap::mergeArray($fileData, $dbData) : CMap::mergeArray($dbData, $fileData);
  }

  /**
   * Translates a message to the specified language.
   *
   * @param string $category message category
   * @param string $message source message
   * @param string $language target language, if null, set to current language
   * @return string translated message
   */
  public function translate($category, $message, $language = null)
  {
    if ($language === null) {
      $language = Yii::app()->getLanguage();
    }

    $module = '';

    if (strpos($category, '.') !== false) {
      $path = explode('.', $category);
      $category = array_pop($path);
      $module = implode('.', $path);
    }

    if ($this->forceTranslation || $language !== $this->getLanguage()) {
      return $this->translateMessage($module, $category, $message, $language);
    }

    return $message;
  }

  /**
   * Translates the specified message.
   * First, check if translation exists in current module, then check root messages dir
   *
   * @param string $module Module id
   * @param string $category Message category
   * @param string $message Source message
   * @param string $language Target language
   * @return string Translated message
   */
  protected function translateMessage($module, $category, $message, $language)
  {
    $modules = $this->getModules($module);
    $key = $category . '.' . $language;

    if (is_array($modules)) {
      foreach ($modules as $id) {
        if (!isset($this->_messages["$id.$key"])) {
          $this->_messages["$id.$key"] = $this->loadMessages($language, $category, $id);
        }

        if (isset($this->_messages["$id.$key"][$message]) && !empty($this->_messages["$id.$key"][$message])) {
          return $this->_messages["$id.$key"][$message];
        }
      }
    }

    if (!isset($this->_messages[$key])) {
      $this->_messages[$key] = $this->loadMessages($language, $category);
    }

    if (isset($this->_messages[$key][$message]) && !empty($this->_messages[$key][$message])) {
      return $this->_messages[$key][$message];
    }

    if ($this->hasEventHandler('onMissingTranslation')) {
      $event = new VMissingTranslationEvent($this, $module, $category, $message, $language);
      $this->onMissingTranslation($event);
    }

    return $message;
  }

  /**
   * @return string the language that the source messages are written in.
   * Defaults to {@link CApplication::language application language}.
   */
  public function getLanguage()
  {
    return $this->_language === null ? Yii::app()->sourceLanguage : $this->_language;
  }

  /**
   * @param string $language the language that the source messages are written in.
   */
  public function setLanguage($language)
  {
    $this->_language = CLocale::getCanonicalID($language);
  }

  /**
   * Returns module and all it parents as array values
   * @param string $module Module id
   * @return array
   */
  public function getModules($module)
  {
    $return = array();

    if (!empty($module)) {
      $modules = explode('.', $module);
      while ($modules !== array()) {
        $return[] = implode('.', $modules);
        array_pop($modules);
      }
    }

    return $return;
  }

  /**
   * Returns relative message folder path for module
   * @param string $module Module ID
   * @return string
   */
  public function getModuleMessagesPath($module)
  {
    return 'application.modules.' . strtr($module, array('.' => '.modules.')) . '.messages';
  }

  /**
   * Returns the DB connection used for the message source
   * @return mixed null if no connection specified (database source disabled)
   */
  public function getDbConnection()
  {
    if ($this->connectionID !== false && $this->_db === null) {
      $this->_db = Yii::app()->getComponent($this->connectionID);
      if (!$this->_db instanceof CDbConnection) {
        $this->_db = null;
      }
    }

    return $this->_db;
  }

  /**
   * Raised when a message cannot be translated.
   * Handlers may log this message or do some default handling.
   * The {@link CMissingTranslationEvent::message} property
   * will be returned by {@link translateMessage}.
   * @param CMissingTranslationEvent $event the event parameter
   */
  public function onMissingTranslation($event)
  {
    $this->raiseEvent('onMissingTranslation', $event);
  }
}
