<?php
/**
 * Core Web Components Configuration
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
return array(
  'layout' => 'default',
  'import' => array(
    'application.core.web.*',
  ),
  'preload' => array(
    'bootstrap',
  ),
  'components' => array(
    'bootstrap' => array(
      'class' => 'ext.bootstrap.components.Bootstrap',
      'coreCss' => false,
      'responsiveCss' => false,
      'yiiCss' => false,
      'enableJS' => false,
    ),
    'assetManager' => array(
      'forceCopy' => false,
    ),
    'authManager' => array(
      'class' => 'VAuthManager',
      'accessSections' => array(
        'core' => array(
          'title' => 'Core',
          'translate' => 'admin.access',
        ),
        'registry' => array(
          'title' => 'Registry',
          'translate' => 'admin.access',
        ),
        'appearance' => array(
          'title' => 'Appearance',
          'translate' => 'admin.access',
        ),
        'content' => array(
          'title' => 'Content',
          'translate' => 'admin.access',
        ),
        'modules' => array(
          'title' => 'Modules',
          'translate' => 'admin.access',
        ),
      ),
      'accessGroups' => array(
        'core' => array(
          'section' => 'core',
          'title' => 'Core And Configuration',
          'translate' => 'admin.content.access',
        ),
        'translate' => array(
          'section' => 'core',
          'title' => 'Localization And Translations',
          'translate' => 'admin.translate.access',
        ),
        'appearance' => array(
          'section' => 'appearance',
          'title' => 'Appearance',
          'translate' => 'admin.content.access',
        ),
        'systemPages' => array(
          'section' => 'appearance',
          'title' => 'System Pages',
          'translate' => 'admin.content.access',
        ),
        'contentPages' => array(
          'section' => 'content',
          'title' => 'Static Pages',
          'translate' => 'admin.content.access',
        ),
        'contentFiles' => array(
          'section' => 'content',
          'title' => 'Uploaded Static Files',
          'translate' => 'admin.content.access',
        ),
        'contentCommon' => array(
          'section' => 'content',
          'title' => 'Shared Content Blocks',
          'translate' => 'admin.content.access',
        ),
        'contentCache' => array(
          'section' => 'content',
          'title' => 'Cache',
          'translate' => 'admin.content.access',
        ),
        'contentBootstrap' => array(
          'section' => 'content',
          'title' => 'Standard Components',
          'translate' => 'admin.content.access',
        ),
        'registryAccounts' => array(
          'section' => 'registry',
          'title' => 'Registry Accounts',
          'translate' => 'admin.registry.access',
        ),
        'registryLogs' => array(
          'section' => 'registry',
          'title' => 'Registry Logs',
          'translate' => 'admin.registry.access',
        ),
      ),
      'accessRules' => array(
        'contentArea' => array(
          'group' => 'appearance',
          'title' => 'Permit Access To Content Areas',
          'translate' => 'admin.content.access'
        ),
        'contentCache' => array(
          'group' => 'contentCache',
          'title' => 'Allow Cache Flush',
          'translate' => 'admin.content.access',
        ),
        'contentCustomMenu' => array(
          'group' => 'appearance',
          'title' => 'Permit Access To Custom Menu',
          'translate' => 'admin.content.access',
        ),
        'contentPageLayout' => array(
          'group' => 'appearance',
          'title' => 'Permit Access To Page Layouts',
          'translate' => 'admin.content.access',
        ),
        'contentRowTemplate' => array(
          'group' => 'appearance',
          'title' => 'Permit Access To Row Templates',
          'translate' => 'admin.content.access',
        ),
        'coreEmail' => array(
          'group' => 'core',
          'title' => 'Permit Access To E-mail Templates',
          'translate' => 'admin.content.access',
        ),
        'coreSettings' => array(
          'group' => 'core',
          'title' => 'Permit Access To Application Settings',
          'translate' => 'admin.content.access',
        ),
        'coreSite' => array(
          'group' => 'core',
          'title' => 'Permit Access To Sites',
          'translate' => 'admin.content.access',
        ),
        'registryAdmin' => array(
          'group' => 'registryAccounts',
          'title' => 'Permit Access To Administrator Accounts',
          'translate' => 'admin.registry.access',
        ),
        'registryAuthLog' => array(
          'group' => 'registryLogs',
          'title' => 'Allow Authentication Log View',
          'translate' => 'admin.registry.access',
        ),
        'registryEventLog' => array(
          'group' => 'registryLogs',
          'title' => 'Allow Event Log View',
          'translate' => 'admin.registry.access',
        ),
        'registryRole' => array(
          'group' => 'registryAccounts',
          'title' => 'Permit Access To Account Roles',
          'translate' => 'admin.registry.access',
        ),
        'sitePageConfig' => array(
          'group' => 'contentPages',
          'title' => 'Allow Site Page Configure',
          'translate' => 'admin.content.access',
        ),
        'sitePageCreate' => array(
          'group' => 'contentPages',
          'title' => 'Allow New Site Page Create',
          'translate' => 'admin.content.access',
        ),
        'sitePageDelete' => array(
          'group' => 'contentPages',
          'title' => 'Allow Site Page Delete',
          'translate' => 'admin.content.access',
        ),
        'sitePageUpdate' => array(
          'group' => 'contentPages',
          'title' => 'Allow Site Page Content Edit',
          'translate' => 'admin.content.access',
        ),
        'commonContentRead' => array(
          'group' => 'contentCommon',
          'title' => 'Allow Shared Content Block View',
          'translate' => 'admin.content.access',
        ),
        'commonContentUpdate' => array(
          'group' => 'contentCommon',
          'title' => 'Allow Shared Content Block Create & Update',
          'translate' => 'admin.content.access',
        ),
        'commonContentDelete' => array(
          'group' => 'contentCommon',
          'title' => 'Allow Shared Content Block Delete',
          'translate' => 'admin.content.access',
        ),
        'siteSystemPage' => array(
          'group' => 'systemPages',
          'title' => 'Permit Access to Site System Pages',
          'translate' => 'admin.content.access',
        ),
        'coreCarouselRead' => array(
          'group' => 'contentBootstrap',
          'title' => 'Allow To View Carousels',
          'translate' => 'admin.content.access',
        ),
        'coreCarouselUpdate' => array(
          'group' => 'contentBootstrap',
          'title' => 'Allow To Create and Update Carousels',
          'translate' => 'admin.content.access',
        ),
        'coreCarouselDelete' => array(
          'group' => 'contentBootstrap',
          'title' => 'Allow To Delete Carousels',
          'translate' => 'admin.content.access',
        ),
        'corePhotoRead' => array(
          'group' => 'contentBootstrap',
          'title' => 'Allow To View Photo',
          'translate' => 'admin.content.access',
        ),
        'corePhotoUpdate' => array(
          'group' => 'contentBootstrap',
          'title' => 'Allow To Create and Update Photo',
          'translate' => 'admin.content.access',
        ),
        'corePhotoDelete' => array(
          'group' => 'contentBootstrap',
          'title' => 'Allow To Delete Photo',
          'translate' => 'admin.content.access',
        ),
        'staticFile' => array(
          'group' => 'contentFiles',
          'title' => 'Permit Access To Files',
          'translate' => 'admin.content.access',
        ),
        'staticImage' => array(
          'group' => 'contentFiles',
          'title' => 'Permit Access To Images',
          'translate' => 'admin.content.access',
        ),
        'staticMedia' => array(
          'group' => 'contentFiles',
          'title' => 'Permit Access To Media Files',
          'translate' => 'admin.content.access',
        ),
        'translateLanguages' => array(
          'group' => 'translate',
          'title' => 'Permit Access To Languages',
          'translate' => 'admin.translate.access',
        ),
        'translateTranslate' => array(
          'group' => 'translate',
          'title' => 'Permit Access To Translations',
          'translate' => 'admin.translate.access',
        )
      ),
      'defaultRoles' => array(
        'guest',
      ),
    ),
    'session' => array(
      'class' => 'CDbHttpSession',
      'connectionID' => 'db',
      'autoCreateSessionTable' => false,
      'autoStart' => false,
      'sessionTableName' => '{{core_session}}',
      'timeout' => 86400,
    ),
    'user' => array(
      'class' => 'VWebUser',
      'loginUrl' => array('/admin/auth/login'),
      'allowAutoLogin' => true,
      'autoRenewCookie' => false,
    ),
    'eventManager' => array(
      'events' => array(
        array(
          'VController',
          'onBeforeAction',
          array('VCoreEventHandler', 'showLicenseKey'),
        ),
        array(
          'VPublicController',
          'onBeforeAction',
          array('VCoreEventHandler', 'setSiteLanguage'),
        ),
        array(
          'VPublicController',
          'onBeforeAction',
          array('VCoreEventHandler', 'determineUniqueVisitor'),
        ),
        array(
          'VPublicController',
          'onBeforeAction',
          array('VCoreEventHandler', 'logRequest'),
        ),
      ),
    ),
  ),
);
