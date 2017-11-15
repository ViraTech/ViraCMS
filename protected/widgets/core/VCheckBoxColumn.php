<?php
/**
 * ViraCMS Checkbox Column Component
 * @see CCheckBoxColumn for details
 *
 * @package vira.core.core
 * @subpackage vira.core.widgets
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
Yii::import('zii.widgets.grid.CGridColumn');
class VCheckboxColumn extends CGridColumn
{
  public $name;
  public $value;
  public $checked;
  public $disabled;
  public $hidden;
  public $htmlOptions = array('class' => 'checkbox-column');
  public $headerHtmlOptions = array('class' => 'checkbox-column');
  public $footerHtmlOptions = array('class' => 'checkbox-column');
  public $checkBoxHtmlOptions = array();
  public $selectableRows = null;
  public $headerTemplate = '{item}';

  public function init()
  {
    if (isset($this->checkBoxHtmlOptions['name'])) {
      $name = $this->checkBoxHtmlOptions['name'];
    }
    else {
      $name = $this->id;
      if (substr($name, -2) !== '[]') {
        $name .= '[]';
      }
      $this->checkBoxHtmlOptions['name'] = $name;
    }
    $name = strtr($name, array('[' => "\\[", ']' => "\\]"));

    if ($this->selectableRows === null) {
      if (isset($this->checkBoxHtmlOptions['class'])) {
        $this->checkBoxHtmlOptions['class'] .= ' select-on-check';
      }
      else {
        $this->checkBoxHtmlOptions['class'] = 'select-on-check';
      }
      return;
    }

    $cball = $cbcode = '';
    if ($this->selectableRows == 0) {
      $cbcode = "return false;";
    }
    elseif ($this->selectableRows == 1) {
      $cbcode = "jQuery(\"input:not(#\"+this.id+\")[name='$name']\").prop('checked',false);";
    }
    elseif (strpos($this->headerTemplate, '{item}') !== false) {
      $cball = <<<CBALL
jQuery(document).on('click','#{$this->id}_all',function() {
  var checked=this.checked;
  jQuery("input[name='$name']:enabled").each(function() {this.checked=checked;});
});

CBALL;
      $cbcode = "jQuery('#{$this->id}_all').prop('checked', jQuery(\"input[name='$name']\").length==jQuery(\"input[name='$name']:checked\").length);";
    }

    if ($cbcode !== '') {
      $js = $cball;
      $js .= <<<EOD
jQuery(document).on('click', "input[name='$name']", function() {
  $cbcode
});
EOD;
      Yii::app()->getClientScript()->registerScript(__CLASS__ . '#' . $this->id, $js);
    }
  }

  protected function renderHeaderCellContent()
  {
    if (empty($this->headerTemplate)) {
      echo $this->grid->blankDisplay;
      return;
    }

    $item = '';
    if ($this->selectableRows === null && $this->grid->selectableRows > 1) {
      $item = CHtml::checkBox($this->id . '_all', false, array('class' => 'select-on-check-all'));
    }
    elseif ($this->selectableRows > 1) {
      $item = CHtml::checkBox($this->id . '_all', false);
    }
    else {
      ob_start();
      parent::renderHeaderCellContent();
      $item = ob_get_clean();
    }

    echo strtr($this->headerTemplate, array(
      '{item}' => $item,
    ));
  }

  protected function renderDataCellContent($row, $data)
  {
    if ($this->hidden !== null) {
      $hidden = $this->evaluateExpression($this->hidden, array(
        'data' => $data,
        'row' => $row,
      ));
      if ($hidden) {
        return;
      }
    }

    if ($this->value !== null) {
      $value = $this->evaluateExpression($this->value, array(
        'data' => $data,
        'row' => $row,
      ));
    }
    elseif ($this->name !== null) {
      $value = CHtml::value($data, $this->name);
    }
    else {
      $value = $this->grid->dataProvider->keys[$row];
    }

    $checked = false;
    if ($this->checked !== null) {
      $checked = $this->evaluateExpression($this->checked, array(
        'data' => $data,
        'row' => $row,
      ));
    }

    $options = $this->checkBoxHtmlOptions;
    if ($this->disabled !== null) {
      $options['disabled'] = $this->evaluateExpression($this->disabled, array(
        'data' => $data,
        'row' => $row,
      ));
    }

    $name = $options['name'];
    unset($options['name']);
    $options['value'] = $value;
    $options['id'] = $this->id . '_' . $row;
    echo CHtml::checkBox($name, $checked, $options);
  }
}
