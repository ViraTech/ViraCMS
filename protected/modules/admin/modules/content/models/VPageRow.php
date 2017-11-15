<?php
/**
 * ViraCMS Page' Content Row Model
 *
 * @package vira.core.core
 * @subpackage vira.core.content
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 *
 * @property string $siteID site identifier
 * @property string $pageID page identifier
 * @property string $layoutID layout identifier (for common areas)
 * @property string $languageID language identifier
 * @property string $pageAreaID page area identifier
 * @property integer $row row number
 * @property string $template template as raw html
 */
class VPageRow extends VActiveRecord
{
  /**
   * @param string $className
   * @return VPageRow
   */
  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }

  public function tableName()
  {
    return '{{core_page_row}}';
  }

  public function rules()
  {
    return array(
      array('siteID,pageID,languageID,pageAreaID,row,template', 'required'),
      array('siteID,pageID,pageAreaID', 'length', 'is' => 36),
      array('languageID', 'length', 'max' => 2),
      array('siteID,pageID,pageAreaID,row', 'numerical', 'integerOnly' => true),
      array('layoutID', 'length', 'max' => 64),
      array('template', 'length', 'max' => 65530),
    );
  }

  public function primaryKey()
  {
    return array(
      'siteID',
      'layoutID',
      'pageID',
      'languageID',
      'pageAreaID',
      'row',
    );
  }

  public function relations()
  {
    return array(
      'area' => array(self::BELONGS_TO, 'VPageArea', 'pageAreaID'),
      'blocks' => array(self::HAS_MANY, 'VPageBlock', array(
          'siteID' => 'siteID',
          'layoutID' => 'layoutID',
          'pageID' => 'pageID',
          'languageID' => 'languageID',
          'pageAreaID' => 'pageAreaID',
        )),
    );
  }

  protected function beforeSave()
  {
    if (parent::beforeSave()) {
      $template = array_map('trim', explode("\n", strtr($this->template, array(
        "\r\n" => "\n",
        "\t" => "",
      ))));
      $this->template = implode('', $template);

      return true;
    }

    return false;
  }
}
