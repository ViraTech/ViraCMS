<?php
/**
 * GUID validator class
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VGuidValidator extends CValidator
{
  /**
   * @var string the regular expression used to validate GUID values
   */
  public $pattern = '/[a-z0-9]{8}\-[a-z0-9]{4}\-[a-z0-9]{4}\-[a-z0-9]{4}\-[a-z0-9]{12}/i';

  /**
   * @var boolean the attibute value can be empty (default false)
   */
  public $allowEmpty = false;

  /**
   * Validates the attribute of the object.
   * If there is any error, the error message is added to the object.
   * @param CModel $object the object being validated
   * @param string $attribute the attribute being validated
   */
  protected function validateAttribute($object, $attribute)
  {
    $value = $object->$attribute;
    if ($this->allowEmpty && $this->isEmpty($value)) {
      return;
    }

    if (preg_match($this->pattern, $value)) {
      $object->$attribute = $value;
    }
    else {
      $message = $this->message !== null ? $this->message : $this->getDefaultMessage();
      $this->addError($object, $attribute, $message);
    }
  }

  /**
   * Returns the JavaScript needed for performing client-side validation.
   * @param CModel $object the data object being validated
   * @param string $attribute the name of the attribute to be validated.
   * @return string the client-side validation script.
   * @see CActiveForm::enableClientValidation
   */
  public function clientValidateAttribute($object, $attribute)
  {
    $message = $this->message !== null ? $this->message : $this->getDefaultMessage();

    $message = strtr($message, array(
      '{attribute}' => $object->getAttributeLabel($attribute),
    ));

    $js = "if(!value.match({$this->pattern})){messages.push(" . CJSON::encode($message) . ");}";

    if ($this->allowEmpty) {
      $js = "if(jQuery.trim(value)!=''){$js}";
    }

    return $js;
  }

  /**
   * Returns default error message
   * @return string
   */
  protected function getDefaultMessage()
  {
    return Yii::t('common', '{attribute} contains incorrect GUID value.');
  }
}
