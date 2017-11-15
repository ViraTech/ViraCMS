<?php
/**
 * ViraCMS Upload Translated Messages Form
 *
 * @package vira.core.core
 * @subpackage vira.core.translate
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class UploadTranslationForm extends CFormModel
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
   * @var CUploadedFile file
   */
  public $file;

  public function rules()
  {
    return array(
      array('languageID,file', 'required'),
      array('languageID', 'exist', 'className' => 'VLanguage', 'attributeName' => 'id', 'message' => Yii::t('admin.translate.errors', 'Unknown language specified.'), 'allowEmpty' => true),
      array('file', 'file'),
      array('encoding', 'in', 'range' => $this->getAvailableEncodings(), 'message' => Yii::t('admin.translate.errors', 'Unsupported encoding "{value}".')),
    );
  }

  public function attributeLabels()
  {
    return array(
      'languageID' => Yii::t('admin.translate.labels', 'Language Translated To'),
      'file' => Yii::t('admin.translate.labels', 'Select File For Upload'),
      'encoding' => Yii::t('admin.translate.labels', 'Select File Encoding'),
    );
  }

  protected function beforeValidate()
  {
    if (parent::beforeValidate()) {
      $this->file = CUploadedFile::getInstance($this, 'file');

      return true;
    }

    return false;
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
