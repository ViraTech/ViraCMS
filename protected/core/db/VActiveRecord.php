<?php
/**
 * ViraCMS ActiveRecord Pattern
 * Based on Yii Framework CActiveRecord Class
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VActiveRecord extends CActiveRecord
{
  public function init()
  {
    Yii::app()->eventManager->attach($this);
    parent::init();
  }

  /**
   * Returns the primary key of the associated database table.
   * @return mixed
   */
  public function primaryKey()
  {
    return 'id';
  }

  /**
   * Quotes column name in the right way
   * @param string $column column name
   * @return string quoted column name
   */
  public function quoteColumn($column)
  {
    return Yii::app()->db->quoteColumnName($column);
  }

  /**
   * Helper function check for named relation is exists
   * @param string $name relation name
   * @return boolean
   */
  public function hasRelation($name)
  {
    if (method_exists($this, 'relations')) {
      $relations = $this->relations();

      if (isset($relations[$name])) {
        return true;
      }
    }

    return false;
  }

  /**
   * Returns first error message if model has errors occurred
   * @return mixed error message
   */
  public function getFirstError()
  {
    $error = $this->getErrors();
    while (is_array($error)) {
      $error = current($error);
    }

    return $error;
  }

  /**
   * Generate and return grid identifier (used by administrative CRUD controller)
   * @return string
   */
  public function getGridID()
  {
    return 'grid-' . strtolower(get_class($this));
  }

  public function filterBySite($siteID)
  {
    if ($this->hasAttribute('siteID')) {
      $this->getDbCriteria()->mergeWith(array(
        'condition' => Yii::app()->db->quoteColumnName('t.siteID') . '=:siteID',
        'params' => array(
          ':siteID' => $siteID,
        ),
      ));
    }

    return $this;
  }

  public function addTimeRangeCondition($attribute, &$criteria, $prefix = 't')
  {
    if ($this->hasAttribute($attribute)) {
      $value = $this->getAttribute($attribute);
      $dbField = $prefix . '.' . $attribute;

      if (is_array($value)) {
        $time = array_filter(array(
          'start' => empty($value['start']) ? 0 : strtotime($value['start'] . ' 00:00:00'),
          'end' => empty($value['end']) ? 0 : strtotime($value['end'] . ' 23:59:59'),
        ));

        if (isset($time['start']) && isset($time['end'])) {
          $criteria->addBetweenCondition($dbField, min($time['start'], $time['end']), max($time['start'], $time['end']));
        }
        elseif (isset($time['start'])) {
          $criteria->compare($dbField, '>=' . $time['start']);
        }
        elseif (isset($time['end'])) {
          $criteria->compare($dbField, '<=' . $time['end']);
        }
      }
    }

    return $this;
  }

  public function addSiteCondition($attribute, &$criteria, $prefix = 't')
  {
    if ($this->hasAttribute($attribute)) {
      $conditions = new CDbCriteria();

      if ($this->hasRelation('site')) {
        $conditions->with[] = 'site';
      }

      $value = $this->getAttribute($attribute);

      if ($value == '*' && Yii::app()->user->getAttribute('siteAccess') == 1) {
        $conditions->condition = '(' . $prefix . '.' . $attribute . ' IS NULL OR ' . $prefix . '.' . $attribute . " = '')";
      }
      else {
        $criteria->compare($prefix . '.' . $attribute, $value);
      }

      if (Yii::app()->user->getAttribute('siteAccess') == 0) {
        $criteria->addInCondition('t.siteID', Yii::app()->user->getModel()->getSiteAccessList());
      }
      $criteria->mergeWith($conditions);
    }
  }

  public function getBaseAlias()
  {
    $reflection = new ReflectionClass($this);
    $alias = preg_replace('#(' . Yii::app()->basePath . DIRECTORY_SEPARATOR . '|\.php$)#', '', $reflection->getFileName());
    return 'application.' . str_replace(DIRECTORY_SEPARATOR, '.', $alias) . PHP_EOL;
  }
}
