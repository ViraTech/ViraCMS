<?php
/**
 * ViraCMS Page Size Selection Widget
 *
 * @package vira.core.core
 * @subpackage vira.core.widgets
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VPageSizeWidget extends CWidget
{
  /**
   * @var array page sizes available for selection
   */
  public $sizes = array(10, 25, 50, 100, 250, 500);

  /**
   * @var string page variable name
   */
  public $pageVar = 'pageSize';

  /**
   * @var string rendering type, valid values is 'button' and 'select'
   */
  public $type = 'button';

  /**
   * @var integer selected page size value
   */
  public $value;

  /**
   * @var array container html options
   */
  public $htmlOptions = array();

  public function init()
  {
    parent::init();
    if (!in_array($this->type, array('button', 'select'))) {
      $this->type = 'button';
    }
    if ($this->type == 'button' && !isset($this->htmlOptions['class'])) {
      $this->htmlOptions['class'] = 'btn-group page-size-selector input-block-level';
    }
  }

  public function run()
  {
    $this->render($this->type);
  }

  public function getDropdownData()
  {
    $data = array();

    foreach ($this->sizes as $size) {
      $data[$size] = Yii::t('common', 'Page Size: {n}', array($size));
    }

    return $data;
  }
}
