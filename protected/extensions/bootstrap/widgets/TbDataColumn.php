<?php
/**
 * TbDataColumn class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2011-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package bootstrap.widgets
 */
Yii::import('zii.widgets.grid.CDataColumn');
/**
 * Bootstrap grid data column.
 */
class TbDataColumn extends CDataColumn
{
  /**
   * Renders the header cell content.
   * This method will render a link that can trigger the sorting if the column is sortable.
   */
  protected function renderHeaderCellContent()
  {
    if ($this->grid->enableSorting && $this->sortable && $this->name !== null) {
      $sort = $this->grid->dataProvider->getSort();
      $label = isset($this->header) ? $this->header : $sort->resolveLabel($this->name);

      if ($sort->resolveAttribute($this->name) !== false)
        $label .= '<span class="caret"></span>';

      echo $sort->link($this->name, $label, array('class' => 'sort-link'));
    }
    else {
      if ($this->name !== null && $this->header === null) {
        if ($this->grid->dataProvider instanceof CActiveDataProvider)
          echo CHtml::encode($this->grid->dataProvider->model->getAttributeLabel($this->name));
        else
          echo CHtml::encode($this->name);
      }
      else
        parent::renderHeaderCellContent();
    }
  }

  /**
   * Renders the filter cell.
   */
  public function renderFilterCell()
  {
    echo '<td>';
    $this->renderFilterCellContent();
    echo '</div></td>';
  }

  protected function renderFilterCellContent()
  {
    echo '<div class="filter-container';
    if (is_string($this->filter)) {
      echo '">';
      echo $this->filter;
    }
    elseif ($this->filter !== false && $this->grid->filter !== null && $this->name !== null && strpos($this->name, '.') === false) {
      if (is_array($this->filter)) {
        echo ' filter-selector">';
        echo CHtml::activeDropDownList($this->grid->filter, $this->name, $this->filter, array('id' => false, 'prompt' => ''));
      }
      elseif ($this->filter === null) {
        echo '">';
        echo CHtml::activeTextField($this->grid->filter, $this->name, array('id' => false));
      }
    }
    else {
      echo '">';
      parent::renderFilterCellContent();
    }
  }
}
