<?php
/**
 * Application Core Components Configuration
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
    'cache' => array(
      'class' => 'CFileCache',
      'behaviors' => array(
        'VTaggedCacheBehavior',
      ),
    ),
    'clientScript' => array(
      'class' => 'VClientScript',
    ),
    'db' => array(
      'class' => 'VDbConnection',
      'connectionString' => 'mysql:host=localhost;dbname=<database>',
      'emulatePrepare' => true,
      'username' => '',
      'password' => '',
      'charset' => 'UTF8',
      'tablePrefix' => '',
      'enableProfiling' => false,
      'enableParamLogging' => true,
      'schemaCachingDuration' => 3600,
    ),
    'searchIndex' => array(
      'class' => 'VSearchIndex',
      'rules' => array(
        'Vira.Core.Page' => array(
          'class' => 'application.modules.admin.modules.content.models.VPageL10n',
          'key' => 'pageID',
          'attributes' => array(
            'siteID' => 'page.siteID',
            'languageID' => 'languageID',
            'title' => 'title',
            'text' => array(
              'description',
              'contents.content',
            ),
          ),
          'expressions' => array(
            'url' => '$model->page->createUrl()',
          ),
        ),
      ),
    ),
    'searchStorage' => array(
      'class' => 'VSearchStorage',
    ),
    'systemLog' => array(
      'class' => 'VSystemLog',
    ),
    'format' => array(
      'class' => 'VFormatter',
      'timeZone' => 'Europe/Moscow',
      'dateFormat' => 'd.m.Y',
      'datetimeFormat' => 'd.m.Y H:i:s',
      'timeFormat' => 'H:i:s',
      'numberFormat' => array(
        'decimals' => 2,
        'decimalSeparator' => ',',
        'thousandSeparator' => ' ',
      ),
    ),
    'image' => array(
      'class' => 'VImage',
    ),
    'log' => array(
      'class' => 'CLogRouter',
      'routes' => array(
        array(
          'class' => 'CFileLogRoute',
          'levels' => 'error',
        ),
      ),
    ),
    'mailer' => array(
      'class' => 'VMailer',
      'mailer' => 'mail',
      'encoding' => '8bit',
      'fromEmail' => 'admin@localhost.localdomain',
      'fromName' => 'Admin',
      'smtpHost' => 'localhost',
      'smtpPort' => '25',
      'smtpSecure' => '',
      'smtpAuth' => false,
      'smtpUsername' => '',
      'smtpPassword' => '',
      'smtpDebug' => false,
    ),
    'passwordGenerator' => array(
      'class' => 'VPasswordGenerator',
      'enableDigits' => true,
      'enableCapitals' => true,
      'enableSymbols' => true,
    ),
    'storage' => array(
      'class' => 'VLocalStorage',
      'directory' => 'webroot.files',
      'subDirectory' => true,
      'subDirectoryLevel' => 2,
      'subDirectoryChunk' => 2,
    ),
    'themeManager' => array(
      'class' => 'VThemeManager',
      'frontendTheme' => 'dark',
      'backendTheme' => 'dark',
      'themes' => array(
        'frontend' => array(
          'dark' => array(
            'title' => 'ViraCMS Frontend Dark Theme',
            'titleTranslate' => 'common',
            'css' => array(
              'theme',
              'theme-responsive',
              'font-awesome',
              '//fonts.googleapis.com/css?family=Open+Sans:300,300italic,400,400italic,700,700italic&subset=latin,cyrillic',
            ),
            'scripts' => array(
              'end' => array(
                'context',
              ),
            ),
            'imageDir' => 'img',
            'logo' => 'logo.png',
            'placeholder' => 'placeholder.jpg',
            'bootstrapCss' => false,
            'responsiveCss' => false,
            'yiiCss' => true,
            'bootstrapJs' => true,
            'captchaOptions' => array(
              'class' => 'VCaptchaAction',
              'transparent' => true,
              'backColor' => 0x000000,
              'foreColor' => 0xc31c4c,
              'width' => 100,
              'height' => 40,
            ),
          ),
          'light' => array(
            'title' => 'ViraCMS Frontend Light Theme',
            'titleTranslate' => 'common',
            'css' => array(
              'theme',
              'theme-responsive',
              'font-awesome',
              '//fonts.googleapis.com/css?family=Open+Sans:300,300italic,400,400italic,700,700italic&subset=latin,cyrillic',
            ),
            'scripts' => array(
              'end' => array(
                'context',
              ),
            ),
            'imageDir' => 'img',
            'logo' => 'logo.png',
            'placeholder' => 'placeholder.jpg',
            'bootstrapCss' => false,
            'responsiveCss' => false,
            'yiiCss' => true,
            'bootstrapJs' => true,
            'captchaOptions' => array(
              'class' => 'VCaptchaAction',
              'transparent' => true,
              'backColor' => 0x000000,
              'foreColor' => 0xc31c4c,
              'width' => 100,
              'height' => 40,
            ),
          ),
        ),
        'backend' => array(
          'dark' => array(
            'title' => 'ViraCMS Backend Dark Theme',
            'titleTranslate' => 'common',
            'css' => array(
              'theme',
              'theme-responsive',
              'font-awesome',
              '//fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800&subset=latin,cyrillic-ext,latin-ext',
            ),
            'scripts' => array(
              'end' => array(
                'context.js',
              ),
            ),
            'imageDir' => 'img',
            'logo' => 'logo.png',
            'placeholder' => 'placeholder.jpg',
            'bootstrapCss' => false,
            'responsiveCss' => false,
            'yiiCss' => true,
            'bootstrapJs' => true,
            'captchaOptions' => array(
              'transparent' => true,
              'backColor' => 0x000000,
              'foreColor' => 0xDE91A7,
              'width' => 100,
              'height' => 40,
            ),
          ),
          'light' => array(
            'title' => 'ViraCMS Backend Light Theme',
            'titleTranslate' => 'common',
            'css' => array(
              'theme',
              'theme-responsive',
              'font-awesome',
              '//fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800&subset=latin,cyrillic-ext,latin-ext',
            ),
            'scripts' => array(
              'end' => array(
                'context.js',
              ),
            ),
            'imageDir' => 'img',
            'logo' => 'logo.png',
            'placeholder' => 'placeholder.jpg',
            'bootstrapCss' => false,
            'responsiveCss' => false,
            'yiiCss' => true,
            'bootstrapJs' => true,
            'captchaOptions' => array(
              'class' => 'VCaptchaAction',
              'transparent' => true,
              'backColor' => 0x000000,
              'foreColor' => 0xc31c4c,
              'width' => 100,
              'height' => 40,
            ),
          ),
        ),
      ),
    ),
    'editor' => array(
      'class' => 'application.components.ViraEditor.ViraEditor',
    ),
    'siteMap' => array(
      'class' => 'VSiteMap',
    ),
    'eventManager' => array(
      'class' => 'VEventManager',
      'enabled' => true,
      'events' => array(
        array(
          'VDbConnection',
          'onConnectionInit',
          array('VCoreEventHandler', 'setSqlTimeZone'),
        ),
      ),
    ),
    'audioPlayer' => array(
      'class' => 'core.components.VAudioPlayer',
      'audioWidgetClass' => 'application.components.audio.soundmanager2.ESoundManager2',
    ),
    'photoViewer' => array(
      'class' => 'core.components.VPhotoViewer',
      'photoWidgetClass' => 'application.components.photo.colorbox.EColorBox',
    ),
    'videoPlayer' => array(
      'class' => 'core.components.VVideoPlayer',
      'videoWidgetClass' => 'application.components.video.gddflvplayer.EGddFlvPlayer',
    ),
  ),
  'params' => array(
    'db' => array(
      'mysql' => array(
        'engine' => 'InnoDB',
        'collate' => 'utf8_unicode_ci',
        'charset' => 'UTF8',
      ),
    ),
  ),
);
