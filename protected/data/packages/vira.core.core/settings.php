<?php

return array(
  'base' => array(
    'label' => 'General Configuration',
    'categories' => array(
      'main' => array(
        'label' => '',
        'items' => array(
          'name' => array(
            'label' => 'Application ID',
            'path' => '',
            'type' => 'string',
            'defaultValue' => 'ViraCMS',
            'valueExpression' => 'Yii::app()->name',
          ),
          'charset' => array(
            'label' => 'Site Encoding Charset',
            'path' => '',
            'type' => 'select',
            'defaultValue' => 'UTF-8',
            'values' => array(
              'UTF-8' => 'UTF-8',
              'CP-1251' => 'CP-1251',
              'ISO-8859-1' => 'ISO-8859-1',
            ),
            'valueExpression' => 'Yii::app()->charset',
          ),
          'language' => array(
            'label' => 'Application Frontend Language',
            'path' => '',
            'type' => 'select',
            'key' => 'id',
            'attribute' => 'title',
            'model' => 'VLanguage',
            'defaultValue' => 'ru',
            'valueExpression' => 'Yii::app()->language',
          ),
          'backendLanguage' => array(
            'label' => 'Application Backend Language',
            'path' => '',
            'type' => 'select',
            'key' => 'id',
            'attribute' => 'title',
            'model' => 'VLanguage',
            'defaultValue' => 'ru',
            'valueExpression' => 'Yii::app()->backendLanguage',
          ),
          'frontendTheme' => array(
            'label' => 'Default Frontend Theme',
            'path' => 'components.themeManager',
            'type' => 'select',
            'defaultValue' => '',
            'valuesExpression' => 'Yii::app()->themeManager->getFrontendThemes()',
            'valueExpression' => 'Yii::app()->themeManager->frontendTheme',
          ),
          'backendTheme' => array(
            'label' => 'Backend Theme',
            'path' => 'components.themeManager',
            'type' => 'select',
            'defaultValue' => '',
            'valuesExpression' => 'Yii::app()->themeManager->getBackendThemes()',
            'valueExpression' => 'Yii::app()->themeManager->backendTheme',
          ),
        ),
      ),
    ),
  ),
  'components' => array(
    'label' => 'Application Components Configuration',
    'categories' => array(
      array(
        'label' => 'Core Mailer Configuration',
        'items' => array(
          'mailer' => array(
            'label' => 'Engine: mail, sendmail or smtp',
            'path' => 'components.mailer',
            'type' => 'select',
            'defaultValue' => 'mail',
            'values' => array(
              'mail',
              'sendmail',
              'smtp',
            ),
            'valueExpression' => 'Yii::app()->mailer->mailer',
          ),
          'encoding' => array(
            'label' => 'Content-Type Encoding',
            'path' => 'components.mailer',
            'type' => 'string',
            'defaultValue' => '8bit',
            'valueExpression' => 'Yii::app()->mailer->encoding',
          ),
          'fromEmail' => array(
            'label' => 'Send From E-Mail',
            'path' => 'components.mailer',
            'type' => 'string',
            'defaultValue' => 'admin@localhost.localdomain',
            'valueExpression' => 'Yii::app()->mailer->fromEmail',
          ),
          'fromName' => array(
            'label' => 'Send From Name',
            'path' => 'components.mailer',
            'type' => 'string',
            'defaultValue' => 'Admin',
            'valueExpression' => 'Yii::app()->mailer->fromName',
          ),
          'smtpHost' => array(
            'label' => 'SMTP Hostname Or IP Address',
            'path' => 'components.mailer',
            'type' => 'string',
            'defaultValue' => 'localhost',
            'valueExpression' => 'Yii::app()->mailer->smtpHost',
          ),
          'smtpPort' => array(
            'label' => 'SMTP Port: 25, 587 for SSL, 465 for TLS',
            'path' => 'components.mailer',
            'type' => 'integer',
            'defaultValue' => '25',
            'valueExpression' => 'Yii::app()->mailer->smtpPort',
          ),
          'smtpSecure' => array(
            'label' => 'SMTP Secure Connection: none, SSL or TLS',
            'path' => 'components.mailer',
            'type' => 'select',
            'defaultValue' => '',
            'values' => array(
              '' => '--',
              'ssl' => 'SSL',
              'tls' => 'TLS',
            ),
            'valueExpression' => 'Yii::app()->mailer->smtpSecure',
          ),
          'smtpAuth' => array(
            'label' => 'SMTP Server Requires Authentication',
            'path' => 'components.mailer',
            'type' => 'boolean',
            'defaultValue' => false,
            'valueExpression' => 'Yii::app()->mailer->smtpAuth',
          ),
          'smtpUsername' => array(
            'label' => 'SMTP Authentication Username',
            'path' => 'components.mailer',
            'type' => 'string',
            'defaultValue' => '',
            'valueExpression' => 'Yii::app()->mailer->smtpUsername',
          ),
          'smtpPassword' => array(
            'label' => 'SMTP Authentication Password',
            'path' => 'components.mailer',
            'type' => 'string',
            'defaultValue' => '',
            'valueExpression' => 'Yii::app()->mailer->smtpPassword',
          ),
        ),
      ),
      array(
        'label' => 'Core Password Generator Configuration',
        'items' => array(
          'enableDigits' => array(
            'label' => 'Enable 0-9 Characters',
            'path' => 'components.passwordGenerator',
            'type' => 'boolean',
            'defaultValue' => true,
            'valueExpression' => 'Yii::app()->passwordGenerator->enableDigits',
          ),
          'enableCapitals' => array(
            'label' => 'Enable A-Z Characters',
            'path' => 'components.passwordGenerator',
            'type' => 'boolean',
            'defaultValue' => true,
            'valueExpression' => 'Yii::app()->passwordGenerator->enableCapitals',
          ),
          'enableSymbols' => array(
            'label' => 'Enable Special Symbols',
            'path' => 'components.passwordGenerator',
            'type' => 'boolean',
            'defaultValue' => true,
            'valueExpression' => 'Yii::app()->passwordGenerator->enableSymbols',
          ),
        ),
      ),
    ),
  ),
  'params' => array(
    'label' => 'Application Parameters',
    'categories' => array(
      array(
        'label' => '',
        'items' => array(
          'adminEmail' => array(
            'label' => 'Site Administrator E-Mail',
            'path' => 'params',
            'type' => 'string',
            'defaultValue' => 'root@localhost',
            'valueExpression' => 'Yii::app()->params["adminEmail"]',
          ),
          'defaultPageSize' => array(
            'label' => 'Default Page Size For Tables And Lists',
            'path' => 'params',
            'type' => 'integer',
            'defaultValue' => 10,
            'valueExpression' => 'Yii::app()->params["defaultPageSize"]',
          ),
          'stayLoggedIn' => array(
            'label' => 'Timeout For User Login (seconds)',
            'path' => 'params',
            'type' => 'integer',
            'defaultValue' => 3600,
            'valueExpression' => 'Yii::app()->params["stayLoggedIn"]',
          ),
          'passwordLengthMin' => array(
            'label' => 'Minimum Password Length',
            'path' => 'params',
            'type' => 'integer',
            'defaultValue' => 6,
            'valueExpression' => 'Yii::app()->params["passwordLengthMin"]',
          ),
          'passwordLengthMax' => array(
            'label' => 'Maximum Password Length',
            'path' => 'params',
            'type' => 'integer',
            'defaultValue' => 21,
            'valueExpression' => 'Yii::app()->params["passwordLengthMax"]',
          ),
          'passwordRestoreTTL' => array(
            'label' => 'Password Restore URL TTL (seconds)',
            'path' => 'params',
            'type' => 'integer',
            'defaultValue' => 86400,
            'valueExpression' => 'Yii::app()->params["passwordRestoreTTL"]',
          ),
        ),
      ),
    ),
  ),
  'web' => array(
    'label' => 'Web Application Configuration',
    'categories' => array(
      array(
        'label' => 'Session Configuration',
        'items' => array(
          'timeout' => array(
            'label' => 'Session Timeout (seconds)',
            'path' => 'components.session',
            'type' => 'integer',
            'defaultValue' => 86400,
            'valueExpression' => 'Yii::app()->session->timeout',
          ),
        ),
      ),
    ),
  ),
  'modules' => array(
    'label' => 'Modules Configuration',
    'categories' => array(
      'content' => array(
        'label' => 'Content Module Configuration',
        'translate' => 'admin.content.settings',
        'items' => array(
          'sitemap' => array(
            'translate' => 'admin.content.settings',
            'label' => 'Site Map Component',
            'path' => 'modules.admin.modules.content',
            'type' => 'select',
            'defaultValue' => 'DraggableSiteMap',
            'values' => array(
              'DraggableSiteMap' => 'Draggable Site Map',
            ),
            'valuesTranslate' => 'admin.content.settings',
            'valueExpression' => '$override["modules"]["admin"]["modules"]["content"]["sitemap"]',
          ),
          'audioWidgetClass' => array(
            'translate' => 'admin.content.settings',
            'label' => 'Audio Player',
            'path' => 'components.audioPlayer',
            'type' => 'select',
            'defaultValue' => '',
            'values' => array(
              'application.components.audio.soundmanager2.ESoundManager2' => 'Sound Manager 2',
            ),
            'valueExpression' => 'Yii::app()->audioPlayer->audioWidgetClass',
          ),
          'photoWidgetClass' => array(
            'translate' => 'admin.content.settings',
            'label' => 'Photo Viewer',
            'path' => 'components.photoViewer',
            'type' => 'select',
            'defaultValue' => '',
            'values' => array(
              'application.components.photo.colorbox.EColorBox' => 'ColorBox',
            ),
            'valueExpression' => 'Yii::app()->photoViewer->photoWidgetClass',
          ),
          'videoWidgetClass' => array(
            'translate' => 'admin.content.settings',
            'label' => 'Video Player',
            'path' => 'components.videoPlayer',
            'type' => 'select',
            'defaultValue' => '',
            'values' => array(
              'application.components.video.gddflvplayer.EGddFlvPlayer' => 'GddFlvPlayer',
            ),
            'valueExpression' => 'Yii::app()->videoPlayer->videoWidgetClass',
          ),
        ),
      ),
    ),
  ),
);
