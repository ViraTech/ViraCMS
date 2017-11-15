<?php
/**
 * Base Application Configuration
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
return array(
  'basePath' => dirname(dirname(dirname(__FILE__))), // Application Path
  'runtimePath' => dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'runtime', // Temporary (Runtime) Path
  'name' => 'ViraCMS Instance', // Default Application Name
  'charset' => 'UTF-8', // Site Encoding
  'sourceLanguage' => 'en', // Application Source Messages Language
  'language' => 'ru', // Application Default Frontend Language
  'backendLanguage' => 'ru', // Application Default Backend Language
  'version' => '1.0.0',
  'import' => array(
    'application.core.*',
    'application.core.behaviors.*',
    'application.core.cache.*',
    'application.core.components.*',
    'application.core.controllers.*',
    'application.core.db.*',
    'application.core.events.*',
    'application.core.helpers.*',
    'application.core.models.*',
    'application.core.modules.*',
    'application.core.translations.*',
    'application.core.validators.*',
    'application.core.web.*',
    'application.core.widgets.*',
    'application.collections.*',
    'application.components.*',
    'application.components.vendors.*',
    'application.models.*',
    'application.forms.*',
    'application.modules.admin.modules.content.models.*',
    'application.modules.admin.modules.content.modules.appearance.models.*',
    'application.modules.admin.modules.content.modules.component.models.*',
    'application.modules.admin.modules.content.modules.upload.models.*',
    'application.modules.admin.modules.config.models.*',
    'application.modules.admin.modules.registry.models.*',
    'application.modules.admin.modules.translate.models.*',
  ),
  'preload' => array(
    'log',
  ),
  'components' => array(
    'errorHandler' => array(
      'errorAction' => 'error/error',
    ),
    'messages' => array(
      'class' => 'VMessageSource',
      'onMissingTranslation' => array('VMissingTranslationHandler', 'missingTranslation'),
    ),
    'guid' => array(
      'class' => 'VGuidComponent',
    ),
    'urlManager' => array(
      'class' => 'VUrlManager',
      'urlFormat' => 'path',
      'routeVar' => false,
      'urlSuffix' => '/',
      'showScriptName' => false,
      'caseSensitive' => false,
      'rules' => array(
        '/' => 'site/index',
        'media/audio' => 'media/audio',
        'media/video' => 'media/video',
        'login' => 'auth/login',
        'logout' => 'auth/logout',
        'restore' => 'auth/restore',
        'error' => 'error/error',
        'cache/<hash:\w+>/rs-w<width:\d+>-h<height:\d+>-<filename:.+>' => array('image/resize', 'urlSuffix' => '', 'caseSensitive' => true),
        'cache/<hash:\w+>/rs-w<width:\d+>-<filename:.+>' => array('image/resize', 'urlSuffix' => '', 'caseSensitive' => true),
        'cache/<hash:\w+>/rs-h<height:\d+>-<filename:.+>' => array('image/resize', 'urlSuffix' => '', 'caseSensitive' => true),
        'cache/<hash:\w+>/cr-w<width:\d+>-h<height:\d+>-<hpos:(left|right|center)>-<vpos:(top|bottom|middle)>-<filename:.+>' => array('image/crop', 'urlSuffix' => '', 'caseSensitive' => true),
        'cache/<hash:\w+>/cr-w<width:\d+>-h<height:\d+>-<filename:.+>' => array('image/crop', 'urlSuffix' => '', 'caseSensitive' => true),
        'cache/<hash:\w+>/ph-w<width:\d+>-h<height:\d+>-<filename:.+>' => array('image/placeholder', 'urlSuffix' => '', 'caseSensitive' => true),
        'tmp/<hash:\w+>/w<width:\d+>-h<height:\d+>-<filename:.+>' => array('image/temp', 'urlSuffix' => '', 'caseSensitive' => true),
        'admin' => 'admin/default/index',
        'admin/login' => 'admin/auth/login',
        'admin/logout' => 'admin/auth/logout',
        'admin/restore' => 'admin/auth/restore',
        'admin/content/appearance/layout/<action>/<id:.+>/' => 'admin/content/appearance/layout/<action>',
        'render/layout/<id:.+>/' => 'render/layout',
        'admin/registry/role/<action:\w+>/<id:.+>/' => 'admin/registry/role/<action>',
        'admin/translate/language/<action:\w+>/<id:\w+>' => 'admin/translate/language/<action>',
        'admin/translate/message/<action:\w+>' => 'admin/translate/message/<action>',
        'admin/translate/source/<action:\w+>' => 'admin/translate/source/<action>',
        array(
          'class' => 'application.core.web.VStaticPageUrlRuler',
        ),
      ),
    ),
    'eventManager' => array(
      'events' => array(
        array(
          'VLocalStorage',
          'onAddFile',
          array(),
        ),
        array(
          'VLocalStorage',
          'onDeleteFile',
          array(),
        ),
        array(
          'VMailer',
          'onBeforeSend',
          array(),
        ),
        array(
          'VMailer',
          'onAfterSend',
          array(),
        ),
        array(
          'VSite',
          'onAfterDelete',
          array('application.modules.admin.modules.content.components.VContentEventHandler', 'deleteSiteContent'),
        ),
        array(
          'VPage',
          'onAfterDelete',
          array('application.modules.admin.modules.content.components.VContentEventHandler', 'deletePageContent'),
        ),
        array(
          'VPageArea',
          'onAfterDelete',
          array('application.modules.admin.modules.content.components.VContentEventHandler', 'deletePageArea'),
        ),
        array(
          'VPageRow',
          'onAfterDelete',
          array('application.modules.admin.modules.content.components.VContentEventHandler', 'deletePageRow'),
        ),
        array(
          'VAccountRole',
          'onAfterDelete',
          array('application.modules.admin.modules.registry.components.VAccountRoleEventHandler', 'delete'),
        ),
        array(
          'VSiteAdmin',
          'onRestorePasswordRequest',
          array('application.modules.admin.modules.registry.components.VAdminAccountEventHandler', 'restorePasswordRequest'),
        ),
        array(
          'VSiteAdmin',
          'onRestorePasswordChange',
          array('application.modules.admin.modules.registry.components.VAdminAccountEventHandler', 'restorePasswordChange'),
        ),
        array(
          'VSiteAdmin',
          'onRestorePasswordChangeError',
          array('application.modules.admin.modules.registry.components.VAdminAccountEventHandler', 'restorePasswordChangeError'),
        ),
        array(
          'VSiteAdmin',
          'onAfterLogin',
          array('application.modules.admin.modules.registry.components.VAdminAccountEventHandler', 'login'),
        ),
        array(
          'VSiteAdmin',
          'onLoginError',
          array('application.modules.admin.modules.registry.components.VAdminAccountEventHandler', 'loginError'),
        ),
        array(
          'VSiteAdmin',
          'onBeforeLogout',
          array('application.modules.admin.modules.registry.components.VAdminAccountEventHandler', 'logout'),
        ),
        array(
          'VSiteAdmin',
          'onEnable',
          array(),
        ),
        array(
          'VSiteAdmin',
          'onDisable',
          array(),
        ),
      ),
    ),
  ),
  'modules' => array(
    'admin' => array(
      'import' => array(
        'application.modules.admin.forms.*',
        'application.modules.admin.models.*',
        'application.modules.admin.components.*',
      ),
      'modules' => array(
        'content' => array(
          'sitemap' => 'DraggableSiteMap',
          'import' => array(
            'application.modules.admin.modules.content.components.*',
          ),
          'modules' => array(
            'appearance',
            'component' => array(
              'import' => array(
                'application.modules.admin.modules.content.modules.component.components.*',
              ),
            ),
            'upload',
          ),
        ),
        'config' => array(
          'import' => array(
            'application.modules.admin.modules.config.components.*',
            'application.modules.admin.modules.config.forms.*',
          ),
        ),
        'registry' => array(
          'import' => array(
            'application.modules.admin.modules.registry.components.*',
          ),
        ),
        'translate',
      ),
    ),
  ),
);
