<?php
/**
 * ViraCMS Model SEO Options Behavior
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VSeoBehavior extends CActiveRecordBehavior
{
  /**
   * @var array search engine optimization options models
   */
  private $_seo = array();

  public function afterSave($event)
  {
    $this->saveSeoModels(false);
  }

  public function afterDelete($event)
  {
    $this->deleteSeoModels();
  }

  /**
   * Populate SEO models with data provided from request
   * @param CHttpRequest $request the request component
   */
  public function populateSeoModels(CHttpRequest $request)
  {
    $data = $request->getParam(get_class($this->getModel()), array());

    if (is_array($data)) {
      foreach ($data as $languageID => $attributes) {
        $this->populateSeoModelInternal($languageID, $attributes);
      }
    }
  }

  /**
   * Populate SEO model with data provided from request
   * @param string $languageID the language identifier
   * @param CHttpRequest $request the request component
   */
  public function populateSeoModel($languageID, CHttpRequest $request)
  {
    $this->populateSeoModelInternal($languageID, $request->getParam(get_class($this->getModel()), array()));
  }

  /**
   * Populate SEO model for certain language with provided data
   * @param string $languageID the language identifier
   * @param array $attributes the data
   */
  public function populateSeoModelInternal($languageID, $attributes)
  {
    $model = $this->getSeoModel($languageID);
    $model->setAttributes($attributes);
  }

  /**
   * Returns SEO model for specified language identifier
   * @param string $languageID the language identifier
   * @return VSeo
   */
  public function getSeoModel($languageID)
  {
    if (!isset($this->_seo[$languageID])) {
      $model = $this->getModel()->find($this->getDbCriteria($languageID));

      if ($model == null) {
        $model = $this->createModel();
        $model->className = $this->getOwnerClassName();
        $model->primaryKey = $this->getOwnerPrimaryKey();
        $model->languageID = $languageID;
      }

      $this->_seo[$languageID] = $model;
    }

    return $this->_seo[$languageID];
  }

  /**
   * Returns all of SEO models
   * @return VSeo[]
   */
  public function getSeoModels()
  {
    return $this->_seo;
  }

  /**
   * Validate SEO models
   * @return boolean
   */
  public function validateSeoModels()
  {
    $validated = true;

    foreach ($this->_seo as $model) {
      $validated &= $model->validate();
    }

    return $validated;
  }

  /**
   * Saves SEO models
   * @param boolean $validate each model need to be validated
   * @return boolean
   */
  public function saveSeoModels($validate = true)
  {
    $saved = true;

    foreach ($this->_seo as $model) {
      $saved &= $model->save($validate);
    }

    return $saved;
  }

  /**
   * Delete SEO models
   */
  public function deleteSeoModels()
  {
    $models = $this->getModel()->findAll($this->getDbCriteria());

    if ($models) {
      foreach ($models as $model) {
        $model->delete();
      }
    }
  }

  /**
   * Returns SEO model object
   * @return VSeo
   */
  private function getModel()
  {
    return VSeo::model();
  }

  /**
   * Returns new SEO model object
   * @param string $scenario the scenario name
   * @return VSeo
   */
  private function createModel($scenario = 'create')
  {
    return new VSeo($scenario);
  }

  /**
   * Returns owner class name
   * @return string
   */
  private function getOwnerClassName()
  {
    return get_class($this->owner);
  }

  /**
   * Returns owner primary key
   * @return string
   */
  private function getOwnerPrimaryKey()
  {
    $pk = $this->owner->getPrimaryKey();

    return is_array($pk) ? implode(',', $pk) : $pk;
  }

  /**
   * Returns database criteria for current owner's models
   * @param string $languageID the language identifier (optional)
   * @return \CDbCriteria
   */
  private function getDbCriteria($languageID = null)
  {
    $criteria = new CDbCriteria();

    $criteria->compare('t.className', $this->getOwnerClassName());
    $criteria->compare('t.primaryKey', $this->getOwnerPrimaryKey());
    $criteria->compare('t.languageID', $languageID);

    return $criteria;
  }
}
