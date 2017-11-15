<?php
/**
 * ViraCMS Mail Templates Localization Model
 *
 * @package vira.core.core
 * @subpackage vira.core.content
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 *
 * @property string $templateID mail template identifier
 * @property string $languageID language identifier
 * @property boolean $isHtml is body in HTML?
 * @property string $subject letter subject
 * @property string $body letter body
 */
class VMailTemplateL10n extends VActiveRecord
{
  /**
   * @return VMailTemplateL10n
   */
  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }

  public function tableName()
  {
    return '{{core_mail_template_l10n}}';
  }

  public function rules()
  {
    return array(
      array('templateID', 'length', 'is' => 36),
      array('languageID', 'length', 'max' => 2),
      array('subject', 'length', 'max' => 1022),
      array('body', 'length', 'max' => 65530),
      array('isHtml', 'boolean'),
    );
  }

  public function primaryKey()
  {
    return array(
      'templateID',
      'languageID',
    );
  }

  public function attributeLabels()
  {
    return array(
      'subject' => Yii::t('admin.content.labels', 'Letter Subject'),
      'body' => Yii::t('admin.content.labels', 'Letter Body'),
      'isHtml' => Yii::t('admin.content.labels', 'HTML Content'),
    );
  }
}
