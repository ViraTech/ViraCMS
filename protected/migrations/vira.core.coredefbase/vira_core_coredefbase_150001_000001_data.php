<?php
/**
 * ViraCMS Base Site Default Data
 *
 * @package vira.core.coredefbase
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class vira_core_coredefbase_150001_000001_data extends VDbMigration
{
  public $version = '1.0.0';

  public function safeUp()
  {
    $this->upload($this->getData());

    return true;
  }

  public function safeDown()
  {
    echo 'Error: you can not delete default database contents.' . PHP_EOL;
    return false;
  }

  public function getData()
  {
    return array(
      '{{core_site}}' => array(
        array('id' => 'c0b800b1-2b03-43c8-8817-798e8a4e6dd3', 'title' => 'Новый сайт ViraCMS', 'host' => '', 'domains' => '', 'redirect' => 0, 'default' => 1, 'theme' => 'dark'),
      ),
      '{{core_site_layout}}' => array(
        array('id' => 'default', 'siteID' => 'c0b800b1-2b03-43c8-8817-798e8a4e6dd3', 'title' => 'Стандартная страница', 'default' => '1', 'linkColor' => '', 'linkHoverColor' => '', 'linkVisitedColor' => '', 'bodyTextColor' => '', 'bodyBackgroundColor' => '', 'bodyBackgroundImage' => '', 'styleOverride' => '', 'metaTags' => ''),
      ),
      '{{core_page_area}}' => array(
        array('id' => '10d3e2fd-ee1e-4d5d-aaa4-e4460d19bb67', 'title' => 'Шапка страницы', 'tag' => 'header', 'classes' => '', 'type' => '1', 'container' => 'container', 'position' => '10'),
        array('id' => 'a859884f-98f7-46dd-a9a7-b0fcdf9bda06', 'title' => 'Контент страницы', 'tag' => 'section', 'classes' => '', 'type' => '2', 'container' => 'container', 'position' => '20'),
        array('id' => 'c7325f33-d516-45bb-b3f4-1890b8b05d23', 'title' => 'Подвал страницы', 'tag' => 'footer', 'classes' => '', 'type' => '1', 'container' => 'container', 'position' => '30'),
      ),
      '{{core_layout_area}}' => array(
        array('pageAreaID' => '10d3e2fd-ee1e-4d5d-aaa4-e4460d19bb67', 'siteID' => 'c0b800b1-2b03-43c8-8817-798e8a4e6dd3', 'layoutID' => 'default'),
        array('pageAreaID' => 'a859884f-98f7-46dd-a9a7-b0fcdf9bda06', 'siteID' => 'c0b800b1-2b03-43c8-8817-798e8a4e6dd3', 'layoutID' => 'default'),
        array('pageAreaID' => 'c7325f33-d516-45bb-b3f4-1890b8b05d23', 'siteID' => 'c0b800b1-2b03-43c8-8817-798e8a4e6dd3', 'layoutID' => 'default'),
      ),
      '{{core_page}}' => array(
        array('id' => '103bc040-6583-4495-9179-3adfc80ab92d', 'siteID' => 'c0b800b1-2b03-43c8-8817-798e8a4e6dd3', 'layoutID' => 'index', 'class' => 'VStaticPageRenderer', 'cacheable' => '1', 'url' => '/', 'redirectUrl' => '', 'parentID' => '', 'homepage' => '1', 'visibility' => '0', 'accessibility' => '0', 'position' => '0'),
      ),
      '{{core_page_l10n}}' => array(
        array('pageID' => '103bc040-6583-4495-9179-3adfc80ab92d', 'languageID' => 'en', 'name' => 'Home'),
        array('pageID' => '103bc040-6583-4495-9179-3adfc80ab92d', 'languageID' => 'ru', 'name' => 'Главная'),
      ),
      '{{core_page_row}}' => array(
        array('siteID' => 'c0b800b1-2b03-43c8-8817-798e8a4e6dd3', 'pageID' => '103bc040-6583-4495-9179-3adfc80ab92d', 'layoutID' => '', 'languageID' => 'ru', 'pageAreaID' => 'a859884f-98f7-46dd-a9a7-b0fcdf9bda06', 'row' => '1', 'template' => '<div class="row-fluid"><div class="span12">###VIRA#4f9c3eeb-ec2b-4295-a7b2-f9e03a1309ec######VIRA#8711211f-0468-4326-b7a4-de2a70a30408###</div></div>'),
        array('siteID' => 'c0b800b1-2b03-43c8-8817-798e8a4e6dd3', 'pageID' => '103bc040-6583-4495-9179-3adfc80ab92d', 'layoutID' => '', 'languageID' => 'ru', 'pageAreaID' => 'a859884f-98f7-46dd-a9a7-b0fcdf9bda06', 'row' => '2', 'template' => '<div class="row-fluid"><div class="span3">###VIRA#48c7155f-e8cd-4d10-ac58-fe6a039bd3d3###</div><div class="span3">###VIRA#5854c9f5-740b-4366-bc24-d133d3d5db2b###</div><div class="span3">###VIRA#39e0df15-9350-42f4-afd8-ff1a13cb43a8###</div><div class="span3">###VIRA#beacc4bc-f544-43ec-9820-ee9e91863d92###</div></div>'),
        array('siteID' => 'c0b800b1-2b03-43c8-8817-798e8a4e6dd3', 'pageID' => '', 'layoutID' => 'default', 'languageID' => 'en', 'pageAreaID' => '10d3e2fd-ee1e-4d5d-aaa4-e4460d19bb67', 'row' => '1', 'template' => '<div class="row-fluid"><div class="span12">###VIRA#bcc168d4-cf5d-4d24-a57d-2692c9c6ee8d###</div></div>'),
        array('siteID' => 'c0b800b1-2b03-43c8-8817-798e8a4e6dd3', 'pageID' => '', 'layoutID' => 'default', 'languageID' => 'en', 'pageAreaID' => 'a859884f-98f7-46dd-a9a7-b0fcdf9bda06', 'row' => '1', 'template' => '<div class="row-fluid"><div class="span12">###VIRA_CONTENT_STUB###</div></div>'),
        array('siteID' => 'c0b800b1-2b03-43c8-8817-798e8a4e6dd3', 'pageID' => '', 'layoutID' => 'default', 'languageID' => 'en', 'pageAreaID' => 'c7325f33-d516-45bb-b3f4-1890b8b05d23', 'row' => '1', 'template' => '<div class="row-fluid"><div class="span6"></div><div class="span3"></div><div class="span3">###VIRA#c0a2bbad-fec8-4f56-938e-ce378075aec2###</div></div>'),
        array('siteID' => 'c0b800b1-2b03-43c8-8817-798e8a4e6dd3', 'pageID' => '', 'layoutID' => 'default', 'languageID' => 'ru', 'pageAreaID' => '10d3e2fd-ee1e-4d5d-aaa4-e4460d19bb67', 'row' => '1', 'template' => '<div class="row-fluid"><div class="span12">###VIRA#bb92cede-a1ca-418f-845c-713f12da16ff###</div></div>'),
        array('siteID' => 'c0b800b1-2b03-43c8-8817-798e8a4e6dd3', 'pageID' => '', 'layoutID' => 'default', 'languageID' => 'ru', 'pageAreaID' => 'a859884f-98f7-46dd-a9a7-b0fcdf9bda06', 'row' => '1', 'template' => '<div class="row-fluid"><div class="span12">###VIRA_CONTENT_STUB###</div></div>'),
        array('siteID' => 'c0b800b1-2b03-43c8-8817-798e8a4e6dd3', 'pageID' => '', 'layoutID' => 'default', 'languageID' => 'ru', 'pageAreaID' => 'c7325f33-d516-45bb-b3f4-1890b8b05d23', 'row' => '1', 'template' => '<div class="row-fluid"><div class="span6"></div><div class="span6"></div></div>'),
        array('siteID' => 'c0b800b1-2b03-43c8-8817-798e8a4e6dd3', 'pageID' => '', 'layoutID' => 'default', 'languageID' => 'ru', 'pageAreaID' => 'c7325f33-d516-45bb-b3f4-1890b8b05d23', 'row' => '2', 'template' => '<div class="row-fluid"><div class="span6">###VIRA#d362557d-86ff-47e8-9aa5-ea65ed7dd58d######VIRA#839edd88-e851-4fda-ae7e-14df3d405888###</div><div class="span3">###VIRA#66bf86ef-42cc-4fe5-a5ae-cad2af1bf044######VIRA#43c30d7f-164d-4cd2-9113-9e3b1365b812###</div><div class="span3">###VIRA#83ff9ac3-43c2-42e1-bece-dba0530451c3######VIRA#ff87a534-eac8-44ad-9d6d-58fa0248b6da###</div></div>'),
      ),
      '{{core_page_block}}' => array(
        array('id' => '39e0df15-9350-42f4-afd8-ff1a13cb43a8', 'siteID' => 'c0b800b1-2b03-43c8-8817-798e8a4e6dd3', 'pageID' => '103bc040-6583-4495-9179-3adfc80ab92d', 'layoutID' => '', 'pageAreaID' => 'a859884f-98f7-46dd-a9a7-b0fcdf9bda06', 'languageID' => 'ru', 'class' => 'VStaticRenderer', 'content' => '<ul><li>Загрузите фотографии или видео</li></ul>'),
        array('id' => '43c30d7f-164d-4cd2-9113-9e3b1365b812', 'siteID' => 'c0b800b1-2b03-43c8-8817-798e8a4e6dd3', 'pageID' => '', 'layoutID' => 'default', 'pageAreaID' => 'c7325f33-d516-45bb-b3f4-1890b8b05d23', 'languageID' => 'ru', 'class' => 'VStaticRenderer', 'content' => '<p><span style="font-size:10px">С помощью этой формы можно что-нибудь поискать</span></p>'),
        array('id' => '48c7155f-e8cd-4d10-ac58-fe6a039bd3d3', 'siteID' => 'c0b800b1-2b03-43c8-8817-798e8a4e6dd3', 'pageID' => '103bc040-6583-4495-9179-3adfc80ab92d', 'layoutID' => '', 'pageAreaID' => 'a859884f-98f7-46dd-a9a7-b0fcdf9bda06', 'languageID' => 'ru', 'class' => 'VStaticRenderer', 'content' => '<ul><li>Добавьте столько страниц, сколько необходимо</li></ul>'),
        array('id' => '4f9c3eeb-ec2b-4295-a7b2-f9e03a1309ec', 'siteID' => 'c0b800b1-2b03-43c8-8817-798e8a4e6dd3', 'pageID' => '103bc040-6583-4495-9179-3adfc80ab92d', 'layoutID' => '', 'pageAreaID' => 'a859884f-98f7-46dd-a9a7-b0fcdf9bda06', 'languageID' => 'ru', 'class' => 'VStaticRenderer', 'content' => '<p style="text-align:center">&nbsp;</p><p style="text-align:center"><a class="btn btn-primary" href="/admin/">Перейти в панель управления</a></p><p style="text-align:center">&nbsp;</p><p style="text-align:center"><strong>Заполните ваш новый сайт информацией в несколько простых шагов:</strong></p>'),
        array('id' => '5854c9f5-740b-4366-bc24-d133d3d5db2b', 'siteID' => 'c0b800b1-2b03-43c8-8817-798e8a4e6dd3', 'pageID' => '103bc040-6583-4495-9179-3adfc80ab92d', 'layoutID' => '', 'pageAreaID' => 'a859884f-98f7-46dd-a9a7-b0fcdf9bda06', 'languageID' => 'ru', 'class' => 'VStaticRenderer', 'content' => '<ul><li>Напишите приветственные фразы, опишите вашу компанию или разместите информацию о себе.</li></ul>'),
        array('id' => '66bf86ef-42cc-4fe5-a5ae-cad2af1bf044', 'siteID' => 'c0b800b1-2b03-43c8-8817-798e8a4e6dd3', 'pageID' => '', 'layoutID' => 'default', 'pageAreaID' => 'c7325f33-d516-45bb-b3f4-1890b8b05d23', 'languageID' => 'ru', 'class' => 'VWidgetRenderer', 'content' => 'a:2:{s:5:"class";s:17:"VSiteSearchWidget";s:6:"params";a:0:{}}'),
        array('id' => '839edd88-e851-4fda-ae7e-14df3d405888', 'siteID' => 'c0b800b1-2b03-43c8-8817-798e8a4e6dd3', 'pageID' => '', 'layoutID' => 'default', 'pageAreaID' => 'c7325f33-d516-45bb-b3f4-1890b8b05d23', 'languageID' => 'ru', 'class' => 'VStaticRenderer', 'content' => '<p><span style="font-size:10px">Здесь можно разместить краткое описание того, зачем нужен этот сайт</span></p>'),
        array('id' => '83ff9ac3-43c2-42e1-bece-dba0530451c3', 'siteID' => 'c0b800b1-2b03-43c8-8817-798e8a4e6dd3', 'pageID' => '', 'layoutID' => 'default', 'pageAreaID' => 'c7325f33-d516-45bb-b3f4-1890b8b05d23', 'languageID' => 'ru', 'class' => 'VWidgetRenderer', 'content' => 'a:2:{s:5:"class";s:23:"VLanguageSelectorWidget";s:6:"params";a:1:{s:5:"align";s:5:"right";}}'),
        array('id' => '8711211f-0468-4326-b7a4-de2a70a30408', 'siteID' => 'c0b800b1-2b03-43c8-8817-798e8a4e6dd3', 'pageID' => '103bc040-6583-4495-9179-3adfc80ab92d', 'layoutID' => '', 'pageAreaID' => 'a859884f-98f7-46dd-a9a7-b0fcdf9bda06', 'languageID' => 'ru', 'class' => 'VStaticRenderer', 'content' => '<hr>'),
        array('id' => 'bb92cede-a1ca-418f-845c-713f12da16ff', 'siteID' => 'c0b800b1-2b03-43c8-8817-798e8a4e6dd3', 'pageID' => '', 'layoutID' => 'default', 'pageAreaID' => '10d3e2fd-ee1e-4d5d-aaa4-e4460d19bb67', 'languageID' => 'ru', 'class' => 'VStaticRenderer', 'content' => '<p style="text-align:center"><a href="/"><img alt="" src="/files/8e/8b/8e8b86405ca2ae537b2b9592116bc245/vira_cms_base_revision_logo.png"></a></p><p style="text-align:center">Ваш новый сайт под управлением <strong>ViraCMS</strong> готов к работе!</p>'),
        array('id' => 'bcc168d4-cf5d-4d24-a57d-2692c9c6ee8d', 'siteID' => 'c0b800b1-2b03-43c8-8817-798e8a4e6dd3', 'pageID' => '', 'layoutID' => 'default', 'pageAreaID' => '10d3e2fd-ee1e-4d5d-aaa4-e4460d19bb67', 'languageID' => 'en', 'class' => 'VStaticRenderer', 'content' => '<p style="text-align:center"><img alt="" src="/files/8e/8b/8e8b86405ca2ae537b2b9592116bc245/vira_cms_base_revision_logo.png"></p><p style="text-align:center">This is test page in English language</p>'),
        array('id' => 'beacc4bc-f544-43ec-9820-ee9e91863d92', 'siteID' => 'c0b800b1-2b03-43c8-8817-798e8a4e6dd3', 'pageID' => '103bc040-6583-4495-9179-3adfc80ab92d', 'layoutID' => '', 'pageAreaID' => 'a859884f-98f7-46dd-a9a7-b0fcdf9bda06', 'languageID' => 'ru', 'class' => 'VStaticRenderer', 'content' => '<ul><li>При необходимости сделайте аналогичные страницы на других языках</li></ul>'),
        array('id' => 'c0a2bbad-fec8-4f56-938e-ce378075aec2', 'siteID' => 'c0b800b1-2b03-43c8-8817-798e8a4e6dd3', 'pageID' => '', 'layoutID' => 'default', 'pageAreaID' => 'c7325f33-d516-45bb-b3f4-1890b8b05d23', 'languageID' => 'en', 'class' => 'VWidgetRenderer', 'content' => 'a:2:{s:5:"class";s:23:"VLanguageSelectorWidget";s:6:"params";a:1:{s:5:"align";s:5:"right";}}'),
        array('id' => 'd362557d-86ff-47e8-9aa5-ea65ed7dd58d', 'siteID' => 'c0b800b1-2b03-43c8-8817-798e8a4e6dd3', 'pageID' => '', 'layoutID' => 'default', 'pageAreaID' => 'c7325f33-d516-45bb-b3f4-1890b8b05d23', 'languageID' => 'ru', 'class' => 'VStaticRenderer', 'content' => '<p>© Укажите наименование вашей компании здесь</p>'),
        array('id' => 'ff87a534-eac8-44ad-9d6d-58fa0248b6da', 'siteID' => 'c0b800b1-2b03-43c8-8817-798e8a4e6dd3', 'pageID' => '', 'layoutID' => 'default', 'pageAreaID' => 'c7325f33-d516-45bb-b3f4-1890b8b05d23', 'languageID' => 'ru', 'class' => 'VStaticRenderer', 'content' => '<p style="text-align:right"><span style="font-size:10px">С помощью этих кнопок выбирается язык сайта</span></p>'),
      ),
    );
  }
}
