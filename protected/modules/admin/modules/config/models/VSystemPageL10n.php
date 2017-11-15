<?php
/**
 * ViraCMS System Page Localization & Content Model
 *
 * @package vira.core.core
 * @subpackage vira.core.content
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 *
 * @property string $systemPageID page identifier
 * @property string $languageID language identifier
 * @property string $name name of the page
 * @property string $title SEO title
 * @property string $keywords SEO keywords
 * @property string $description SEO description
 * @property string $content page contents
 */
class VSystemPageL10n extends VActiveRecord
{
  /**
   * @param string $className
   * @return VSystemPageL10n
   */
  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }

  public function tableName()
  {
    return '{{core_system_page_l10n}}';
  }

  public function primaryKey()
  {
    return array(
      'systemPageID',
      'languageID',
    );
  }

  public function relations()
  {
    return array(
      'page' => array(self::BELONGS_TO, 'VPage', 'pageID'),
    );
  }

  public function rules()
  {
    return array(
      array('languageID', 'required'),
      array('systemPageID', 'length', 'is' => 36),
      array('systemPageID', 'required', 'on' => 'create,update'),
      array('name', 'length', 'max' => 255),
      array('title', 'length', 'max' => 1024),
      array('keywords,description', 'length', 'max' => 65530),
      array('languageID', 'length', 'max' => 2),
      array('content', 'length', 'max' => 16777216),
    );
  }

  public function attributeLabels()
  {
    return array(
      'name' => Yii::t('admin.content.labels', 'Page Name'),
      'title' => Yii::t('admin.content.labels', 'SEO Title'),
      'keywords' => Yii::t('admin.content.labels', 'SEO Keywords'),
      'description' => Yii::t('admin.content.labels', 'SEO Description'),
    );
  }
}
