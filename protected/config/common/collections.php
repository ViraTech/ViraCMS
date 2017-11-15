<?php
/**
 * Application Collections
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
return array(
  'components' => array(
    'collection' => array(
      'class' => 'VCollection',
      'import' => array(
        'application.collections.*',
      ),
      'collections' => array(
        'accountType' => array(
          'class' => 'VAccountTypeCollection',
        ),
        'pageRenderer' => array(
          'class' => 'VPageRendererCollection',
        ),
        'blockRenderer' => array(
          'class' => 'VBlockRendererCollection',
        ),
        'rendererAction' => array(
          'class' => 'VRendererActionCollection',
        ),
        'pageAreaType' => array(
          'class' => 'VPageAreaTypeCollection',
        ),
        'pageAreaContainer' => array(
          'class' => 'VPageAreaContainerCollection',
        ),
        'pageVisibility' => array(
          'class' => 'VPageVisibilityCollection',
        ),
        'pageAccessibility' => array(
          'class' => 'VPageAccessibilityCollection',
        ),
        'authLogType' => array(
          'class' => 'VAuthLogTypeCollection',
        ),
        'historyEvent' => array(
          'class' => 'VHistoryEventCollection',
        ),
      ),
    ),
  ),
);
