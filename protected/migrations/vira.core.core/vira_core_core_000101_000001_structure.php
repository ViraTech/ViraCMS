<?php
/**
 * ViraCMS Core Database Structure
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class vira_core_core_000101_000001_structure extends VDbMigration
{
  public $version = '1.0.0';

  public function safeUp()
  {
    $this->createTable('{{core_language}}', array(
      'id' => 'varchar(2)',
      'active' => 'boolean default 1',
      'locale' => 'varchar(15)',
      'title' => 'string',
      'index' => 'int default 0',
    ));
    $this->addPrimaryKey('pk_language', '{{core_language}}', 'id');

    $this->createTable('{{core_site_admin}}', array(
      'id' => 'varchar(36)',
      'siteAccess' => 'int(1) default 1',
      'roleID' => 'varchar(32)',
      'status' => 'int(1) default 1',
      'languageID' => 'varchar(2)',
      'username' => 'string',
      'email' => 'string',
      'salt' => 'varchar(32)',
      'password' => 'varchar(32)',
      'name' => 'string',
      'timeCurrentLogin' => 'int default 0',
      'ipCurrentLogin' => 'int default 0',
      'timeLastLogin' => 'int default 0',
      'ipLastLogin' => 'int default 0',
    ));
    $this->addPrimaryKey('pk_core_site_admin', '{{core_site_admin}}', 'id');

    $this->createTable('{{core_site_admin_access}}', array(
      'adminID' => 'varchar(36)',
      'siteID' => 'varchar(36)',
    ));
    $this->addPrimaryKey('pk_site_admin_access', '{{core_site_admin_access}}', 'adminID,siteID');

    $this->createTable('{{core_site}}', array(
      'id' => 'varchar(36)',
      'title' => 'varchar(255)',
      'host' => 'varchar(1022)',
      'domains' => 'text',
      'redirect' => 'boolean default 0',
      'default' => 'boolean default 0',
      'theme' => 'varchar(1022)',
      'webroot' => 'varchar(4094)',
    ));
    $this->addPrimaryKey('pk_core_site', '{{core_site}}', 'id');

    $this->createTable('{{core_site_layout}}', array(
      'id' => 'varchar(36)',
      'siteID' => 'varchar(36)',
      'title' => 'varchar(255)',
      'default' => 'boolean default 0',
      'linkColor' => 'varchar(7)',
      'linkHoverColor' => 'varchar(7)',
      'linkVisitedColor' => 'varchar(7)',
      'bodyTextColor' => 'varchar(7)',
      'bodyBackgroundColor' => 'varchar(7)',
      'bodyBackgroundImage' => 'varchar(36)',
      'favIconImage' => 'varchar(36)',
      'styleOverride' => 'text',
      'metaTags' => 'text',
    ));
    $this->addPrimaryKey('pk_site_layout', '{{core_site_layout}}', 'id,siteID');

    $this->createTable('{{core_content_common}}', array(
      'id' => 'varchar(36)',
      'title' => 'varchar(1022)',
      'content' => $this->getDbConnection()->driverName == 'mysql' ? 'mediumtext' : 'blob',
      'style' => $this->getDbConnection()->driverName == 'mysql' ? 'mediumtext' : 'blob',
      'script' => $this->getDbConnection()->driverName == 'mysql' ? 'mediumtext' : 'blob',
    ));
    $this->addPrimaryKey('pk_core_content_common', '{{core_content_common}}', 'id');

    $this->createTable('{{core_content_file}}', array(
      'id' => 'varchar(36)',
      'siteID' => 'varchar(36)',
      'className' => 'varchar(255)',
      'primaryKey' => 'varchar(255)',
      'filename' => 'varchar(1022)',
      'mime' => 'string',
      'size' => 'int',
      'path' => 'varchar(4094)',
      'comment' => 'varchar(1022)',
    ));
    $this->addPrimaryKey('pk_core_content_file', '{{core_content_file}}', 'id');
    $this->createIndex('idx_model', '{{core_content_file}}', 'className,primaryKey');

    $this->createTable('{{core_content_image}}', array(
      'id' => 'varchar(36)',
      'siteID' => 'varchar(36)',
      'className' => 'varchar(255)',
      'primaryKey' => 'varchar(255)',
      'filename' => 'varchar(1022)',
      'mime' => 'string',
      'size' => 'int',
      'width' => 'int',
      'height' => 'int',
      'path' => 'varchar(4094)',
      'comment' => 'varchar(1022)',
    ));
    $this->addPrimaryKey('pk_core_content_image', '{{core_content_image}}', 'id');
    $this->createIndex('idx_model', '{{core_content_image}}', 'className,primaryKey');

    $this->createTable('{{core_content_media}}', array(
      'id' => 'varchar(36)',
      'siteID' => 'varchar(36)',
      'className' => 'varchar(255)',
      'primaryKey' => 'varchar(255)',
      'filename' => 'varchar(1022)',
      'mime' => 'string',
      'size' => 'int',
      'path' => 'varchar(4094)',
      'comment' => 'varchar(1022)',
    ));
    $this->addPrimaryKey('pk_core_content_media', '{{core_content_media}}', 'id');
    $this->createIndex('idx_model', '{{core_content_media}}', 'className,primaryKey');

    $this->createTable('{{core_content_template}}', array(
      'id' => 'varchar(64)',
      'title' => 'string',
      'template' => 'text',
    ));
    $this->addPrimaryKey('pk_core_content_template', '{{core_content_template}}', 'id');

    $this->createTable('{{core_custom_menu}}', array(
      'id' => 'varchar(36)',
      'siteID' => 'varchar(36)',
      'title' => 'string',
    ));
    $this->addPrimaryKey('pk_core_custom_menu', '{{core_custom_menu}}', 'id');

    $this->createTable('{{core_custom_menu_item}}', array(
      'id' => 'varchar(36)',
      'menuID' => 'varchar(36)',
      'parentID' => 'varchar(36)',
      'pageID' => 'varchar(36)',
      'url' => 'varchar(4094)',
      'target' => 'varchar(32)',
      'anchor' => 'varchar(255)',
      'position' => 'int default 0',
    ));
    $this->addPrimaryKey('pk_core_custom_menu_item', '{{core_custom_menu_item}}', 'id');

    $this->createTable('{{core_custom_menu_l10n}}', array(
      'itemID' => 'varchar(36)',
      'languageID' => 'varchar(2)',
      'title' => 'varchar(1022)',
    ));
    $this->addPrimaryKey('pk_custom_menu_l10n', '{{core_custom_menu_l10n}}', 'itemID,languageID');

    $this->createTable('{{core_log_event}}', array(
      'id' => 'varchar(36)',
      'siteID' => 'varchar(36)',
      'className' => 'varchar(255)',
      'primaryKey' => 'varchar(255)',
      'source' => 'varchar(64)',
      'event' => 'string',
      'params' => 'varchar(4094)',
      'translate' => 'varchar(128)',
      'authorType' => 'varchar(64)',
      'authorID' => 'varchar(36)',
      'remote' => 'int',
      'time' => 'int',
    ));
    $this->addPrimaryKey('pk_core_log_event', '{{core_log_event}}', 'id');
    $this->createIndex('idx_site', '{{core_log_event}}', 'siteID');
    $this->createIndex('idx_model', '{{core_log_event}}', 'className,primaryKey');
    $this->createIndex('idx_source', '{{core_log_event}}', 'source');
    $this->createIndex('idx_time', '{{core_log_event}}', 'time');

    $this->createTable('{{core_log_auth}}', array(
      'id' => 'varchar(36)',
      'siteID' => 'varchar(36)',
      'type' => 'int',
      'result' => 'boolean',
      'authorType' => 'varchar(64)',
      'authorID' => 'varchar(36)',
      'remote' => 'int',
      'time' => 'int',
    ));
    $this->addPrimaryKey('pk_core_log_auth', '{{core_log_auth}}', 'id');
    $this->createIndex('idx_site', '{{core_log_auth}}', 'siteID');
    $this->createIndex('idx_type', '{{core_log_auth}}', 'type');
    $this->createIndex('idx_result', '{{core_log_auth}}', 'result');
    $this->createIndex('idx_time', '{{core_log_auth}}', 'time');

    $this->createTable('{{core_request_stat}}', array(
      'siteID' => 'varchar(36)',
      'date' => 'date',
      'users' => 'int default 0',
      'requests' => 'int default 0',
    ));
    $this->addPrimaryKey('pk_core_request_stat', '{{core_request_stat}}', 'siteID,date');

    $this->createTable('{{core_page}}', array(
      'id' => 'varchar(36)',
      'siteID' => 'varchar(36)',
      'layoutID' => 'varchar(64)',
      'class' => 'varchar(64)',
      'cacheable' => 'int(1) default 0',
      'url' => 'string',
      'redirectRoute' => 'string',
      'redirectParam' => 'varchar(4094)',
      'redirectValue' => 'varchar(4094)',
      'redirectUrl' => 'varchar(4094)',
      'parentID' => 'varchar(36)',
      'homepage' => 'int(1) default 0',
      'visibility' => 'int(4) default 0',
      'accessibility' => 'int default 0',
      'position' => 'int default 0',
    ));
    $this->addPrimaryKey('pk_core_page', '{{core_page}}', 'id');
    $this->createIndex('idx_core_page_parent', '{{core_page}}', 'parentID');
    $this->createIndex('idx_core_page_layout', '{{core_page}}', 'layoutID');
    $this->createIndex('idx_core_page_pos', '{{core_page}}', 'position');

    $this->createTable('{{core_page_l10n}}', array(
      'pageID' => 'varchar(36)',
      'languageID' => 'varchar(2)',
      'name' => 'varchar(255)',
    ));
    $this->addPrimaryKey('pk_core_page_l10n', '{{core_page_l10n}}', 'pageID,languageID');

    $this->createTable('{{core_page_area}}', array(
      'id' => 'varchar(36)',
      'title' => 'varchar(255)',
      'tag' => 'varchar(64)',
      'classes' => 'varchar(1022)',
      'type' => 'int default 0',
      'container' => 'varchar(255)',
      'position' => 'int default 0',
    ));
    $this->addPrimaryKey('pk_core_page_area', '{{core_page_area}}', 'id');

    $this->createTable('{{core_layout_area}}', array(
      'pageAreaID' => 'varchar(36)',
      'siteID' => 'varchar(36)',
      'layoutID' => 'varchar(64)',
    ));
    $this->addPrimaryKey('pk_core_layout_area', '{{core_layout_area}}', 'pageAreaID,siteID,layoutID');

    $this->createTable('{{core_page_block}}', array(
      'id' => 'varchar(36)',
      'siteID' => 'varchar(36)',
      'pageID' => 'varchar(36)',
      'layoutID' => 'varchar(64)',
      'pageAreaID' => 'varchar(36)',
      'languageID' => 'varchar(2)',
      'class' => 'string',
      'content' => 'text'
    ));
    $this->addPrimaryKey('pk_core_page_block', '{{core_page_block}}', 'id');
    $this->createIndex('idx_site_page', '{{core_page_block}}', 'siteID,pageID');
    $this->createIndex('idx_site_layout', '{{core_page_block}}', 'siteID,layoutID');

    $this->createTable('{{core_page_row}}', array(
      'siteID' => 'varchar(36)',
      'pageID' => 'varchar(36)',
      'layoutID' => 'varchar(64)',
      'languageID' => 'varchar(2)',
      'pageAreaID' => 'varchar(36)',
      'row' => 'int default 1',
      'template' => 'text',
    ));
    $this->addPrimaryKey('pk_core_page_row', '{{core_page_row}}', 'siteID,layoutID,pageID,languageID,pageAreaID,row');

    $this->createTable('{{core_system_page}}', array(
      'id' => 'varchar(36)',
      'siteID' => 'varchar(36)',
      'layoutID' => 'varchar(36)',
      'module' => 'varchar(255)',
      'controller' => 'varchar(255)',
      'view' => 'varchar(255)',
      'timeUpdated' => 'int',
      'updatedBy' => 'varchar(36)',
    ));
    $this->addPrimaryKey('pk_core_system_page', '{{core_system_page}}', 'id');

    $this->createTable('{{core_system_page_l10n}}', array(
      'systemPageID' => 'varchar(36)',
      'languageID' => 'varchar(2)',
      'name' => 'varchar(64)',
      'title' => 'varchar(1022)',
      'keywords' => 'text',
      'description' => 'text',
      'content' => $this->getDbConnection()->driverName == 'mysql' ? 'mediumtext' : 'blob',
    ));
    $this->addPrimaryKey('pk_core_system_page_l10n', '{{core_system_page_l10n}}', 'systemPageID,languageID');

    $this->createTable('{{core_system_view}}', array(
      'module' => 'varchar(255)',
      'controller' => 'varchar(255)',
      'view' => 'varchar(255)',
      'title' => 'varchar(255)',
      'translate' => 'varchar(255)',
    ));
    $this->addPrimaryKey('pk_core_system_view', '{{core_system_view}}', 'module,controller,view');

    $this->createTable('{{core_password_restore}}', array(
      'id' => 'varchar(36)',
      'area' => 'int default 0',
      'email' => 'string',
      'username' => 'string',
      'name' => 'string',
      'languageID' => 'varchar(2)',
      'expire' => 'int',
    ));

    $this->createTable('{{core_session}}', array(
      'id' => 'varchar(32)',
      'expire' => 'int(11)',
      'data' => 'longblob',
    ));
    $this->addPrimaryKey('pk_session', '{{core_session}}', 'id');

    $this->createTable('{{core_translate}}', array(
      'hash' => 'varchar(128)',
      'module' => 'varchar(32)',
      'category' => 'varchar(32)',
      'languageID' => 'varchar(2)',
      'translate' => 'text',
    ));
    $this->addPrimaryKey('pk_translate', '{{core_translate}}', 'hash,module,category,languageID');

    $this->createTable('{{core_translate_source}}', array(
      'hash' => 'varchar(128)',
      'module' => 'varchar(32)',
      'category' => 'varchar(32)',
      'source' => 'text',
    ));
    $this->addPrimaryKey('pk_translate_source', '{{core_translate_source}}', 'hash,module,category');

    $this->createTable('{{core_mail_template}}', array(
      'id' => 'varchar(36)',
      'module' => 'varchar(32)',
      'name' => 'varchar(255)',
    ));
    $this->addPrimaryKey('pk_core_mail_template', '{{core_mail_template}}', 'id');

    $this->createTable('{{core_mail_template_l10n}}', array(
      'templateID' => 'varchar(36)',
      'languageID' => 'varchar(2)',
      'isHtml' => 'boolean default 0',
      'subject' => 'varchar(1022)',
      'body' => 'text',
    ));
    $this->addPrimaryKey('pk_core_mail_template_l10n', '{{core_mail_template_l10n}}', 'templateID,languageID');

    $this->createTable('{{core_account_role}}', array(
      'id' => 'varchar(16)',
      'title' => 'varchar(255)',
      'system' => 'boolean default 0',
      'allowAll' => 'boolean default 0',
    ));
    $this->addPrimaryKey('pk_core_account_role', '{{core_account_role}}', 'id');

    $this->createTable('{{core_account_access}}', array(
      'accountRoleID' => 'varchar(16)',
      'accessRuleID' => 'varchar(128)',
      'permit' => 'boolean default 0',
    ));
    $this->addPrimaryKey('pk_core_account_access', '{{core_account_access}}', 'accountRoleID,accessRuleID');

    $this->createTable('{{core_history}}', array(
      'id' => 'varchar(36)',
      'className' => 'varchar(255)',
      'primaryKey' => 'varchar(255)',
      'eventID' => 'varchar(32)',
      'timestamp' => 'int',
      'userID' => 'varchar(36)',
      'ip' => 'varchar(15)',
      'agent' => 'varchar(255)',
    ));
    $this->addPrimaryKey('pk_core_history', '{{core_history}}', 'id');
    $this->createIndex('idx_core_history_itm', '{{core_history}}', 'className,primaryKey');

    $this->createTable('{{core_seo}}', array(
      'id' => 'varchar(36)',
      'className' => 'varchar(255)',
      'primaryKey' => 'varchar(255)',
      'languageID' => 'varchar(2)',
      'title' => 'varchar(1022)',
      'keywords' => 'varchar(4094)',
      'description' => 'text',
    ));
    $this->addPrimaryKey('pk_core_seo', '{{core_seo}}', 'id');
    $this->createIndex('idx_core_seo_itm', '{{core_seo}}', 'className,primaryKey,languageID');

    $this->createTable('{{core_carousel}}', array(
      'id' => 'varchar(36)',
      'siteID' => 'varchar(36)',
      'public' => 'boolean default 1',
    ));
    $this->addPrimaryKey('pk_carousel', '{{core_carousel}}', 'id');
    $this->createIndex('idx_carousel_site', '{{core_carousel}}', 'siteID');
    $this->createIndex('idx_carousel_public', '{{core_carousel}}', 'public');

    $this->createTable('{{core_carousel_l10n}}', array(
      'carouselID' => 'varchar(36)',
      'languageID' => 'varchar(2)',
      'title' => 'varchar(255)',
    ));
    $this->addPrimaryKey('pk_core_carousel_l10n', '{{core_carousel_l10n}}', 'carouselID,languageID');

    $this->createTable('{{core_carousel_image}}', array(
      'id' => 'varchar(36)',
      'carouselID' => 'varchar(36)',
      'imageID' => 'varchar(36)',
      'position' => 'int',
    ));
    $this->addPrimaryKey('pk_carousel_image', '{{core_carousel_image}}', 'id');

    $this->createTable('{{core_carousel_image_l10n}}', array(
      'imageID' => 'varchar(36)',
      'languageID' => 'varchar(2)',
      'title' => 'varchar(255)',
      'caption' => 'varchar(1022)',
      'pageID' => 'varchar(36)',
      'url' => 'varchar(4094)',
    ));
    $this->addPrimaryKey('pk_core_carousel_image_l10n', '{{core_carousel_image_l10n}}', 'imageID,languageID');

    $this->createTable('{{core_photo}}', array(
      'id' => 'varchar(36)',
      'siteID' => 'varchar(36)',
      'languageID' => 'varchar(2)',
      'public' => 'boolean default 1',
      'title' => 'varchar(255)',
    ));
    $this->addPrimaryKey('pk_core_photo', '{{core_photo}}', 'id');
    $this->createIndex('idx_core_photo_site', '{{core_photo}}', 'siteID');
    $this->createIndex('idx_core_photo_lang', '{{core_photo}}', 'languageID');
    $this->createIndex('idx_core_photo_public', '{{core_photo}}', 'public');

    $this->createTable('{{core_photo_image}}', array(
      'ownerID' => 'varchar(36)',
      'imageID' => 'varchar(36)',
      'label' => 'varchar(1022)',
      'sort' => 'int',
    ));
    $this->addPrimaryKey('pk_core_photo_image', '{{core_photo_image}}', 'ownerID,imageID');
    $this->createIndex('idx_core_photo_image_sort', '{{core_photo_image}}', 'sort');

    return true;
  }

  public function safeDown()
  {
    echo 'Error: you can not revert core database structure.' . PHP_EOL;
    return false;
  }
}
