<?php
/**
 * ViraCMS Search Index Component
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VSearchIndex extends VApplicationComponent
{
  /**
   * @var array search index rules
   */
  public $rules;

  /**
   * Adds the content to the search index storage
   * @param string $module the module name
   * @param string $key the object key value
   * @return boolean
   */
  public function add($module, $key = null)
  {
    if (!($module && Yii::app()->hasComponent('searchStorage'))) {
      return false;
    }

    foreach ($this->findModels($module, $key) as $model) {
      if ($attributes = $this->getAttributes($module, $model)) {
        Yii::app()->searchStorage->update(
          $module, $attributes['key'], $attributes['siteID'], $attributes['languageID'], $attributes['url'], $attributes['title'], $attributes['text']
        );
      }
    }

    return true;
  }

  /**
   * Returns indexing rules
   * @param string $module the module name
   * @return mixed
   */
  private function getRules($module)
  {
    return isset($this->rules[$module]) ? $this->rules[$module] : false;
  }

  /**
   * Returns objects or false if no rules has been defined
   * @param string $module the module name
   * @param string $key the object key value
   * @return array
   * @todo think about HUGE set of models and it's impact on script's memory usage
   */
  private function findModels($module, $key)
  {
    if (($rules = $this->getRules($module)) != false) {
      $className = Yii::import($rules['class']);
      $model = call_user_func(array($className, 'model'));
      $criteria = new CDbCriteria();
      $criteria->compare($rules['key'], $key);

      return $model->findAll($criteria);
    }

    return array();
  }

  /**
   * Returns attributes for module object or false if no rules has been defined
   * @param string $module the module name
   * @param mixed $model the model object
   * @return mixed
   */
  private function getAttributes($module, $model)
  {
    if (($rules = $this->getRules($module)) != false) {
      $attributes = array(
        'key' => $this->retrieveAttributeValue($model, $rules['key']),
        'siteID' => '',
        'languageID' => '',
        'url' => '',
        'title' => '',
        'text' => '',
      );

      foreach ($rules['attributes'] as $name => $attribute) {
        if (is_array($attribute)) {
          $attributes[$name] = array();
          foreach ($attribute as $attr) {
            $attributes[$name][] = $this->retrieveAttributeValue($model, $attr);
          }
          $attributes[$name] = implode(' ', $attributes[$name]);
        }
        else {
          $attributes[$name] = $this->retrieveAttributeValue($model, $attribute);
        }
      }

      foreach ($rules['expressions'] as $name => $expression) {
        $attributes[$name] = $this->evaluateExpression($expression, array('model' => $model));
      }

      return $attributes;
    }

    return false;
  }

  /**
   * Retrieves the model attribute value
   * @param mixed $model the object model
   * @param string $attribute the attribute name
   * @return string
   */
  private function retrieveAttributeValue($model, $attribute)
  {
    $value = '';

    if (strpos($attribute, '.')) {
      $attribute = explode('.', $attribute);
      $name = $attribute[0];
      $attribute = implode('.', array_slice($attribute, 1));

      if ($model->hasRelation($name) && ($relation = $model->getRelated($name)) !== null) {
        if (is_array($relation)) {
          $value = array();
          foreach ($relation as $item) {
            $value[] = $this->retrieveAttributeValue($item, $attribute);
          }
          $value = implode(' ', $value);
        }
        else {
          $value = $this->retrieveAttributeValue($relation, $attribute);
        }
      }
    }
    elseif (isset($model[$attribute])) {
      $value = $model[$attribute];
    }

    return $value;
  }

  /**
   * Removes the content from the search index storage
   * @param string $module the module name
   * @param string $key the object key value
   * @return boolean
   */
  public function remove($module, $key)
  {
    if (Yii::app()->hasComponent('searchStorage')) {
      Yii::app()->searchStorage->delete($module, $key, true);
    }

    return true;
  }

  /**
   * Search over search index storage
   * @param string $siteID site identifier
   * @param string $languageID language identifier
   * @param string $query search query string
   */
  public function search($siteID, $languageID, $query)
  {
    $results = array();

    if (Yii::app()->hasComponent('searchStorage')) {
      if ($siteID == null) {
        $siteID = Yii::app()->site->id;
      }

      if ($languageID == null) {
        $languageID = Yii::app()->getLanguage();
      }

      $results = Yii::app()->searchStorage->search($siteID, $languageID, $query);
    }

    return $results;
  }

  /**
   * Totally cleans up search index
   */
  public function clear()
  {
    if (Yii::app()->hasComponent('searchStorage')) {
      Yii::app()->searchStorage->clear();
    }
  }

  /**
   * Totally rebuilds search index
   */
  public function rebuild()
  {
    if (!Yii::app()->hasComponent('searchStorage')) {
      return false;
    }

    foreach ($this->rules as $module => $rules) {
      $this->add($module);
    }

    return true;
  }
}
