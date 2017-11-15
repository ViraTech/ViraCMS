<?php
/**
 * ViraCMS Button Column Component
 * Stylized to Twitter Bootstrap 2
 *
 * @package vira.core.core
 * @subpackage vira.core.widgets
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VButtonColumn extends CGridColumn
{
  public $size;
  public $actions;
  public $filter;

  public function renderDataCellContent($row, $data)
  {
    $buttons = array();
    if (is_array($this->actions)) {
      foreach ($this->actions as $button) {
        if (isset($button['visible'])) {
          if (!($button['visible'] = $this->evaluateExpression($button['visible'], array('data' => $data, 'row' => $row)))) {
            continue;
          }
        }
        $button['url'] = $this->evaluateExpression($button['url'], array('data' => $data, 'row' => $row));
        if (isset($button['label'])) {
          $button['htmlOptions']['title'] = $button['label'];
          $button['label'] = '';
        }
        $buttons[] = $button;
      }
    }

    Yii::app()->Controller->widget('bootstrap.widgets.TbButtonGroup', array(
      'size' => $this->size,
      'buttons' => $buttons,
      'htmlOptions' => array(
        'class' => 'btn-admin',
      )
    ));
  }

  public function renderFilterCellContent()
  {
    echo $this->filter;
  }
}
