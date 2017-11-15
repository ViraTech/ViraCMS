<?php
/**
 * ViraCMS Administrator Module
 *
 * @package vira.core.core
 * @subpackage vira.core.translate
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class AdminModule extends VSystemWebModule
{
  public $params = array();
  private $_sites = array();

  /**
   * Generate components menu items
   * @param VController $ctx current controller
   * @return array
   */
  public function getComponentMenu($ctx)
  {
    $delimiter = array();

    $componentsMenu = array(
      array('label' => Yii::t('admin.labels', 'Modules')),
    );

    foreach ($this->getModules() as $id => $config) {
      $module = $this->getModule($id);
      if ($module !== null) {
        if (!method_exists($module, 'getModuleMenu')) {
          continue;
        }
        if (($menu = $module->getModuleMenu($ctx)) === array()) {
          continue;
        }
        $componentsMenu = CMap::mergeArray($componentsMenu, $menu);
      }
    }

    $coreComponentsMenu = array(
      array('label' => Yii::t('admin.labels', 'Engine Components')),
    );

    if ($this->checkAccess('coreCarouselRead,coreCarouselUpdate,coreCarouselDelete,corePhotoRead,corePhotoUpdate,corePhotoDelete')) {
      $coreComponentsMenu[] = array(
        'label' => Yii::t('admin.content.module', 'Standard Components'),
        'icon' => 'puzzle-piece',
        'url' => '#',
        'items' => array(
          array(
            'icon' => 'picture',
            'label' => Yii::t('admin.content.module', 'Carousel'),
            'url' => $ctx->createUrl('/admin/content/component/carousel/index'),
            'visible' => $this->checkAccess('coreCarouselRead,coreCarouselUpdate,coreCarouselDelete')
          ),
          array(
            'icon' => 'picture',
            'label' => Yii::t('admin.content.module', 'Photo'),
            'url' => $ctx->createUrl('/admin/content/component/photo/index'),
            'visible' => $this->checkAccess('corePhotoRead,corePhotoUpdate,corePhotoDelete')
          ),
        ),
      );
    }

    if ($this->checkAccess('translateLanguages', true)) {
      $coreComponentsMenu[] = array(
        'label' => Yii::t('admin.labels', 'Localization'),
        'icon' => 'headphones',
        'url' => '#',
        'items' => array(
          array(
            'label' => Yii::t('admin.labels', 'Languages'),
            'icon' => 'font',
            'url' => $ctx->createUrl('/admin/translate/language'),
          ),
          array(
            'label' => Yii::t('admin.labels', 'Source Messages'),
            'icon' => 'align-right',
            'url' => $ctx->createUrl('/admin/translate/source'),
          ),
          array(
            'label' => Yii::t('admin.labels', 'Translated Messages'),
            'icon' => 'align-left',
            'url' => $ctx->createUrl('/admin/translate/message'),
          ),
        ),
      );
    }

    if (count($componentsMenu) && count($coreComponentsMenu)) {
      $delimiter = array('---');
    }

    return CMap::mergeArray($componentsMenu, $delimiter, $coreComponentsMenu);
  }

  /**
   * Generate navigation bar configuration
   * @param VController $ctx current controller
   * @return array
   */
  public function getNavBarParams($ctx)
  {
    $logo = Yii::app()->theme->getLogoImage();

    $content = array(
      'label' => Yii::t('admin.labels', 'Content'),
      'url' => '#',
      'items' => array(),
    );

    $staticSiteContent = array();

    if ($this->checkAccess('sitePageCreate,sitePageUpdate,sitePageConfig,sitePageDelete')) {
      $staticSiteContent[] = array(
        'label' => Yii::t('admin.labels', 'Site Map'),
        'icon' => 'sitemap',
        'url' => $ctx->createUrl('/admin/content/page/map'),
      );

      $staticSiteContent[] = array(
        'label' => Yii::t('admin.labels', 'Pages'),
        'icon' => 'list',
        'url' => $ctx->createUrl('/admin/content/page/index'),
      );
    }

    if ($this->checkAccess('commonContentRead,commonContentUpdate,commonContentDelete')) {
      $staticSiteContent[] = array(
        'label' => Yii::t('admin.labels', 'Shared Blocks'),
        'icon' => 'copy',
        'url' => $ctx->createUrl('/admin/content/common/index'),
      );
    }

    if (count($staticSiteContent)) {
      array_unshift($staticSiteContent, array('label' => Yii::t('admin.labels', 'Static Content')));
    }

    $uploadedFiles = array();

    if ($this->checkAccess('staticFile,staticImage,staticMedia')) {
      $uploadedFiles[] = array(
        'label' => Yii::t('admin.labels', 'Files'),
        'icon' => 'file',
        'url' => $ctx->createUrl('/admin/content/upload/file/index'),
        'visible' => $this->checkAccess('staticFile', true),
      );

      $uploadedFiles[] = array(
        'label' => Yii::t('admin.labels', 'Images'),
        'icon' => 'picture',
        'url' => $ctx->createUrl('/admin/content/upload/image/index'),
        'visible' => $this->checkAccess('staticImage', true),
      );

      $uploadedFiles[] = array(
        'label' => Yii::t('admin.labels', 'Multimedia'),
        'icon' => 'film',
        'url' => $ctx->createUrl('/admin/content/upload/media/index'),
        'visible' => $this->checkAccess('staticMedia', true),
      );
    }

    if (count($uploadedFiles)) {
      array_unshift($uploadedFiles, array('label' => Yii::t('admin.labels', 'Uploaded Files')));
    }

    $contentCache = array();

    if ($this->checkAccess('contentCache', true)) {
      $contentCache[] = '---';
      $contentCache[] = array(
        'label' => Yii::t('admin.labels', 'Flush Cache'),
        'icon' => 'trash',
        'url' => $ctx->createUrl('/admin/config/cache/index'),
      );
    }

    $content['items'] = array_merge($staticSiteContent, $uploadedFiles, $contentCache);

    $registry = array(
      'label' => Yii::t('admin.labels', 'Registry'),
      'url' => '#',
      'items' => array(),
    );

    if ($this->checkAccess('registryAdmin,registryRole')) {
      $registry['items'][] = array(
        'label' => Yii::t('admin.labels', 'Administrators'),
        'icon' => 'briefcase',
        'url' => $ctx->createUrl('/admin/registry/admin/index'),
        'visible' => $this->checkAccess('registryAdmin', true),
      );
      $registry['items'][] = array(
        'label' => Yii::t('admin.labels', 'Administrator Roles'),
        'icon' => 'tags',
        'url' => $ctx->createUrl('/admin/registry/role/index'),
        'visible' => $this->checkAccess('registryRole', true),
      );
    }

    if ($this->checkAccess('registryEventLog,registryRequestLog,registryAuthLog')) {
      $registry['items'][] = '---';
      $registry['items'][] = array('label' => Yii::t('admin.labels', 'Application Logs'));
      $registry['items'][] = array(
        'label' => Yii::t('admin.labels', 'Authentication Log'),
        'icon' => 'list-alt',
        'url' => $ctx->createUrl('/admin/registry/auth/index'),
        'visible' => $this->checkAccess('registryAuthLog', true),
      );
      $registry['items'][] = array(
        'label' => Yii::t('admin.labels', 'Event Log'),
        'icon' => 'list-alt',
        'url' => $ctx->createUrl('/admin/registry/event/index'),
        'visible' => $this->checkAccess('registryEventLog', true),
      );
    }

    $settings = array(
      'label' => Yii::t('admin.labels', 'Settings'),
      'url' => '#',
      'items' => array(
        array(
          'label' => Yii::t('admin.labels', 'Profile'),
          'icon' => 'user',
          'url' => $ctx->createUrl('/admin/profile/index'),
        ),
      ),
    );

    if ($this->checkAccess('contentCustomMenu,contentArea,contentPageLayout,contentRowTemplate')) {
      $settings['items'][] = '---';
      $settings['items'][] = array('label' => Yii::t('admin.labels', 'Appearance'));
      $settings['items'][] = array(
        'label' => Yii::t('admin.labels', 'Custom Menus'),
        'icon' => 'list',
        'url' => $ctx->createUrl('/admin/content/appearance/menu/index'),
        'visible' => $this->checkAccess('contentCustomMenu', true),
      );
      $settings['items'][] = array(
        'label' => Yii::t('admin.labels', 'Content Areas'),
        'icon' => 'th-list',
        'url' => $ctx->createUrl('/admin/content/appearance/area/index'),
        'visible' => $this->checkAccess('contentArea', true),
      );
      $settings['items'][] = array(
        'label' => Yii::t('admin.labels', 'Page Layouts'),
        'icon' => 'book',
        'url' => $ctx->createUrl('/admin/content/appearance/layout/index'),
        'visible' => $this->checkAccess('contentPageLayout', true),
      );
      $settings['items'][] = array(
        'label' => Yii::t('admin.labels', 'Row Templates'),
        'icon' => 'columns',
        'url' => $ctx->createUrl('/admin/content/appearance/template/index'),
        'visible' => $this->checkAccess('contentRowTemplate', true),
      );
    }

    if ($this->checkAccess('coreSite,coreEmail,coreSettings,siteSystemPage')) {
      $settings['items'][] = '---';
      $settings['items'][] = array('label' => Yii::t('admin.labels', 'Configuration'));
      $settings['items'][] = array(
        'label' => Yii::t('admin.labels', 'Sites'),
        'icon' => 'building',
        'url' => $ctx->createUrl('/admin/config/site/index'),
        'visible' => $this->checkAccess('coreSite', true),
      );
      /* disabled // 20170531 // evc
        $settings['items'][] = array(
        'label' => Yii::t('admin.labels','System Pages'),
        'icon' => 'shield',
        'url' => $ctx->createUrl('/admin/config/system/index'),
        'visible' => $this->checkAccess('siteSystemPage',true),
        );
       */
      $settings['items'][] = array(
        'label' => Yii::t('admin.labels', 'E-Mail Templates'),
        'icon' => 'envelope',
        'url' => $ctx->createUrl('/admin/config/mail/index'),
        'visible' => $this->checkAccess('coreEmail', true),
      );
      $settings['items'][] = array(
        'label' => Yii::t('admin.labels', 'Application Settings'),
        'icon' => 'cog',
        'url' => $ctx->createUrl('/admin/config/settings/index'),
        'visible' => $this->checkAccess('coreSettings', true),
      );
      $settings['items'][] = array(
        'label' => Yii::t('admin.labels', 'Application Cache Settings'),
        'icon' => 'cog',
        'url' => $ctx->createUrl('/admin/config/settings/cache'),
        'visible' => $this->checkAccess('coreSettings', true),
      );
    }

    return array(
      'brand' => $logo ? CHtml::image(Yii::app()->theme->getImageUrl($logo, Yii::app()->site->name)) : 'ViraCMS',
      'brandUrl' => $ctx->createUrl('/admin/default/index'),
      'brandOptions' => array('class' => $logo ? 'brand-large' : 'brand-text', 'title' => 'ViraCMS v' . Yii::app()->getVersion()),
      'htmlOptions' => array('class' => 'no-border'),
      'fixed' => 'top',
      'items' => array(
        array(
          'class' => 'bootstrap.widgets.TbMenu',
          'items' => array_filter(array(
            count($content['items']) ? $content : null,
            array('label' => Yii::t('admin.labels', 'Components'), 'url' => '#', 'items' => $this->getComponentMenu($ctx)),
            count($registry['items']) ? $registry : null,
            $settings,
          )),
        ),
        array(
          'class' => 'bootstrap.widgets.TbMenu',
          'htmlOptions' => array(
            'class' => 'pull-right',
          ),
          'items' => array(
            $this->getSitesMenu($ctx),
            '---',
            array(
              'label' => Yii::t('common', 'Logout'),
              'url' => $ctx->createUrl('/admin/auth/logout'),
            ),
          ),
        ),
      )
    );
  }

  /**
   * Generate go to site menu
   * @param VController $ctx current controller
   * @return array
   */
  protected function getSitesMenu($ctx)
  {
    $list = array(
      'label' => Yii::t('admin.labels', 'View Site'),
      'url' => '#',
      'items' => array(),
    );

    if (count($this->sites) < 10) {
      foreach ($this->sites as $site) {
        $list['items'][] = $this->getSiteMenuEntry($ctx, $site);
      }
    }
    else {
      foreach ($this->sites as $site) {
        if ($site->default) {
          $list['items'][] = $this->getSiteMenuEntry($ctx, $site);
          break;
        }
      }

      if (($recently = Yii::app()->user->getState('Vira.RecentlyOpenSites')) !== null) {
        $list['items'][] = array(
          'label' => Yii::t('admin.labels', 'Recently Open'),
        );

        foreach ($recently as $recent) {
          foreach ($this->sites as $site) {
            if ($site->id == $recent) {
              $list['items'][] = $this->getSiteMenuEntry($ctx, $site);
              break;
            }
          }
        }
      }

      $list['items'][] = '---';

      $list['items'][] = array(
        'label' => Yii::t('admin.labels', 'Select Another Site'),
        'icon' => 'question-sign',
        'url' => 'javascript:void(0)',
        'linkOptions' => array('id' => 'site-select-list'),
      );

      $ctx->cs->registerScriptFile($ctx->coreScriptUrl('dialogs'));
      $ctx->cs->registerScript('SiteSelectorModal', "
$('#site-select-list').on('click',function(e)
{
	e.preventDefault();
	var modal = viraCoreConfirm('" . Yii::t('admin.labels', 'Select Site') . "',
		'" . str_replace("\n", '', CHtml::dropDownList('site-selector', '', CHtml::listData($this->sites, 'id', 'title'), array('class' => 'input-block-level'))) . "',
		function(e){
			e.preventDefault();
			modal.modal('hide');
			var popup = window.open(('" . $ctx->createUrl('/admin/site/open', array('id' => '00000000-0000-4000-8000-000000000000')) . "').replace('00000000-0000-4000-8000-000000000000',$('#site-selector').val()), '_blank');
			popup.focus();
		},
		function(e){},
		{
			ok: '" . Yii::t('admin.labels', 'Open Site') . "',
			cancel: '" . Yii::t('common', 'Cancel') . "'
		});
});
");
    }

    return $list;
  }

  /**
   * Generate sites list
   * @return VSite[]
   */
  public function getSites()
  {
    if (empty($this->_sites)) {
      $this->_sites = VSite::model()->autoFilter()->findAll();
    }

    return $this->_sites;
  }

  /**
   * Generate site menu entry
   * @param VController $ctx current controller
   * @param VSite $site site
   * @return array
   */
  protected function getSiteMenuEntry($ctx, $site)
  {
    return array(
      'label' => $site->title,
      'icon' => $site->default ? 'check' : 'check-empty',
      'url' => $this->getSiteUrl($ctx, $site),
      'linkOptions' => array('target' => '_blank'),
    );
  }

  /**
   * Generate site URL
   * @param VController $ctx current controller
   * @param VSite $site site
   * @return string
   */
  protected function getSiteUrl($ctx, $site)
  {
    return $site->host ?
      ((stripos($site->host, 'http://') !== 0 && stripos($site->host, 'https://') !== 0 ? 'http://' : '') . $site->host . '/') :
      $ctx->createUrl('/site/index');
  }

  public function checkAccess($list, $strict = false)
  {
    $ok = true;
    $manager = Yii::app()->authManager;

    if (!is_array($list)) {
      $list = explode(',', $list);
    }

    foreach ($list as $entry) {
      $access = $manager->checkAccess($entry, Yii::app()->user->id);
      $ok = $strict ? $ok && $access : $ok || $access;
      if (!$ok) {
        break;
      }
    }

    return $ok;
  }
}
