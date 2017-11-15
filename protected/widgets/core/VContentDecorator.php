<?php
/**
 * ViraCMS Content Decorator Widget
 * Based on Yii Framework CContentDecorator class file.
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VContentDecorator extends COutputProcessor
{
  /**
   * @var mixed the name of the view that will be used to decorate the captured content.
   */
  public $view;

  /**
   * @var array the variables (name=>value) to be extracted and made available in the decorative view.
   */
  public $data = array();

  /**
   * Processes the captured output.
   * This method decorates the output with the specified {@link view}.
   * @param string $output the captured output to be processed
   */
  public function processOutput($output)
  {
    $output = $this->decorate($output);
    parent::processOutput($output);
  }

  /**
   * Decorates the content by rendering a view and embedding the content in it.
   * @param string $content the content to be decorated
   * @return string the decorated content
   */
  protected function decorate($content)
  {
    $owner = $this->getOwner();
    $controller = Yii::app()->getController();

    if ($this->view === null) {
      $viewFile = $controller->getLayoutFile(null);
    }
    elseif (($offset = stripos($this->view, 'layouts/')) !== false && in_array($offset, range(0, 2))) {
      $viewFile = $controller->getLayoutFile(substr($this->view, strlen('layouts/') + $offset));
    }
    else {
      $viewFile = $owner->getViewFile($this->view);
    }

    if ($viewFile !== false) {
      $data = $this->data;
      $data['content'] = $content;
    }

    return $viewFile === false ? $content : $owner->renderFile($viewFile, $data, true);
  }
}
