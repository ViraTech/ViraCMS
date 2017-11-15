<?php
/**
 * ViraCMS Static Page Localization Model
 *
 * @package vira.core.core
 * @subpackage vira.core.content
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 *
 * @property string $pageID page identifier
 * @property string $languageID language identifier
 * @property string $name name of the page
 */
class VPageL10n extends VActiveRecord
{
  /**
   * @param string $className
   * @return VPageL10n
   */
  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }

  public function tableName()
  {
    return '{{core_page_l10n}}';
  }

  public function primaryKey()
  {
    return array(
      'pageID',
      'languageID',
    );
  }

  public function behaviors()
  {
    return CMap::mergeArray(array(
        'SearchIndexerBehavior' => array(
          'class' => 'VSearchIndexerBehavior',
          'module' => 'Vira.Core.Page',
          'keyAttribute' => 'pageID',
        ),
        ), parent::behaviors());
  }

  public function relations()
  {
    return array(
      'page' => array(self::BELONGS_TO, 'VPage', 'pageID'),
      'contents' => array(
        self::HAS_MANY,
        'VPageBlock',
        array(
          'pageID' => 'pageID',
          'languageID' => 'languageID'
        ),
        'condition' => 'class=:class',
        'params' => array(
          ':class' => 'VStaticRenderer',
        ),
      ),
    );
  }

  public function rules()
  {
    return array(
      array('languageID', 'required'),
      array('pageID', 'length', 'is' => 36),
      array('pageID', 'required', 'on' => 'create,update'),
      array('name', 'length', 'max' => 255),
      array('languageID', 'length', 'max' => 2),
    );
  }

  public function attributeLabels()
  {
    return array(
      'pageID' => Yii::t('admin.content.labels', 'Page ID'),
      'languageID' => Yii::t('admin.content.labels', 'Language'),
      'name' => Yii::t('admin.content.labels', 'Page Name'),
    );
  }
}
