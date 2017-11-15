<?php
/**
 * ViraCMS Page' Content Block Model
 *
 * @package vira.core.core
 * @subpackage vira.core.content
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 *
 * @property string $id primary key
 * @property string $siteID site identifier
 * @property string $layoutID layout identifier (for common area)
 * @property string $pageAreaID area identifier
 * @property string $pageID page identifier
 * @property string $languageID language
 * @property string $class renderer class
 * @property mixed $content block contents
 */
class VPageBlock extends VActiveRecord
{
  /**
   * @param string $className
   * @return VPageBlock
   */
  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }

  public function tableName()
  {
    return '{{core_page_block}}';
  }

  public function relations()
  {
    return array(
      'page' => array(self::BELONGS_TO, 'VPage', 'pageID'),
    );
  }

  public function behaviors()
  {
    return CMap::mergeArray(
        array(
        'GuidBehavior' => array(
          'class' => 'VGuidBehavior',
        ),
        ), parent::behaviors()
    );
  }

  public function rules()
  {
    return array(
      array('id', 'unsafe', 'except' => 'create'),
      array('id,siteID,pageID,pageAreaID', 'length', 'is' => 36),
      array('class', 'required'),
      array('layoutID', 'length', 'max' => 64),
      array('languageID', 'length', 'max' => 2),
      array('class', 'length', 'max' => 255),
      array('content', 'safe'),
    );
  }

  protected function beforeSave()
  {
    if (parent::beforeSave()) {
      $content = array_map('trim', explode("\n", strtr($this->content, array(
        "\r\n" => "\n",
        "\t" => "",
      ))));
      $this->content = implode('', $content);

      return true;
    }

    return false;
  }
}
