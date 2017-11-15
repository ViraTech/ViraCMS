<?php
/**
 * TbInputHorizontal class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2011-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package bootstrap.widgets.input
 */
Yii::import('bootstrap.widgets.input.TbInput');
/**
 * Bootstrap horizontal form input widget.
 * @since 0.9.8
 */
class TbInputHorizontal extends TbInput
{
  /**
   * Runs the widget.
   */
  public function run()
  {
    echo CHtml::openTag('div', array('class' => 'control-group ' . $this->getContainerCssClass()));
    parent::run();
    echo '</div>';
  }

  /**
   * Returns the label for this block.
   * @return string the label
   */
  protected function getLabel()
  {
    if (isset($this->labelOptions['class']))
      $this->labelOptions['class'] .= ' control-label';
    else
      $this->labelOptions['class'] = 'control-label';

    return parent::getLabel();
  }

  /**
   * Renders a checkbox.
   * @return string the rendered content
   */
  protected function checkBox()
  {
    $attribute = $this->attribute;
    echo '<div class="controls">';
    $this->labelOptions['class'] = (isset($this->labelOptions['class']) ? $this->labelOptions['class'] . ' ' : '') . 'checkbox';
    $this->labelOptions['for'] = $this->getAttributeId($attribute);
    echo CHtml::openTag('label', $this->labelOptions);
    echo $this->form->checkBox($this->model, $attribute, $this->htmlOptions) . PHP_EOL;
    echo $this->model->getAttributeLabel($attribute);
    echo $this->getError() . $this->getHint();
    echo '</label></div>';
  }

  /**
   * Renders a list of checkboxes.
   * @return string the rendered content
   */
  protected function checkBoxList()
  {
    echo $this->getLabel();
    echo '<div class="controls">';
    echo $this->form->checkBoxList($this->model, $this->attribute, $this->data, $this->htmlOptions);
    echo $this->getError() . $this->getHint();
    echo '</div>';
  }

  /**
   * Renders a list of inline checkboxes.
   * @return string the rendered content
   */
  protected function checkBoxListInline()
  {
    $this->htmlOptions['inline'] = true;
    $this->checkBoxList();
  }

  /**
   * Renders a drop down list (select).
   * @return string the rendered content
   */
  protected function dropDownList()
  {
    echo $this->getLabel();
    echo '<div class="controls">';
    echo $this->form->dropDownList($this->model, $this->attribute, $this->data, $this->htmlOptions);
    echo $this->getError() . $this->getHint();
    echo '</div>';
  }

  /**
   * Renders a file field.
   * @return string the rendered content
   */
  protected function fileField()
  {
    echo $this->getLabel();
    echo '<div class="controls">';
    echo $this->form->fileField($this->model, $this->attribute, $this->htmlOptions);
    echo $this->getError() . $this->getHint();
    echo '</div>';
  }

  /**
   * Renders a password field.
   * @return string the rendered content
   */
  protected function passwordField()
  {
    echo $this->getLabel();
    echo '<div class="controls">';
    echo $this->getPrepend();
    echo $this->form->passwordField($this->model, $this->attribute, $this->htmlOptions);
    echo $this->getAppend();
    echo $this->getError() . $this->getHint();
    echo '</div>';
  }

  /**
   * Renders a radio button.
   * @return string the rendered content
   */
  protected function radioButton()
  {
    $attribute = $this->attribute;
    echo '<div class="controls">';
    echo '<label class="radio" for="' . $this->getAttributeId($attribute) . '">';
    echo $this->form->radioButton($this->model, $attribute, $this->htmlOptions) . PHP_EOL;
    echo $this->model->getAttributeLabel($attribute);
    echo $this->getError() . $this->getHint();
    echo '</label></div>';
  }

  /**
   * Renders a list of radio buttons.
   * @return string the rendered content
   */
  protected function radioButtonList()
  {
    echo $this->getLabel();
    echo '<div class="controls">';
    echo $this->form->radioButtonList($this->model, $this->attribute, $this->data, $this->htmlOptions);
    echo $this->getError() . $this->getHint();
    echo '</div>';
  }

  /**
   * Renders a list of inline radio buttons.
   * @return string the rendered content
   */
  protected function radioButtonListInline()
  {
    $this->htmlOptions['inline'] = true;
    $this->radioButtonList();
  }

  /**
   * Renders a textarea.
   * @return string the rendered content
   */
  protected function textArea()
  {
    echo $this->getLabel();
    echo '<div class="controls">';
    echo $this->form->textArea($this->model, $this->attribute, $this->htmlOptions);
    echo $this->getError() . $this->getHint();
    echo '</div>';
  }

  /**
   * Renders a text field.
   * @return string the rendered content
   */
  protected function textField()
  {
    echo $this->getLabel();
    echo '<div class="controls">';
    echo $this->getPrepend();
    echo $this->form->textField($this->model, $this->attribute, $this->htmlOptions);
    echo $this->getAppend();
    echo $this->getError() . $this->getHint();
    echo '</div>';
  }

  /**
   * Renders a CAPTCHA.
   * @return string the rendered content
   */
  protected function captcha()
  {
    echo $this->getLabel();
    echo '<div class="controls"><div class="captcha">';
    echo $this->form->textField($this->model, $this->attribute, $this->htmlOptions);
    echo '<div class="widget">' . $this->widget('CCaptcha', $this->captchaOptions, true) . '</div>';
    echo $this->getError() . $this->getHint();
    echo '</div></div>';
  }

  /**
   * Renders a date picker field.
   * @return string the rendered content
   */
  protected function datepickerField()
  {
    $this->htmlOptions['data-datepicker'] = 'datepicker';
    if (!isset($this->htmlOptions['data-format'])) {
      $this->htmlOptions['data-format'] = Yii::app()->format->dateFormat;
    }
    $value = $this->model->{$this->attribute};
    $this->htmlOptions['rel'] = preg_match('/\d+\-\d+\-\d+/', $value) ? $value : date('Y-m-d', $value);
    $this->htmlOptions['data-source'] = CHtml::activeId($this->model, $this->attribute);

    echo $this->getLabel();
    echo '<div class="controls">';
    echo $this->getPrepend();
    echo CHtml::activeHiddenField($this->model, $this->attribute);
    echo CHtml::textField('', $value && $value != '0000-00-00' ? Yii::app()->format->formatDate($value) : '', $this->htmlOptions);
    echo $this->getAppend();
    echo $this->getError() . $this->getHint();
    echo '</div>';
    Yii::app()->bootstrap->registerDatepickerScript();
  }

  /**
   * Renders an uneditable field.
   * @return string the rendered content
   */
  protected function uneditableField()
  {
    $value = isset($this->htmlOptions['value']) ? $this->htmlOptions['value'] : $this->model->getAttribute($this->attribute);
    if (isset($this->htmlOptions['value'])) {
      unset($this->htmlOptions['value']);
    }
    if (isset($this->htmlOptions['type'])) {
      $value = Yii::app()->format->format($value, $this->htmlOptions['type']);
      unset($this->htmlOptions['type']);
    }
    echo $this->getLabel();
    echo '<div class="controls">';
    echo CHtml::tag('span', $this->htmlOptions, $value);
    echo $this->getError() . $this->getHint();
    echo '</div>';
  }
}
