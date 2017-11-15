<?php
/**
 * ViraCMS Download Source Messages Form
 *
 * @package vira.core.core
 * @subpackage vira.core.translate
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class DownloadSourceMessageForm extends CFormModel
{
  /**
   * @var string file encoding
   */
  public $encoding;

  /**
   * @return language identifier
   */
  public $languageID;

  /**
   * @var boolean generate file of phrases without translation only
   */
  public $withoutTranslation = false;

  public function rules()
  {
    return array(
      array('encoding', 'required'),
      array('withoutTranslation', 'boolean'),
      array('languageID', 'exist', 'className' => 'VLanguage', 'attributeName' => 'id', 'message' => Yii::t('admin.translate.errors', 'Unknown language specified.'), 'allowEmpty' => true),
      array('encoding', 'in', 'range' => $this->getAvailableEncodings(), 'message' => Yii::t('admin.translate.errors', 'Unsupported encoding "{value}".')),
    );
  }

  public function attributeLabels()
  {
    return array(
      'languageID' => Yii::t('admin.translate.labels', 'Choose Translation Language'),
      'encoding' => Yii::t('admin.translate.labels', 'Select File Encoding'),
      'withoutTranslation' => Yii::t('admin.translate.labels', 'Generate file of messages without translation only'),
    );
  }

  /**
   * List of available file encodings
   * @return array
   */
  public function getAvailableEncodings()
  {
    return array(
      'UTF-8',
      'ISO-8859-1',
      'CP1251',
      'CP437',
    );
  }
}
