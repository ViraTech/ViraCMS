<?php
/**
 * ViraCMS Model SEO Options Model
 *
 * @package vira.core.core
 * @subpackage vira.core.content
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 *
 * @property string $id the primary key
 * @property string $className the item's class name
 * @property string $primaryKey the item's primary key
 * @property string $languageID the language identifier
 * @property string $title the SEO title
 * @property string $keywords the SEO key words
 * @property string $description the SEO description
 */
class VSeo extends VActiveRecord
{
  /**
   * @inheritdoc
   */
  public function behaviors()
  {
    return array(
      'GuidBehavior' => array(
        'class' => 'core.behaviors.VGuidBehavior',
        'type' => VGuidBehavior::GUID_RANDOM,
      ),
    );
  }

  /**
   * @param string $className
   * @return VSeo
   */
  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }

  /**
   * @inheritdoc
   */
  public function tableName()
  {
    return '{{core_seo}}';
  }

  /**
   * @inheritdoc
   */
  public function primaryKey()
  {
    return 'id';
  }

  /**
   * @inheritdoc
   */
  public function rules()
  {
    return array(
      array('title', 'length', 'max' => 1022),
      array('keywords', 'length', 'max' => 4094),
      array('description', 'length', 'max' => 65530),
    );
  }

  /**
   * @inheritdoc
   */
  public function attributeLabels()
  {
    return array(
      'title' => Yii::t('admin.content.labels', 'SEO Title'),
      'keywords' => Yii::t('admin.content.labels', 'SEO Keywords'),
      'description' => Yii::t('admin.content.labels', 'SEO Description'),
    );
  }
}
