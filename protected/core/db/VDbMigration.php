<?php
/**
 * ViraCMS Database Migrations
 * Based on Yii Framework CDbMigration Class
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VDbMigration extends CDbMigration
{
  /**
   * Create new database table
   * @param string $table name
   * @param array $columns columns
   * @param mixed $options table options
   */
  public function createTable($table, $columns, $options = null)
  {
    if ($options === null) {
      if (isset(Yii::app()->params['db'][Yii::app()->db->driverName])) {
        $params = Yii::app()->params['db'][Yii::app()->db->driverName];
        $options = array();
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
    }

    parent::createTable($table, $columns, $options);
  }

  /**
   * Upload default data into database tables
   * @param array $data the data in format 'tableName' => [ ..rows.. ]
   * @return boolean
   */
  public function upload(array $data)
  {
    foreach ($data as $table => $rows) {
      $this->insertMultiple($table, $rows);
    }

    return true;
  }
}
