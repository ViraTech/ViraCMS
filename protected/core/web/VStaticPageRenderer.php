<?php
/**
 * ViraCMS Static Page Renderer Class
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VStaticPageRenderer extends VApplicationComponent
{
  const DEFAULT_LAYOUT_NAME = 'default';

  /**
   * @var mixed the rendering subject
   */
  protected $_subject;

  /**
   * @var mixed the layout name
   */
  protected $_layout;

  /**
   * @var array the subject contents
   */
  protected $_content = array();

  /**
   * @var boolean disable page cache
   */
  protected $_disableCache = false;

  /**
   * @var boolean set to true if content cache has been expired
   */
  protected $_cacheExpired = false;

  /**
   * Constructor
   *
   * @param mixed $subject the rendering subject
   */
  public function __construct($subject)
  {
    if ($subject) {
      if ($subject instanceof VPage) {
        $this->_disableCache = !$subject->cacheable;
      }

      if ($subject instanceof VSystemPage) {
        $this->_disableCache = true;
      }

      $this->_subject = $subject;

      $this->_layout = $subject->layoutID;
    }
    else {
      $this->_layout = Yii::app()->getController()->layout;
    }

    $this->init();
  }

  /**
   * Renderer initialization
   */
  public function init()
  {
    parent::init();

    if (Yii::app()->hasComponent('cache')) {
      $this->_content = Yii::app()->cache->get($this->getContentCacheKey());
    }

    if ($this->_disableCache || $this->_content === false) {
      $this->_cacheExpired = true;
      $this->prerender();
      $this->setPageMetadata();
    }
    else {
      Yii::app()->getController()->setPageTitle($this->_content['pageTitle']);
      Yii::app()->getClientScript()->setContentPack($this->_content['scripts']);
      $this->renderDynamicContent();
    }
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
    $controller = Yii::app()->getController();

    $output = $this->getOutput();

    if ($controller->getViewFile($view)) {
      $content = $controller->renderPartial($view, $data, true);
    }
    else {
      $content = $this->renderAreaOutput($this->_content['page']);
    }

    $output = strtr($output, array(
      '###VIRA_CONTENT_STUB###' => $content,
    ));

    if (($layoutFile = $controller->getLayoutFile($controller->layout)) === false) {
      $layoutFile = $controller->getLayoutFile(self::DEFAULT_LAYOUT_NAME);
    }

    if ($layoutFile !== false) {
      $output = $controller->renderFile($layoutFile, array('content' => $output), true);
    }

    $controller->afterRender($view, $output);

    $this->afterRender();

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
   * Creates page content grid, set SEO options, page custom styles and other meta tags.
   */
  protected function prerender()
  {
    $this->_content = array(
      'page' => array(
        'content' => array(),
        'dynamic' => false,
      ),
    );
    $siteID = Yii::app()->site->id;

    if ($this->_layout) {
      $layout = VSiteLayout::model()->with(array(
          'backgroundImage',
          'iconImage',
        ))->findByPk(array(
        'id' => $this->_layout,
        'siteID' => $siteID,
      ));
    }

    if (empty($layout)) {
      $layout = VSiteLayout::model()->with(array(
          'backgroundImage',
          'iconImage',
        ))->findByAttributes(
        array('siteID' => $siteID), array('order' => 't.default DESC')
      );
    }

    if (empty($layout)) {
      return array();
    }

    foreach (VLayoutArea::model()->findArea($siteID, $layout->id) as $layoutArea) {
      $area = $layoutArea->pageArea->attributes;
      $area['dynamic'] = false;
      $area['primary'] = $area['type'] == VPageAreaTypeCollection::PRIMARY;
      $area['content'] = array();

      $this->_content['area'][$layoutArea->pageAreaID] = $area;
    }

    $criteria = new CDbCriteria();

    $criteria->compare('t.layoutID', $layout->id);
    if ($this->_subject instanceof VPage) {
      $criteria->compare('t.pageID', $this->_subject->id, false, 'OR');
    }

    $criteria->with = array(
      'area',
      'blocks',
    );

    $criteria->compare('t.siteID', $siteID);
    $criteria->compare('t.languageID', Yii::app()->getLanguage());

    $criteria->order = Yii::app()->db->quoteColumnName('area.position') . ' ASC,' .
      Yii::app()->db->quoteColumnName('t.row') . ' ASC';

    foreach (VPageRow::model()->findAll($criteria) as $row) {
      if (!isset($this->_content['area'][$row->pageAreaID])) {
        continue;
      }

      $grid = $this->renderContentGrid($row->blocks);
      $grid['template'] = $row->template;

      if ($row->pageID) {
        $this->_content['page']['content'][] = $grid;
        $this->_content['page']['dynamic'] |= $grid['dynamic'];
      }
      else {
        $this->_content['area'][$row->pageAreaID]['content'][] = $grid;
        $this->_content['area'][$row->pageAreaID]['dynamic'] |= $grid['dynamic'];
      }
    }

    $body = '';

    if ($layout->backgroundImage) {
      $body .= "background-image: url('" . Yii::app()->storage->getFileUrl($this->_layout->backgroundImage->path) . "');";
    }

    if ($layout->bodyBackgroundColor) {
      $body .= 'background-color:' . $layout->bodyBackgroundColor . ';';
    }

    if ($body) {
      $body = 'body{' . $body . '}';
    }

    $links = '';

    if ($layout->linkColor) {
      $links .= 'a{color:' . $layout->linkColor . '}';
    }

    if ($layout->linkHoverColor) {
      $links .= 'a:hover,.page a:active,.page a:focus{color:' . $layout->linkHoverColor . '}';
    }

    if ($layout->linkVisitedColor) {
      $links .= 'a:visited{color:' . $layout->linkVisitedColor . '}';
    }

    $this->_content['styles'] = implode("\n", array_filter(array(
      $body,
      $links,
      $layout->styleOverride,
    )));

    if ($this->_subject) {
      $this->_content['subject'] = $this->_subject->attributes;
      $this->_content['l10n'] = $this->_subject->getL10nModel(Yii::app()->getLanguage(), false)->attributes;
      $this->_content['seo'] = $this->_subject->getSeoModel(Yii::app()->getLanguage(), false)->attributes;
    }

    $this->_content['metaTags'] = $layout->metaTags;

    if ($layout->iconImage) {
      $icons = array();
      foreach ($this->getFavIconDimensions() as $options) {
        $url = $layout->iconImage->getUrl($options['width'], $options['height']);
        $tag = $options['tag'];
        unset($options['width']);
        unset($options['height']);
        unset($options['tag']);
        switch ($tag) {
          case 'meta':
            $options['content'] = $url;
            $icon = CHtml::tag('meta', $options, '');
            break;

          default:
            $options['href'] = $url;
            $icon = CHtml::tag('link', $options, '');
        }
        $icons[] = $icon;
      }
      $this->_content['metaTags'] .= "\n" . implode("\n", $icons);
    }
  }

  /**
   * Sets page metadata
   */
  protected function setPageMetadata()
  {
    $controller = Yii::app()->getController();

    if (isset($this->_content['styles']) && $this->_content['styles']) {
      $controller->cs->registerCss('StyleOverride', $this->_content['styles']);
    }

    if (isset($this->_content['metaTags']) && $this->_content['metaTags']) {
      foreach (explode("\n", $this->_content['metaTags']) as $meta) {
        Yii::app()->getClientScript()->addHtmlMetaTag($meta);
      }
    }

    if (!empty($this->_content['l10n']['title'])) {
      $controller->setPageTitle($this->_content['l10n']['title']);
    }

    if (!empty($this->_content['seo']) && !empty($this->_content['seo']['title'])) {
      $controller->setPageTitle($this->_content['seo']['title']);
    }

    if (!empty($this->_content['seo'])) {
      $controller->setSeoKeywords($this->_content['seo']['keywords']);
      $controller->setSeoDescription($this->_content['seo']['description']);
    }
  }

  /**
   * After Render Handler
   * Saves rendered page content into the application cache
   */
  protected function afterRender()
  {
    $this->_content['pageTitle'] = Yii::app()->getController()->getPageTitle();
    $this->_content['scripts'] = Yii::app()->getClientScript()->getContentPack();

    if (!$this->_disableCache && $this->_cacheExpired && Yii::app()->hasComponent('cache')) {
      Yii::app()->cache->set(
        $this->getContentCacheKey(), $this->_content, Yii::app()->params['defaultCacheDuration'], new VTaggedCacheDependency(
        'Vira.Pages', Yii::app()->params['defaultCacheTagDuration']
        )
      );
    }
  }

  /**
   * Render the widgets for the content
   */
  protected function renderDynamicContent()
  {
    if (empty($this->_content['area'])) {
      $this->_content['area'] = array();
      return;
    }

    foreach ($this->_content['area'] as &$area) {
      $this->renderDynamicContentBlocks($area);
    }

    $this->renderDynamicContentBlocks($this->_content['page']);
  }

  protected function renderDynamicContentBlocks(&$area)
  {
    if (!$area['dynamic'] || !isset($area['content']) || !is_array($area['content'])) {
      return;
    }

    foreach ($area['content'] as &$row) {
      if (!$row['dynamic'] || isset($row['grid']) || is_array($row['grid'])) {
        continue;
      }

      foreach ($row['grid'] as &$block) {
        if (!$block['dynamic']) {
          continue;
        }

        $block['rendered'] = $this->renderContentBlock($block, true);
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

    if (isset($this->_content) && isset($this->_content['area'])) {
      foreach ($this->_content['area'] as $area) {
        $output .= $this->getAreaOutput($area);
      }
    }

    return $output;
  }

  /**
   * Render the area html output
   * @param array $area the area configuration
   * @return string
   */
  protected function getAreaOutput($area)
  {
    $content = CHtml::tag('div', array('class' => $area['container']), $this->renderAreaOutput($area));

    return CHtml::tag($area['tag'], array('class' => $area['classes']), $content);
  }

  /**
   * Page area renderer
   * @param mixed $area page area content grid or plain area content
   * @return string
   */
  protected function renderAreaOutput($area)
  {
    $output = '';

    if (is_array($area['content'])) {
      foreach ($area['content'] as $row) {
        $replace = array();

        foreach ($row['grid'] as $block) {
          $replace['###VIRA#' . $block['id'] . '###'] = $block['rendered'];
        }

        $template = strtr($row['template'], $replace);

        $output .= preg_replace('/###VIRA#\w{8}-\w{4}-\w{4}-\w{4}-\w{12}###/', '', $template);
      }
    }
    else {
      $output = $area['content'];
    }

    return $output;
  }

  protected function renderContentGrid($blocks)
  {
    $result = array(
      'dynamic' => false,
      'grid' => array(),
    );

    if (is_array($blocks) && count($blocks)) {
      foreach ($blocks as $block) {
        $rendered = $this->renderContentBlock($block);
        $result['grid'][] = $rendered;
        $result['dynamic'] |= $rendered['dynamic'];
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
        'id' => $data->id,
        'class' => $data->class,
        'content' => $data->content,
        'rendered' => '',
        'dynamic' => false,
      );
    }
    else {
      $result = $data;
    }

    if (isset($result['class']) && isset($result['content']) && class_exists($result['class'])) {
      $renderer = new $result['class']($result['content']);
      $result['dynamic'] = $renderer->isDynamic;
      $result['rendered'] = $renderer->render();
    }

    if ($plain) {
      $result = isset($result['rendered']) ? $result['rendered'] : '';
    }
    elseif (!$result['dynamic']) {
      unset($result['class']);
      unset($result['content']);
    }

    return $result;
  }

  /**
   * Returns subject cache key
   * @return string
   */
  protected function getContentCacheKey()
  {
    $controller = Yii::app()->getController();

    $key = array(
      'Vira.Content',
      Yii::app()->site->id,
      Yii::app()->getLanguage(),
    );

    if ($controller->module) {
      $key[] = $controller->module->id;
    }

    $key[] = $controller->id;
    $key[] = md5(serialize($controller->action->id) . serialize($controller->actionParams));

    return implode('.', $key);
  }

  /**
   * Returns favourite icon sizes with meta tag options
   * @return array
   */
  protected function getFavIconDimensions()
  {
    return array(
      array('tag' => 'link', 'rel' => 'apple-touch-icon', 'sizes' => '57x57', 'width' => 57, 'height' => 57),
      array('tag' => 'link', 'rel' => 'apple-touch-icon', 'sizes' => '60x60', 'width' => 60, 'height' => 60),
      array('tag' => 'link', 'rel' => 'apple-touch-icon', 'sizes' => '72x72', 'width' => 72, 'height' => 72),
      array('tag' => 'link', 'rel' => 'apple-touch-icon', 'sizes' => '76x76', 'width' => 76, 'height' => 76),
      array('tag' => 'link', 'rel' => 'apple-touch-icon', 'sizes' => '114x114', 'width' => 114, 'height' => 114),
      array('tag' => 'link', 'rel' => 'apple-touch-icon', 'sizes' => '120x120', 'width' => 120, 'height' => 120),
      array('tag' => 'link', 'rel' => 'apple-touch-icon', 'sizes' => '144x144', 'width' => 144, 'height' => 144),
      array('tag' => 'link', 'rel' => 'apple-touch-icon', 'sizes' => '152x152', 'width' => 152, 'height' => 152),
      array('tag' => 'link', 'rel' => 'apple-touch-icon', 'sizes' => '180x180', 'width' => 180, 'height' => 180),
      array('tag' => 'link', 'rel' => 'icon', 'type' => 'image/png', 'sizes' => '192x192', 'width' => 192, 'height' => 192),
      array('tag' => 'link', 'rel' => 'icon', 'type' => 'image/png', 'sizes' => '32x32', 'width' => 32, 'height' => 32),
      array('tag' => 'link', 'rel' => 'icon', 'type' => 'image/png', 'sizes' => '96x96', 'width' => 96, 'height' => 96),
      array('tag' => 'link', 'rel' => 'icon', 'type' => 'image/png', 'sizes' => '16x16', 'width' => 16, 'height' => 16),
      array('tag' => 'meta', 'name' => 'msapplication-TileImage', 'width' => 144, 'height' => 144),
    );
  }
}
