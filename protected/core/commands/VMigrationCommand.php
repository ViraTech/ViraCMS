<?php
/**
 * ViraCMS Database Migrations Component
 * Rewritten From Yii Framework MigrateCommand Class
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VMigrationCommand extends CConsoleCommand
{
  const BASE_MIGRATION = 'vira_core_core_000000_000000_base';
  const ASCENDING_ORDER = 'asc';
  const DESCENDING_ORDER = 'desc';

  /**
   * @var string base path for migrations
   */
  public $migrationPath = 'application.migrations';

  /**
   * @var array modules found in migrations base path
   */
  public $migrationModules = array();

  /**
   * @var string the name of the table for keeping applied migration information
   */
  public $migrationTable = '{{core_migration}}';

  /**
   * @var string the application component ID that specifies the database connection for
   * storing migration information. Defaults to 'db'.
   */
  public $connectionID = 'db';

  /**
   * @var string the default command action
   */
  public $defaultAction = 'up';

  /**
   * @var CDbConnection
   */
  private $_db;

  /**
   * @var array migrations cache
   */
  private $_migrationCache;

  /**
   * @var boolean suppress output
   */
  private $_quiet = false;

  /**
   * Runs before action
   * @return boolean
   */
  private function start()
  {
    $path = Yii::getPathOfAlias($this->migrationPath);

    if (!$path || !is_dir($path)) {
      $this->error("Error: The migration directory does not exist: {$this->migrationPath}" . PHP_EOL);
      return false;
    }

    $this->migrationPath = $path;

    $this->migrationModules = $this->getMigrationModules();

    $this->output(PHP_EOL . "ViraCMS Migration Tool (based on Yii Framework v" . Yii::getVersion() . " Migration Tool v1.0)" . PHP_EOL . PHP_EOL);

    return true;
  }

  /**
   * Runs after action. If command has ran in quiet mode then return non-integer value to prevent exit() in the end.
   * @param string $action the action name
   * @param array $params the parameters to be passed to the action method.
   * @param integer $exitCode the application exit code returned by the action method.
   * @return mixed
   */
  protected function afterAction($action, $params, $exitCode = 0)
  {
    if ($this->_quiet) {
      return '';
    }
    else {
      return parent::afterAction($action, $params, $exitCode);
    }
  }

  /**
   * Apply new migrations action
   * @param string $modules list of modules to apply migrations
   * @param boolean $quiet suppress output messages
   * @return int
   */
  public function actionUp($modules = '', $quiet = false)
  {
    $this->_quiet = $quiet;

    if (!$this->start()) {
      return false;
    }

    $modules = $modules ? explode(',', $modules) : null;

    $migrations = $this->getNewMigrations($modules);

    if (empty($migrations)) {
      $this->output("No new migration found. Your system is up-to-date." . PHP_EOL);
      return true;
    }

    $migrations = $this->sortMigrations($migrations, self::ASCENDING_ORDER);

    $total = count($migrations);

    $this->output("Total $total new " . ($total === 1 ? 'migration' : 'migrations') . " to be applied -" . PHP_EOL);
    $module = '';
    foreach ($migrations as $migration) {
      if ($module != $migration['module']) {
        $module = $migration['module'];
        $this->output(PHP_EOL . "  module '$module':" . PHP_EOL);
      }
      $this->output("    " . $migration['migration'] . PHP_EOL);
    }

    $this->output(PHP_EOL);

    $error = false;
    $cancel = array();
    foreach ($migrations as $migration) {
      if (!isset($cancel[$migration['module']]) && $this->migrateUp($migration['module'], $migration['migration']) === false) {
        $this->error(PHP_EOL . "Migration failed. All later migrations for module '{$migration['module']}' are canceled." . PHP_EOL);
        $cancel[$migration['module']] = true;
        $error = true;
      }
    }

    $this->output(PHP_EOL . "Migrated up ");
    $this->output($error ? "with errors." : "successfully.");
    $this->output(PHP_EOL);

    return true;
  }

  /**
   * Revert migrations action
   * @param string $module module name
   * @param string $version revert to provided version only
   * @param boolean $quiet suppress output messages
   * @return int
   */
  public function actionDown($module, $version = '', $quiet = false)
  {
    $this->_quiet = $quiet;

    if (!$this->start()) {
      return false;
    }

    $migrations = $this->getMigrationHistory($module, $version, -1);

    if ($migrations === array()) {
      $this->output("No migration has been found for module '$module'");
      if ($version) {
        $this->output("version $version");
      }
      $this->output("." . PHP_EOL);
      return 1;
    }

    $migrations = $this->sortMigrations($migrations, self::DESCENDING_ORDER);

    $error = false;
    $n = count($migrations);
    $this->output("Total $n " . ($n === 1 ? 'migration' : 'migrations') . " to be reverted for module '$module':" . PHP_EOL);
    foreach ($migrations as $migration => $time) {
      $this->output("    $migration" . PHP_EOL);
    }
    $this->output(PHP_EOL);

    foreach ($migrations as $migration => $time) {
      ob_start();
      $result = $this->migrateDown($module, $migration);
      $this->output(ob_get_clean());
      if ($result === false) {
        $this->error(PHP_EOL . "Migration failed. All later migrations for module '$module'");
        if ($version) {
          $this->error("version $version");
        }
        $this->error(' are canceled.' . PHP_EOL);
        $error = true;
        break;
      }
    }

    $this->output(PHP_EOL);
    $this->output("Migrated down ");
    $this->output($error ? "with errors." : "successfully.");
    $this->output(PHP_EOL);

    return true;
  }

  /**
   * Process applying new migrations
   * @param string $module module name
   * @param string $class migration class name
   * @return boolean false if operation has failed
   */
  private function migrateUp($module, $class)
  {
    if ($class === self::BASE_MIGRATION) {
      return;
    }

    $this->output("*** applying $class" . PHP_EOL);
    $start = microtime(true);

    $migration = $this->instantiateMigration($module, $class);
    if ($migration === null) {
      return false;
    }

    ob_start();
    $result = $migration->safeUp();
    $this->output(ob_get_clean());
    if ($result !== false) {
      if (($connection = $this->getDbConnection()) === false) {
        return false;
      }
      $connection->createCommand()->insert($this->migrationTable, array(
        'module' => $module,
        'class' => $class,
        'version' => $migration->version,
        'apply_time' => time(),
      ));
      $time = microtime(true) - $start;
      $this->output("*** applied $class (time: " . sprintf("%.3f", $time) . "s)" . PHP_EOL . PHP_EOL);

      return true;
    }
    else {
      $time = microtime(true) - $start;
      $this->output("*** failed to apply $class (time: " . sprintf("%.3f", $time) . "s)" . PHP_EOL . PHP_EOL);

      return false;
    }
  }

  /**
   * Process reverting migrations
   * @param string $module module name
   * @param string $class migration class name
   * @return boolean false if operation has failed
   */
  private function migrateDown($module, $class)
  {
    if ($class === self::BASE_MIGRATION) {
      return;
    }

    $this->output("*** reverting $class" . PHP_EOL);
    $start = microtime(true);
    $migration = $this->instantiateMigration($module, $class);

    if ($migration === null) {
      return false;
    }

    if ($migration->safeDown() !== false) {
      $db = $this->getDbConnection();
      $db->createCommand()->delete($this->migrationTable, $db->quoteColumnName('class') . '=:class', array(
        ':class' => $class,
      ));
      $time = microtime(true) - $start;
      $this->output("*** reverted $class (time: " . sprintf("%.3f", $time) . "s)" . PHP_EOL . PHP_EOL);
    }
    else {
      $time = microtime(true) - $start;
      $this->output("*** failed to revert $class (time: " . sprintf("%.3f", $time) . "s)" . PHP_EOL . PHP_EOL);
      return false;
    }
  }

  /**
   * Open migration class and return it as object
   * @param string $module module name
   * @param string $class migration class name
   * @return VDbMigration
   */
  private function instantiateMigration($module, $class)
  {
    $file = implode(DIRECTORY_SEPARATOR, array($this->migrationPath, $module, $class)) . '.php';
    if (file_exists($file)) {
      require_once($file);
      $migration = new $class;
      $migration->setDbConnection($this->getDbConnection());
      return $migration;
    }

    return null;
  }

  /**
   * Return current database connection
   * @return CDbConnection
   */
  private function getDbConnection()
  {
    if ($this->_db === null) {
      $this->_db = Yii::app()->getComponent($this->connectionID);
    }

    if (!($this->_db instanceof CDbConnection)) {
      $this->error("Error: CMigrationCommand.connectionID '{$this->connectionID}' is invalid. Please make sure it refers to the ID of a CDbConnection application component." . PHP_EOL);
      return false;
    }

    return $this->_db;
  }

  /**
   * Return list of modules that has migration directory
   * @return array
   */
  private function getMigrationModules()
  {
    $modules = array();

    $dh = opendir($this->migrationPath);
    while (($dir = readdir($dh)) !== false) {
      if ($dir === '.' || $dir === '..') {
        continue;
      }
      if (is_dir($this->migrationPath . DIRECTORY_SEPARATOR . $dir)) {
        $modules[] = $dir;
      }
    }
    closedir($dh);

    asort($modules);

    return $modules;
  }

  /**
   * Load migration history for provided module
   * @param string $module module name
   * @param string $version version
   * @param integer $limit max. number of history records
   * @return array
   */
  private function getMigrationHistory($module, $version, $limit)
  {
    if ($this->_migrationCache === null) {
      $db = $this->getDbConnection();
      if ($db->schema->getTable($this->migrationTable) === null) {
        $this->createMigrationHistoryTable();
      }

      $query = $db->createCommand()
        ->select('module,version,class,apply_time')
        ->from($this->migrationTable)
        ->order('class DESC')
        ->limit($limit)
        ->queryAll();
      foreach ($query as $row) {
        $this->_migrationCache[$row['module']][$row['version']][$row['class']] = $row['apply_time'];
      }
    }

    if ($version == null) {
      $return = array();
      if (isset($this->_migrationCache[$module])) {
        foreach ($this->_migrationCache[$module] as $version => $migrations) {
          $return = array_merge($migrations, $return);
        }
      }
      return $return;
    }
    else {
      return isset($this->_migrationCache[$module][$version]) ? $this->_migrationCache[$module][$version] : array();
    }
  }

  /**
   * Create migration history table
   */
  private function createMigrationHistoryTable()
  {
    $db = $this->getDbConnection();

    $this->output('Creating migration history table "' . $this->migrationTable . '"...');

    $db->createCommand()->createTable($this->migrationTable, array(
      'class' => 'string NOT NULL',
      'module' => 'varchar(128) NOT NULL',
      'version' => 'varchar(16) NOT NULL',
      'apply_time' => 'integer',
    ));
    $db->createCommand()->addPrimaryKey('pkMigration', $this->migrationTable, 'class');

    $db->createCommand()->insert($this->migrationTable, array(
      'module' => 'vira.core.core',
      'version' => '1.0.0',
      'class' => self::BASE_MIGRATION,
      'apply_time' => time(),
    ));

    $this->output("done." . PHP_EOL);
  }

  /**
   * Load new migrations
   * @param string $modules modules list
   * @return array
   */
  private function getNewMigrations($modules = null)
  {
    if ($modules === null) {
      $modules = $this->getMigrationModules();
    }
    elseif (!is_array($modules)) {
      $modules = array($modules);
    }

    $applied = array();
    $migrations = array();

    foreach ($modules as $module) {
      $prefix = strtr($module, array(
        '.' => '_',
        '-' => '_',
      ));
      foreach ($this->getMigrationHistory($module, null, -1) as $class => $time) {
        $applied[$module][$class] = true;
      }
      if (file_exists($this->migrationPath . DIRECTORY_SEPARATOR . $module)) {
        $dh = opendir($this->migrationPath . DIRECTORY_SEPARATOR . $module);
        while (($file = readdir($dh)) !== false) {
          if ($file === '.' || $file === '..' || !is_file(implode(DIRECTORY_SEPARATOR, array($this->migrationPath, $module, $file)))) {
            continue;
          }
          if (preg_match('/^((\w+_)+(\d{6}_\d{6})_.*?)\.php$/', $file, $matches) && !isset($applied[$module][$matches[1]])) {
            if (substr($matches[2], 0, -1) == $prefix) {
              $migrations[$module][] = $matches[1];
            }
          }
        }
        closedir($dh);
      }
    }

    return $migrations;
  }

  /**
   * Sort migrations by selected order
   * @param array $migrations migrations list
   * @param string $order sorting order
   * @return array sorted migrations list
   */
  private function sortMigrations($migrations, $order)
  {
    $sorted = array();
    switch ($order) {
      case self::DESCENDING_ORDER:
        $sorted = $migrations;
        arsort($sorted);
        break;

      case self::ASCENDING_ORDER:
        foreach ($migrations as $module => $contents) {
          foreach ($contents as $migration) {
            if (preg_match('/^(\w+_)+(\d{6}_\d{6})_.*?$/', $migration, $timestamp)) {
              $timestamp = sscanf($timestamp[2], '%2d%2d%2d_%2d%2d%2d');
              array_unshift($timestamp, '20%02d-%02d-%02d %02d:%02d:%02d');
              $timestamp = call_user_func_array('sprintf', $timestamp);
              $sorted[] = array(
                'module' => $module,
                'migration' => $migration,
                'timestamp' => strtotime($timestamp),
              );
            }
          }

          $timestamps = array();
          foreach ($sorted as $i => $data) {
            $timestamps[$i] = $data['timestamp'];
          }

          array_multisort($timestamps, SORT_ASC, $sorted);
        }
        break;
    }

    return $sorted;
  }

  /**
   * Output console message
   * @param string $message the message string
   */
  private function output($message)
  {
    if (!$this->_quiet) {
      echo $message;
    }
  }

  /**
   * Output error message
   * @param string $message the message string
   */
  private function error($message)
  {
    echo $message;
  }
}
