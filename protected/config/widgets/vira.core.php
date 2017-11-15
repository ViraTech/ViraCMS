<?php
/**
 * Widget Factory Configuration
 * Core Widgets Configuration
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
return array(
  'import' => array(
    'application.widgets.core.VCheckBoxColumn',
    'application.widgets.core.VButtonColumn',
  ),
  'components' => array(
    'widgetFactory' => array(
      'class' => 'VWidgetFactory',
      'categories' => array(
        array(
          'category' => 'core',
          'name' => 'Core Widgets',
          'translate' => 'admin.widgets',
        ),
        array(
          'category' => 'custom',
          'name' => 'Custom Widgets',
          'translate' => 'admin.widgets',
        ),
      ),
      'widgets' => array(
        'CJuiAccordion' => array('cssFile' => false),
        'CJuiAutoComplete' => array('cssFile' => false),
        'CJuiButton' => array('cssFile' => false),
        'CJuiDatePicker' => array('cssFile' => false),
        'CJuiDialog' => array('cssFile' => false),
        'CJuiDraggable' => array('cssFile' => false),
        'CJuiDroppable' => array('cssFile' => false),
        'CJuiInputWidget' => array('cssFile' => false),
        'CJuiProgressBar' => array('cssFile' => false),
        'CJuiResizable' => array('cssFile' => false),
        'CJuiSelectable' => array('cssFile' => false),
        'CJuiSlider' => array('cssFile' => false),
        'CJuiSliderInput' => array('cssFile' => false),
        'CJuiSortable' => array('cssFile' => false),
        'CJuiTabs' => array('cssFile' => false),
        'CJuiWidget' => array('cssFile' => false),
        'CJuiInputWidget' => array('cssFile' => false),
        'JMultiSelect' => array('cssFile' => false),
      ),
      'available' => array(
        array(
          'category' => 'core',
          'class' => 'CustomNavbar.VCustomNavbarWidget',
          'name' => 'Custom Navbar Widget',
          'translate' => 'admin.widgets',
        ),
        array(
          'category' => 'core',
          'class' => 'CustomMenu.VCustomMenuWidget',
          'name' => 'Custom Menu Widget',
          'translate' => 'admin.widgets',
        ),
        array(
          'category' => 'core',
          'class' => 'SectionMenu.VSectionMenuWidget',
          'name' => 'Site Section Menu Widget',
          'translate' => 'admin.widgets',
        ),
        array(
          'category' => 'core',
          'class' => 'Breadcrumbs.VBreadcrumbsWidget',
          'name' => 'Breadcrumbs Widget',
          'translate' => 'admin.widgets',
        ),
        array(
          'category' => 'core',
          'class' => 'FrontendSitemap.VFrontendSitemapWidget',
          'name' => 'Frontend Sitemap Widget',
          'translate' => 'admin.widgets',
        ),
        array(
          'category' => 'core',
          'class' => 'LanguageSelector.VLanguageSelectorWidget',
          'name' => 'Language Selector Widget',
          'translate' => 'admin.widgets',
        ),
        array(
          'category' => 'core',
          'class' => 'CommonContentBlock.VCommonContentBlockWidget',
          'name' => 'Shared Content Block',
          'translate' => 'admin.widgets',
        ),
        array(
          'category' => 'core',
          'class' => 'SiteSearch.VSiteSearchWidget',
          'name' => 'Site Search',
          'translate' => 'admin.widgets',
        ),
        array(
          'category' => 'core',
          'class' => 'Carousel.VCarouselWidget',
          'name' => 'Carousel',
          'translate' => 'admin.widgets',
        ),
        array(
          'category' => 'core',
          'class' => 'Photo.VPhotoWidget',
          'name' => 'Photo',
          'translate' => 'admin.widgets',
        ),
      ),
    ),
  ),
);
