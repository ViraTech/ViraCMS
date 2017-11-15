<?php
/**
 * ViraCMS Static Page Renderer Class For ViraEditor Component
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class ViraEditorRenderer extends VApplicationComponent
{
  const DEFAULT_LAYOUT_NAME = 'default';
  const MODE_EDIT_CONTENT = 1;
  const MODE_EDIT_LAYOUT = 2;
  const MODE_EDIT_SYSTEM = 3;

  /**
   * @var integer the editor's mode
   */
  protected $_mode;

  /**
   * @var VPage page model
   */
  protected $_page;

  /**
   * @var mixed layout model or layout name
   */
  protected $_layout;

  /**
   * @var VSystemPage system page model
   */
  protected $_system;

  /**
   * @var array the content grid
   */
  protected $_content = array();

  /**
   * @var string current site identifier
   */
  protected $_siteID;

  /**
   * @var string current language code
   */
  protected $_languageID;

  /**
   * Constructor
   *
   * @param mixed $subject VPage for static page, VSiteLayout for layout, or empty for module view
   * @param mixed $siteID (optional) site identifier
   * @param mixed $languageID (optional) language identifier
   */
  public function __construct($subject = null, $siteID = null, $languageID = null)
  {
    $r = Yii::app()->request;

    $this->_siteID = $r->getParam('siteID', $siteID ? $siteID : Yii::app()->site->id);
    $this->_languageID = $r->getParam('lng', $languageID ? $languageID : Yii::app()->getLanguage());

    if ($r->getParam('edit', '') == 'content') {
      $this->_mode = self::MODE_EDIT_CONTENT;
    }

    if ($r->getParam('edit', '') == 'layout') {
      $this->_mode = self::MODE_EDIT_LAYOUT;
      $this->_layout = $r->getParam('id', self::DEFAULT_LAYOUT_NAME);
    }

    if ($r->getParam('edit', '') == 'system') {
      $this->_mode = self::MODE_EDIT_SYSTEM;
      $this->_layout = $r->getParam('id', self::DEFAULT_LAYOUT_NAME);
    }

    if ($subject instanceof VPage) {
      $this->_page = $subject;
      $this->_layout = $subject->layoutID;
    }

    if ($subject instanceof VSystemPage) {
      $this->_system = $subject;
      $this->_layout = $subject->layoutID;
    }

    if ($subject instanceof VSiteLayout) {
      $this->_layout = $subject;
    }

    Yii::app()->setLanguage($this->_languageID);
    Yii::app()->getController()->setPageTitle($subject->title);

    $this->init();
  }

  /**
   * Renderer initialization
   */
  public function init()
  {
    parent::init();

    if ($this->_system && $this->_layout == null) {
      $this->_layout = $this->_system->layoutID;
    }

    if ($this->_page && $this->_layout == null) {
      $this->_layout = $this->_page->layoutID;
    }

    $this->prerender();

    $this->renderDynamicContent();
  }

  /**
   * Render specified view
   * @param string $view view name
   * @param mixed $data view data
   * @param boolean $return return rendered content
   * @return mixed rendered content if $return set to true
   */
  public function render($view = 'index', $data = null, $return = false)
  {
    switch ($this->_mode) {
      case self::MODE_EDIT_CONTENT:
        $model = $this->_page;
        break;

      case self::MODE_EDIT_LAYOUT:
        $model = $this->_layout;
        break;

      case self::MODE_EDIT_SYSTEM:
        $model = $this->_system;
        break;
    }

    Yii::app()->editor->embed($model);

    $output = $this->getOutput();

    $controller = Yii::app()->getController();

    if (($layoutFile = $controller->getLayoutFile($controller->layout)) === false) {
      $layoutFile = $controller->getLayoutFile(self::DEFAULT_LAYOUT_NAME);
    }

    if ($layoutFile !== false) {
      $output = $controller->renderFile($layoutFile, array('content' => $output), true);
    }

    $controller->afterRender($view, $output);

    $output = $controller->processOutput($output);

    if ($return) {
      return $output;
    }
    else {
      echo $output;
    }
  }

  /**
   * Prerenderer
   * Creates page content grid
   */
  protected function prerender()
  {
    $this->_content = array();

    if (!$this->_layout instanceof VSiteLayout) {
      if ($this->_layout) {
        $this->_layout = VSiteLayout::model()->with(array('backgroundImage'))->findByPk(array(
          'id'     => $this->_layout,
          'siteID' => $this->_siteID,
        ));
      }

      if ($this->_layout == null) {
        $this->_layout = VSiteLayout::model()->with(array('backgroundImage'))->findByAttributes(
          array('siteID' => $this->_siteID), array('order' => 't.default DESC'));
      }

      if ($this->_layout == null) {
        return array();
      }
    }

    foreach (VLayoutArea::model()->findArea($this->_siteID, $this->_layout->id) as $layoutArea) {
      $this->_content[ 'area' ][ $layoutArea->pageAreaID ] = $this->getAreaAttributes($layoutArea->pageArea);

      if (($this->_page || $this->_system) && $layoutArea->pageArea->type == VPageAreaTypeCollection::PRIMARY) {
        $this->_content[ 'primary' ] = $this->getAreaAttributes($layoutArea->pageArea, true);
      }
    }

    $criteria = new CDbCriteria();
    $criteria->compare('t.layoutID', $this->_layout->id);
    if ($this->_page) {
      $criteria->compare('t.pageID', $this->_page->id, false, 'OR');
    }
    $criteria->with = array(
      'area',
      'blocks',
    );
    $criteria->compare('t.siteID', $this->_siteID);
    $criteria->compare('t.languageID', $this->_languageID);
    $criteria->order = Yii::app()->db->quoteColumnName('area.position') . ' ASC,' .
      Yii::app()->db->quoteColumnName('t.row') . ' ASC';

    foreach (VPageRow::model()->findAll($criteria) as $row) {
      $this->prerenderArea($row);
    }

    $this->_content[ 'layout' ] = $this->_layout->attributes;

    $body = '';

    if ($this->_layout->backgroundImage) {
      $body .= "background-image: url('" . Yii::app()->storage->getFileUrl($this->_layout->backgroundImage->path) . "');";
    }

    if ($this->_content[ 'layout' ][ 'bodyBackgroundColor' ]) {
      $body .= 'background-color:' . $this->_content[ 'layout' ][ 'bodyBackgroundColor' ] . ';';
    }

    if ($body) {
      $body = 'body{' . $body . '}';
    }

    $links = '';

    if ($this->_content[ 'layout' ][ 'linkColor' ]) {
      $links .= 'a{color:' . $this->_content[ 'layout' ][ 'linkColor' ] . '}';
    }

    if ($this->_content[ 'layout' ][ 'linkHoverColor' ]) {
      $links .= 'a:hover,a:active,a:focus{color:' . $this->_content[ 'layout' ][ 'linkHoverColor' ] . '}';
    }

    if ($this->_content[ 'layout' ][ 'linkVisitedColor' ]) {
      $links .= 'a:visited{color:' . $this->_content[ 'layout' ][ 'linkVisitedColor' ] . '}';
    }

    $this->_content[ 'styles' ] = implode("\n", array_filter(array(
      $body,
      $links,
      $this->_content[ 'layout' ][ 'styleOverride' ],
    )));
  }

  protected function getAreaAttributes($area, $primary = false)
  {
    $attributes = $area->attributes;

    $attributes[ 'title' ] = $area->title;
    $attributes[ 'primary' ] = $primary;
    $attributes[ 'dynamic' ] = false;
    $attributes[ 'content' ] = array();

    if ($primary && $this->_system) {
      $attributes[ 'content' ] = strtr(
        $this->_system->getContent($this->_languageID), $this->_system->getParams()
      );
    }

    return $attributes;
  }

  protected function prerenderArea($row)
  {
    if (isset($this->_content[ 'area' ][ $row->pageAreaID ])) {
      if ($row[ 'pageID' ] && $this->_content[ 'area' ][ $row->pageAreaID ][ 'type' ] == VPageAreaTypeCollection::PRIMARY) {
        $grid = $this->renderContentGrid($row->blocks);
        $grid[ 'template' ] = $row->template;
        $this->_content[ 'primary' ][ 'content' ][] = $grid;
        $this->_content[ 'primary' ][ 'dynamic' ] |= $grid[ 'dynamic' ];
      }
      else {
        $grid = $this->renderContentGrid($row->blocks);
        $grid[ 'template' ] = $row->template;
        $this->_content[ 'area' ][ $row->pageAreaID ][ 'content' ][] = $grid;
        $this->_content[ 'area' ][ $row->pageAreaID ][ 'dynamic' ] |= $grid[ 'dynamic' ];
      }
    }
  }

  /**
   * Render widgets for cached content
   */
  protected function renderDynamicContent()
  {
    if (empty($this->_content[ 'area' ])) {
      $this->_content[ 'area' ] = array();
      return;
    }

    foreach ($this->_content[ 'area' ] as &$area) {
      if ($area[ 'dynamic' ]) {
        if (is_array($area[ 'content' ])) {
          foreach ($area[ 'content' ] as &$row) {
            if ($row[ 'dynamic' ] && isset($row[ 'grid' ]) && is_array($row[ 'grid' ])) {
              foreach ($row[ 'grid' ] as &$block) {
                if ($block[ 'dynamic' ]) {
                  $block[ 'rendered' ] = $this->renderContentBlock($block, true);
                }
              }
            }
          }
        }
      }
    }
  }

  /**
   * Generate and return page output
   * @return string
   */
  protected function getOutput()
  {
    $output = '';

    if (isset($this->_content) && isset($this->_content[ 'area' ])) {
      foreach ($this->_content[ 'area' ] as $area) {
        $output .= $this->getAreaOutput($area);
      }
    }

    return $output;
  }

  protected function getAreaHtmlOptions($area)
  {
    $htmlOptions = array();

    $htmlOptions[ 'data-area-title' ] = Yii::t('admin.content.titles', '{type} area «{title}»', array(
        '{type}'  => Yii::app()->collection->pageAreaType->itemAt($area[ 'type' ]),
        '{title}' => $area[ 'title' ],
    ));

    if ($this->checkEdit($area)) {
      $htmlOptions[ 'data-area' ] = $area[ 'id' ];
      $htmlOptions[ 'data-container-class' ] = $area[ 'container' ];
      $htmlOptions[ 'data-area-type' ] = $area[ 'type' ];
    }
    else {
      $htmlOptions[ 'data-uneditable-area' ] = $area[ 'id' ];
    }

    if ($area[ 'classes' ]) {
      $htmlOptions[ 'class' ] = (isset($htmlOptions[ 'class' ]) ? $htmlOptions[ 'class' ] . ' ' : '') . $area[ 'classes' ];
    }

    return $htmlOptions;
  }

  /**
   * Render the area html output
   * @param array $area the area configuration
   * @return string
   */
  protected function getAreaOutput($area)
  {
    $htmlOptions = $this->getAreaHtmlOptions($area);
    $content = $this->renderAreaOutput($area);

    $content = $this->formatContent($content, $area);

    if ($area[ 'type' ] == VPageAreaTypeCollection::PRIMARY && !$area[ 'primary' ] && isset($this->_content[ 'primary' ])) {
      $content = strtr($content, array(
        '###VIRA_CONTENT_STUB###' => $this->getAreaOutput($this->_content[ 'primary' ]),
      ));
    }

    if ($area[ 'primary' ]) {
      $area[ 'container' ] = 'container-primary';
    }

    $content = CHtml::tag('div', array('class' => $area[ 'container' ]), $content);

    return CHtml::tag($area[ 'tag' ], $htmlOptions, $content);
  }

  /**
   * Page area renderer
   * @param mixed $area page area content grid or plain area content
   * @return string
   */
  protected function renderAreaOutput($area)
  {
    $output = '';

    if (is_array($area[ 'content' ])) {
      foreach ($area[ 'content' ] as $i => $row) {
        $replace = array();

        foreach ($row[ 'grid' ] as $block) {
          $htmlOptions = array();

          if ($block[ 'dynamic' ]) {
            $htmlOptions[ 'data-widget' ] = $block[ 'id' ];
            $params = @unserialize($block[ 'content' ]);
            if (is_array($params) && isset($params[ 'class' ])) {
              $htmlOptions[ 'data-widget-id' ] = $params[ 'class' ];
              $htmlOptions[ 'data-widget-config' ] = isset($params[ 'params' ]) && is_array($params[ 'params' ]) ? CJSON::encode($params[ 'params' ]) : '';
            }
          }
          else {
            $htmlOptions[ 'data-block' ] = $block[ 'id' ];
          }

          $block[ 'rendered' ] = CHtml::tag('div', $htmlOptions, $block[ 'rendered' ]);

          $replace[ '###VIRA#' . $block[ 'id' ] . '###' ] = $block[ 'rendered' ];
        }

        $template = strtr(CHtml::tag('div', array('data-row' => $i), $row[ 'template' ]), $replace);

        $output .= preg_replace('/###VIRA#\w{8}-\w{4}-\w{4}-\w{4}-\w{12}###/', '', $template);
      }
    }
    else {
      $output = $area[ 'content' ];
    }

    return $output;
  }

  protected function renderContentGrid($blocks)
  {
    $result = array(
      'dynamic' => false,
      'grid'    => array(),
    );

    if (is_array($blocks) && count($blocks)) {
      foreach ($blocks as $block) {
        $rendered = $this->renderContentBlock($block);
        $result[ 'grid' ][] = $rendered;
        $result[ 'dynamic' ] |= $rendered[ 'dynamic' ];
      }
    }

    return $result;
  }

  /**
   * Content block renderer
   * @param VPageBlock $data block class
   * @param boolean $plain if set to false then return rendered content with it's own metadata
   * @return mixed rendered block
   */
  protected function renderContentBlock($data, $plain = false)
  {
    if ($data instanceof VPageBlock) {
      $result = array(
        'id'       => $data->id,
        'class'    => $data->class,
        'content'  => $data->content,
        'rendered' => '',
        'dynamic'  => false,
      );
    }
    else {
      $result = $data;
    }

    if (isset($result[ 'class' ]) && isset($result[ 'content' ]) && class_exists($result[ 'class' ])) {
      $renderer = new $result[ 'class' ]($result[ 'content' ]);
      if (method_exists($renderer, 'disableScripts')) {
        $renderer->disableScripts();
      }
      if (method_exists($renderer, 'disableCache')) {
        $renderer->disableCache();
      }
      $result[ 'dynamic' ] = $renderer->isDynamic;
      $result[ 'rendered' ] = $renderer->render(Yii::app()->getController());
    }

    if ($plain) {
      $result = isset($result[ 'rendered' ]) ? $result[ 'rendered' ] : '';
    }
    elseif (!$result[ 'dynamic' ]) {
      unset($result[ 'class' ]);
      unset($result[ 'content' ]);
    }

    return $result;
  }

  /**
   * Return true if page area can be edited in this mode
   * @param array $area area
   * @return boolean
   */
  protected function checkEdit($area)
  {
    $allowed = array();

    if ($this->_mode == self::MODE_EDIT_LAYOUT) {
      $allowed = array(
        VPageAreaTypeCollection::PRIMARY,
        VPageAreaTypeCollection::COMMON,
      );
    }

    if ($this->_mode == self::MODE_EDIT_CONTENT) {
      $allowed = array(
        $area[ 'primary' ] ? VPageAreaTypeCollection::PRIMARY : null,
        VPageAreaTypeCollection::EXTRA,
      );
    }

    if ($this->_mode == self::MODE_EDIT_SYSTEM) {
      $allowed = array(
        VPageAreaTypeCollection::PRIMARY,
      );
    }

    return in_array($area[ 'type' ], $allowed);
  }

  protected function formatContent($content, $area)
  {
    if ($this->_mode == self::MODE_EDIT_LAYOUT) {
      if (empty($content)) {
        $content = $this->formatEmptyRow(
          $area[ 'type' ] == VPageAreaTypeCollection::PRIMARY ?
          '###VIRA_CONTENT_STUB###' : ''
        );
      }
      $content = strtr($content, array(
        '###VIRA_CONTENT_STUB###' => $this->formatContentStub(),
      ));
    }
    elseif ($this->_mode == self::MODE_EDIT_SYSTEM) {
      $systemContent = $area[ 'type' ] == VPageAreaTypeCollection::PRIMARY ?
        CHtml::tag('div', array('data-block' => 'system'), $content) : '';
      if (empty($content)) {
        $content = $this->formatEmptyRow($systemContent);
      }
      else {
        $content = strtr($content, array(
          '###VIRA_CONTENT_STUB###' => $systemContent,
        ));
      }
    }
    elseif ($this->_mode == self::MODE_EDIT_CONTENT) {
      if (empty($content)) {
        $content = $this->formatEmptyRow();
      }
    }

    return $content;
  }

  protected function formatEmptyRow($content = null)
  {
    return CHtml::tag('div', array('data-row' => 'new-row'), CHtml::tag('div', array('class' => 'row-fluid'), CHtml::tag('div', array('class' => 'span12'), $content)));
  }

  protected function formatContentStub()
  {
    return CHtml::tag('div', array(
        'class'             => 'movable',
        'data-content-stub' => 'true',
        ), Yii::t('admin.content.labels', 'Content Stub')
    );
  }
}
