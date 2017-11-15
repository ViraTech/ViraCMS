<?php
/**
 * ViraCMS Search Storage Component
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VSearchStorage extends VApplicationComponent
{
  const STATUS_CREATED = 1;
  const STATUS_UPDATED = 2;
  const STATUS_REMOVED = 3;
  const CHUNK_LENGTH = 65530;
  const MAX_TITLE_LENGTH = 255;
  const MIN_WORD_LENGTH = 2;
  const MAX_WORD_LENGTH = 63;

  /**
   * @var CDbConnection database connection component
   */
  public $db;

  /**
   * @var string index table name
   */
  public $indexTable = 'core_search_index';

  /**
   * @var string content table name
   */
  public $contentTable = 'core_search_index_content';
  private $_stemFactory;

  public function init()
  {
    parent::init();

    if ($this->db == null) {
      $this->db = Yii::app()->db;
    }
    else {
      $this->db = Yii::createComponent($this->db);
    }

    if (!$this->checkDb()) {
      $this->createDb();
    }
  }

  /**
   * Checks if index and content tables is exist in current database connection
   * @return boolean
   */
  private function checkDb()
  {
    return $this->db->schema->getTable($this->indexTable) != null && $this->db->schema->getTable($this->contentTable) != null;
  }

  /**
   * Creates index and content tables
   */
  private function createDb()
  {
    $options = array();

    if (isset(Yii::app()->params['db'][$this->db->driverName])) {
      $params = Yii::app()->params['db'][$this->db->driverName];
      if (isset($params['engine'])) {
        $options[] = 'ENGINE=' . $params['engine'];
      }
      if (isset($params['charset'])) {
        $options[] = 'DEFAULT CHARSET=' . $params['charset'];
      }
      if (isset($params['collate'])) {
        $options[] = 'COLLATE=' . $params['collate'];
      }
      $options = implode(' ', $options);
    }

    if ($this->db->schema->getTable($this->indexTable) == null) {
      $tableName = trim($this->indexTable, ' {}');
      $this->db->createCommand()->createTable($this->indexTable, array(
        'id' => 'varchar(36)',
        'siteID' => 'varchar(36)',
        'languageID' => 'varchar(2)',
        'module' => 'varchar(63)',
        'key' => 'varchar(36)',
        'url' => 'varchar(1022)',
        'title' => 'varchar(255)',
        'status' => 'int(1) default 0',
        'timeCreated' => 'int default 0',
        'timeUpdated' => 'int default 0',
        ), $options);
      $this->db->createCommand()->addPrimaryKey("pk_{$tableName}", $this->indexTable, 'id');
      $this->db->createCommand()->createIndex("idx_{$tableName}_site", $this->indexTable, 'siteID');
      $this->db->createCommand()->createIndex("idx_{$tableName}_lang", $this->indexTable, 'languageID');
      $this->db->createCommand()->createIndex("idx_{$tableName}_object", $this->indexTable, 'module,key');
      $this->db->createCommand()->createIndex("idx_{$tableName}_status", $this->indexTable, 'status');
    }

    if ($this->db->schema->getTable($this->contentTable) == null) {
      $tableName = trim($this->contentTable, ' {}');
      $this->db->createCommand()->createTable($this->contentTable, array(
        'indexID' => 'varchar(36)',
        'position' => 'int',
        'content' => 'text',
        ), $options);
      $this->db->createCommand()->addPrimaryKey("pk_{$tableName}", $this->contentTable, 'indexID,position');
    }
  }

  /**
   * Update search index storage
   * @param string $module the module name
   * @param string $key the key value
   * @param string $siteID the site identifier
   * @param string $languageID the language identifier
   * @param string $url the object external URL (relative)
   * @param string $title the object title
   * @param string $text the text to be indexed
   * @return boolean
   */
  public function update($module, $key, $siteID, $languageID, $url, $title, $text)
  {
    $index = array(
      'siteID' => $siteID,
      'languageID' => $languageID,
      'module' => $module,
      'key' => $key,
      'url' => $url,
      'title' => $this->prepareTitle($title),
    );

    $id = $this->db->createCommand()->
        select('id')->
        from($this->indexTable)->
        where(array(
          'AND',
          $this->db->quoteColumnName('siteID') . '=:site',
          $this->db->quoteColumnName('languageID') . '=:lang',
          $this->db->quoteColumnName('module') . '=:module',
          $this->db->quoteColumnName('key') . '=:key'
          ), array(
          ':site' => $index['siteID'],
          ':lang' => $index['languageID'],
          ':module' => $index['module'],
          ':key' => $index['key'],
        ))->queryScalar();

    if (!$id) {
      $missing = true;
    }
    else {
      $missing = false;
      $index['status'] = self::STATUS_UPDATED;
      $index['timeUpdated'] = time();

      $this->db->createCommand()->update($this->indexTable, $index, 'id=:id', array(':id' => $id));
    }

    if ($missing) {
      $index['id'] = Yii::app()->guid->random();
      $index['status'] = self::STATUS_CREATED;
      $index['timeCreated'] = time();
      $index['timeUpdated'] = 0;

      $this->db->createCommand()->insert($this->indexTable, $index);
      $id = $index['id'];
    }

    if ($id && $text) {
      $text = $this->prepareText($text);

      if (mb_strlen($text, Yii::app()->charset) > self::CHUNK_LENGTH) {
        $chunks = array();
        $chunk = array();
        $chunkLength = 0;

        foreach (explode(' ', $text) as $word) {
          $wordLength = mb_strlen($word, Yii::app()->charset);

          if ($chunkLength + $wordLength > self::CHUNK_LENGTH) {
            $chunks[] = implode(' ', $chunk);
            $chunk = array();
            $chunkLength = 0;
          }

          $chunk[] = $word;
          $chunkLength += $wordLength;
        }
        $chunks[] = implode(' ', $chunk);
      }
      else {
        $chunks = array($text);
      }

      $this->db->createCommand()->delete($this->contentTable, 'indexID=:id', array(':id' => $id));
      foreach ($chunks as $position => &$chunk) {
        $this->db->createCommand()->insert($this->contentTable, array(
          'indexID' => $id,
          'position' => $position,
          'content' => $chunk,
        ));
      }
    }

    return true;
  }

  /**
   * Delete content from search index storage
   * @param string $tableName the table name
   * @param string $keyName the table key name
   * @param string $keyValue the table key value
   * @param boolean $complete true for complete remove content or false for just mark content deleted
   */
  public function delete($module, $key, $complete = false)
  {
    $condition = implode(' AND ', array(
      Yii::app()->db->quoteColumnName('module') . '=:module',
      Yii::app()->db->quoteColumnName('key') . '=:key',
    ));

    $params = array(
      ':module' => $module,
      ':key' => $key,
    );

    if ($complete) {
      $indexes = $this->db->createCommand()->select('id')->from($this->indexTable)->where($condition, $params)->queryAll();
      foreach ($indexes as $index) {
        $this->db->createCommand()->delete($this->indexTable, 'id=:id', array(':id' => $index['id']));
        $this->db->createCommand()->delete($this->contentTable, 'indexID=:id', array(':id' => $index['id']));
      }
    }
    else {
      $this->db->createCommand()->update($this->indexTable, array('status' => self::STATUS_REMOVED), $condition, $params);
    }
  }

  /**
   * Search over index
   * @param string $siteID site identifier
   * @param string $languageID language identifier
   * @param string $query query string
   * @return array search results
   */
  public function search($siteID, $languageID, $query)
  {
    $db = Yii::app()->db;

    $words = $this->prepareWords($query);
    if (!count($words)) {
      return array();
    }
    $stemmed = $this->stem($languageID, $words);

    $generalCondition = array(
      'AND',
      't.siteID=:siteID',
      't.languageID=:languageID',
      't.status<>:statusRemoved',
    );
    $wordsTitleCondition = array('OR');
    $wordsContentCondition = array('OR');
    $stemmedTitleCondition = array('OR');
    $stemmedContentCondition = array('OR');

    $params = array(
      ':siteID' => $siteID,
      ':languageID' => $languageID,
      ':statusRemoved' => self::STATUS_REMOVED,
    );

    $count = 0;
    foreach ($words as $word) {
      $wordsTitleCondition[] = "t.title LIKE " . $db->quoteValue('%' . $word . '%');
      $wordsContentCondition[] = "c.content LIKE " . $db->quoteValue('%' . $word . '%');
      $count++;
    }

    $count = 0;
    foreach ($stemmed as $word) {
      $stemmedTitleCondition[] = "t.title LIKE " . $db->quoteValue('%' . $word . '%');
      $stemmedContentCondition[] = "c.content LIKE " . $db->quoteValue('%' . $word . '%');
      $count++;
    }

    $columns = 't.id,t.title,t.url,c.content';

    $query = $db->createCommand()->
      select($columns)->
      from($this->indexTable . ' t')->
      leftJoin($this->contentTable . ' c', 'c.indexID=t.id')->
      where($generalCondition, $params)->
      andWhere($wordsTitleCondition);

    $queryContent = $db->createCommand()->
      select($columns)->
      from($this->indexTable . ' t')->
      leftJoin($this->contentTable . ' c', 'c.indexID=t.id')->
      where($generalCondition, $params)->
      andWhere($wordsContentCondition);
    $query->union($queryContent->getText());

    $queryStemmedTitle = $db->createCommand()->
      select($columns)->
      from($this->indexTable . ' t')->
      leftJoin($this->contentTable . ' c', 'c.indexID=t.id')->
      where($generalCondition, $params)->
      andWhere($stemmedTitleCondition);
    $query->union($queryStemmedTitle->getText());

    $queryStemmedContent = $db->createCommand()->
      select($columns)->
      from($this->indexTable . ' t')->
      leftJoin($this->contentTable . ' c', 'c.indexID=t.id')->
      where($generalCondition, $params)->
      andWhere($stemmedContentCondition);
    $query->union($queryStemmedContent->getText());

    $rows = $query->queryAll();

    $results = array();

    foreach ($rows as $row) {
      if (isset($results[$row['id']])) {
        $results[$row['id']]['content'] .= ' ' . $row['content'];
      }
      else {
        $results[$row['id']] = $row;
      }
    }

    return array(
      'words' => $words,
      'stemmed' => $stemmed,
      'results' => $results,
    );
  }

  /**
   * Truncates index and content tables
   */
  public function clear()
  {
    $this->db->createCommand()->truncateTable($this->indexTable);
    $this->db->createCommand()->truncateTable($this->contentTable);
  }

  /**
   * Prepare title to include to search index storage
   * @param string $title origin title
   * @return string prepared title
   */
  protected function prepareTitle($title)
  {
    $prepared = $this->prepareText($title, false);

    if (mb_strlen($prepared, Yii::app()->charset) > self::MAX_TITLE_LENGTH) {
      $prepared = mb_strcut($prepared, 0, self::MAX_TITLE_LENGTH - 3, Yii::app()->charset) . '...';
    }

    return $prepared;
  }

  /**
   * Prepare text to include to search index storage
   * @param string $text origin text
   * @param boolean $toLowerCase convert to lower case
   * @return string prepared text
   */
  protected function prepareText($text, $toLowerCase = true)
  {
    $prepared = $text;

    if ($toLowerCase) {
      // convert everything to lowercase
      $prepared = mb_convert_case($prepared, MB_CASE_LOWER, Yii::app()->charset);
    }

    // decode all entities to regular symbols
    $prepared = html_entity_decode($prepared, ENT_COMPAT, Yii::app()->charset);

    // replace tabs and line endings with spaces
    $prepared = strtr($prepared, array(
      "\t" => ' ',
      "\r\n" => ' ',
      "\n" => ' ',
      '>' => '> ', // workaround: add space after all ">" chars to split content onto parts
    ));

    // exclude html tags
    $prepared = strip_tags($prepared);

    // compress spaces
    $prepared = preg_replace('/\s+/', ' ', $prepared);

    return $prepared;
  }

  /**
   * Break text onto words
   * @param string $text origin text
   * @return array words text broken to
   */
  protected function prepareWords($text)
  {
    $words = array();

    // clean up text
    $text = $this->prepareText(preg_replace('/\W/u', ' ', $text));

    foreach (explode(' ', $text) as $word) {
      $wordLength = mb_strlen($word, Yii::app()->charset);
      if ($wordLength > self::MIN_WORD_LENGTH && $wordLength < self::MAX_WORD_LENGTH) {
        $words[$word] = true;
      }
    }

    return array_keys($words);
  }

  /**
   * Stem provided words
   * @param string $languageID language identifier
   * @param array $words origin words
   * @return array stemmed words
   */
  protected function stem($languageID, $words)
  {
    $stemmed = array();

    if (($stemmer = $this->getStemmer($languageID)) !== false) {
      foreach ($words as $word) {
        $stemmed[] = $stemmer->stem($word);
      }
    }

    return !empty($stemmed) ? $stemmed : $words;
  }

  /**
   * Return stemmer object for selected language
   * @param string $languageID language identifier
   * @return mixed
   */
  protected function getStemmer($languageID)
  {
    if ($this->_stemFactory === null) {
      Yii::import('application.components.stemmers.VSearchStemmer', true);
      $this->_stemFactory = Yii::createComponent(array('class' => 'VStemFactory'));
    }

    return $this->_stemFactory ? $this->_stemFactory->getStemmer($languageID) : false;
  }
}
