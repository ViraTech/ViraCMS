<?php
/**
 * ViraCMS Model Localization Behavior
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VLocalizationBehavior extends CBehavior
{
  /**
   * @var string owner model relation name for localization models
   */
  public $relationName = 'l10n';

  /**
   * @var string (optional) provide relation class name if it can't be determined from owner model's relation
   */
  public $relationClassName;

  /**
   * @var string (optional) relation key attribute
   */
  public $keyAttribute;

  /**
   * @var string localization model language attribute name, defaults to "languageID"
   */
  public $languageAttribute = 'languageID';

  /**
   * @var array localization models cache
   */
  protected $_l10n = array();

  public function events()
  {
    return array(
      'onAfterDelete' => 'deleteL10nModels',
    );
  }

  /**
   * Return localization model
   * @param string $languageID language identifier
   * @param boolean $anyAvailable return any available model
   * @param string $scenario model scenario
   * @return mixed
   */
  public function getL10nModel($languageID = null, $anyAvailable = true, $scenario = 'update')
  {
    if ($languageID === null) {
      $languageID = Yii::app()->getLanguage();
    }

    if ($this->_l10n === array()) {
      $this->loadL10nModels();
    }

    if (!isset($this->_l10n[$languageID]) && $anyAvailable && count($this->_l10n)) {
      $l10n = current($this->_l10n);
      $l10n->setScenario($scenario);
      return $l10n;
    }

    if (!isset($this->_l10n[$languageID])) {
      $className = $this->getRelationClassName();
      $keyAttribute = $this->getKeyAttribute();
      $primaryKey = $this->owner->getPrimaryKey();
      $this->_l10n[$languageID] = new $className($scenario);
      $this->_l10n[$languageID]->setAttribute($this->languageAttribute, $languageID);
      $this->setKeyAttribute($this->_l10n[$languageID], $keyAttribute, $primaryKey);
    }

    return $this->_l10n[$languageID];
  }

  /**
   * Set localization model key attribute (include composite key)
   * @param mixed $model localization model
   * @param mixed $keyAttribute key attribute, or array of attributes
   * @param mixed $primaryKey parent model primary key (or array of keys)
   */
  protected function setKeyAttribute(&$model, $keyAttribute, $primaryKey)
  {
    if (is_array($keyAttribute)) {
      $attributes = array_combine($keyAttribute, $primaryKey);
      foreach ($attributes as $attribute => $value) {
        if ($model->hasAttribute($attribute)) {
          $model->setAttribute($attribute, $value);
        }
      }
    }
    else {
      $model->setAttribute($keyAttribute, $primaryKey);
    }
  }

  /**
   * Load localization models from owner model relation
   */
  protected function loadL10nModels()
  {
    $relation = $this->owner->hasRelation($this->relationName) ? $this->owner->getRelated($this->relationName) : array();

    foreach ($relation as $l10n) {
      $this->_l10n[$l10n->getAttribute($this->languageAttribute)] = $l10n;
    }
  }

  /**
   * Populate localization models with request data
   * @param CHttpRequest $request
   */
  public function populateL10nModels(CHttpRequest $request)
  {
    $data = $request->getParam($this->getRelationClassName());
    if (is_array($data)) {
      foreach ($data as $languageID => $attributes) {
        $this->populateL10nModel($languageID, $attributes);
      }
    }
    elseif (isset($data[$this->languageAttribute])) {
      $this->populateL10nModel($data[$this->languageAttribute], $data);
    }
  }

  /**
   * Populate localization model for specified language with provided attributes data
   * @param string $languageID
   * @param array $attributes
   */
  public function populateL10nModel($languageID, $attributes)
  {
    $model = $this->getL10nModel($languageID, false, $this->owner->getScenario());
    $model->setAttributes($attributes);
  }

  /**
   * Validate localization models
   * @return boolean status
   */
  public function validateL10nModels()
  {
    $success = true;

    foreach ($this->getL10nModels() as $l10n) {
      $success &= $l10n->validate();
    }

    return $success;
  }

  /**
   * Save localization models
   * @param boolean $validate perform validation procedure
   * @return boolean status
   */
  public function saveL10nModels($validate = true)
  {
    $success = true;

    foreach ($this->getL10nModels() as $l10n) {
      $success &= $l10n->save($validate);
    }

    return $success;
  }

  /**
   * Return all available localization models
   * @return type
   */
  public function getL10nModels()
  {
    if ($this->_l10n === array()) {
      $this->loadL10nModels();
    }

    return $this->_l10n;
  }

  /**
   * Return relation class name
   * @return string
   */
  public function getRelationClassName()
  {
    if ($this->relationClassName === null) {
      $this->acquireRelationParams();
    }

    return $this->relationClassName;
  }

  /**
   * Return relation key attribute
   * @return string
   */
  public function getKeyAttribute()
  {
    if ($this->keyAttribute === null) {
      $this->acquireRelationParams();
    }

    return $this->keyAttribute;
  }

  /**
   * Automatically acquire relation attributes from owner model
   */
  protected function acquireRelationParams()
  {
    $relations = $this->owner->relations();
    if (isset($relations[$this->relationName])) {
      $this->relationClassName = $relations[$this->relationName][1];
      $this->keyAttribute = $relations[$this->relationName][2];
    }
  }

  /**
   * Delete all localization models
   */
  public function deleteL10nModels()
  {
    $l10n = $this->getL10nModels();
    foreach ($l10n as $model) {
      if (!$model->isNewRecord) {
        $model->delete();
      }
    }
  }
}
