<?php
/**
 * ViraCMS Public (frontend) Controller
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VPublicController extends VController
{
  public $layout = 'default';

  /**
   * @var mixed current rendering subject
   */
  protected $_subject;

  /**
   * @var string page title
   */
  protected $_title;

  /**
   * Default access rules
   * @return array
   */
  public function accessRules()
  {
    return array(
      array(
        'allow',
        'users' => array('*'),
      )
    );
  }

  /**
   * Before action event
   * @param CAction $action current action
   * @return boolean is application can continue?
   */
  protected function beforeAction($action)
  {
    if (parent::beforeAction($action)) {
      if (Yii::app()->maintenance) {
        if (!($this->id == 'error' && ($this->action->id == 'maintenance' || $this->action->id == 'error'))) {
          $this->forward('/error/maintenance');
        }
      }

      return true;
    }

    return false;
  }

  /**
   * Get current rendering subject
   * @return mixed
   */
  public function getSubject()
  {
    return $this->_subject;
  }

  /**
   * Set current rendering subject
   * @param mixed $subject
   */
  public function setSubject($subject)
  {
    if ($subject !== null && ($subject instanceof VPage || $subject instanceof VSystemPage)) {
      $this->_subject = $subject;
      $this->title = $subject->title;
    }
  }

  /**
   * Remove current rendering subject
   */
  public function unsetSubject()
  {
    $this->_subject = null;
  }

  /**
   * Get current page title
   * @return string
   */
  public function getTitle()
  {
    return $this->_title ? $this->_title : $this->getPageTitle();
  }

  /**
   * Set current page title
   * @param string $title page title
   * @param boolean $encode need to HTML encode?
   */
  public function setTitle($title, $encode = true)
  {
    $this->_title = $encode ? CHtml::encode($title) : $title;
    $this->setPageTitle($this->_title);
  }

  /**
   * Render specified view file
   * @param string $view view file name
   * @param array $data view data
   * @param boolean $return need to return rendered output?
   * @return mixed
   */
  public function render($view, $data = null, $return = false)
  {
    if ($this->beforeRender($view)) {
      if ($this->subject instanceof VPage || $this->subject instanceof VSystemPage) {
        $class = empty($this->subject->class) ? VPageRendererCollection::STATIC_RENDERER : $this->subject->class;
        $renderer = new $class($this->subject);
      }
      else {
        $renderer = new VStaticPageRenderer(null);
      }

      return $renderer->render($view, $data, $return);
    }
  }

  /**
   * Run forward action
   */
  public function forward($route, $exit = true, $params = array(), $model = null)
  {
    $this->unsetSubject();
    parent::forward($route, $exit, $params, $model);
  }

  /**
   * @inheritdoc
   */
  public function getIsHomePage()
  {
    return $this->subject instanceof VPage ? $this->subject->homepage : parent::getIsHomePage();
  }
}
