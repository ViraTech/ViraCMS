<?php
/**
 * ViraCMS Core Default Data
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class vira_core_core_000101_000002_default_data extends VDbMigration
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
      '{{core_language}}' => array(
        array('id' => 'en', 'active' => 1, 'locale' => 'en_US', 'title' => 'English', 'index' => 10),
        array('id' => 'ru', 'active' => 1, 'locale' => 'ru_RU', 'title' => 'Русский', 'index' => 20),
      ),
      '{{core_content_template}}' => array(
        array('id' => '0a44bdc1-8e18-4b05-ac12-cd151a4b50ca', 'title' => '50% | 25% | 25%', 'template' => '<div class="row-fluid"><div class="span6"></div><div class="span3"></div><div class="span3"></div></div>'),
        array('id' => '0d93465f-526c-407e-ae09-e0a8981f0c36', 'title' => '25% | 25% | 50%', 'template' => '<div class="row-fluid"><div class="span3"></div><div class="span3"></div><div class="span6"></div></div>'),
        array('id' => '161de0d2-ed78-4116-a54f-7efaaaf99f5e', 'title' => '25% | 50% | 25%', 'template' => '<div class="row-fluid"><div class="span3"></div><div class="span6"></div><div class="span3"></div></div>'),
        array('id' => '2df5ff69-ee13-4f57-9d34-b4a870f54cd8', 'title' => '50% | 50%', 'template' => '<div class="row-fluid"><div class="span6"></div><div class="span6"></div></div>'),
        array('id' => '39647a1f-b3c4-4b83-bd7c-41deb749c17d', 'title' => '25% | 33% | 42%', 'template' => '<div class="row-fluid"><div class="span3"></div><div class="span4"></div><div class="span5"></div></div>'),
        array('id' => '436d0761-273f-4813-b3c1-442a10f0cc4a', 'title' => '16% | 84%', 'template' => '<div class="row-fluid"><div class="span2"></div><div class="span10"></div></div>'),
        array('id' => '569fc5e9-f614-4e15-ad6b-28af40b4e08b', 'title' => '25% | 75%', 'template' => '<div class="row-fluid"><div class="span3"></div><div class="span9"></div></div>'),
        array('id' => '56f708da-ceec-4cd9-9637-96028ceea3b4', 'title' => '25% | 25% | 25% | 25%', 'template' => '<div class="row-fluid"><div class="span3"></div><div class="span3"></div><div class="span3"></div><div class="span3"></div></div>'),
        array('id' => '59fef7cb-2601-4852-b825-f48bb1001bfe', 'title' => '66% | 33%', 'template' => '<div class="row-fluid"><div class="span8"></div><div class="span4"></div></div>'),
        array('id' => '71ecfc8c-547a-4fba-8c4f-704884425e25', 'title' => '16% | 16% | 16% | 16% | 16% | 16%', 'template' => '<div class="row-fluid"><div class="span2"></div><div class="span2"></div><div class="span2"></div><div class="span2"></div><div class="span2"></div><div class="span2"></div></div>'),
        array('id' => '9717cb9c-ceeb-4e71-a1fd-9d20b29948fa', 'title' => '33% | 33% | 33%', 'template' => '<div class="row-fluid"><div class="span4"></div><div class="span4"></div><div class="span4"></div></div>'),
        array('id' => 'aa4c219a-d3e5-4fb2-8f1a-6159eadc3de0', 'title' => '100%', 'template' => '<div class="row-fluid"><div class="span12"></div></div>'),
        array('id' => 'bab4f059-eab1-4fae-942e-2398dd838458', 'title' => '33% | 66%', 'template' => '<div class="row-fluid"><div class="span4"></div><div class="span8"></div></div>'),
        array('id' => 'c60b6cb4-4831-4e11-aa31-d0634c2392df', 'title' => '84% | 16%', 'template' => '<div class="row-fluid"><div class="span10"></div><div class="span2"></div></div>'),
        array('id' => 'd2e531f5-7a21-4cc5-94ec-76cb66b647b1', 'title' => '42% | 33% | 25%', 'template' => '<div class="row-fluid"><div class="span5"></div><div class="span4"></div><div class="span3"></div></div>'),
        array('id' => 'def9a216-a85b-4f34-9eb8-1dda84bd0d5b', 'title' => '75% | 25%', 'template' => '<div class="row-fluid"><div class="span9"></div><div class="span3"></div></div>'),
      ),
      '{{core_mail_template}}' => array(
        array('id' => 'ad7fcfe6-ab7d-45c4-b228-fd4d732ef683', 'module' => 'registry', 'name' => 'newPassword'),
        array('id' => '81f3f70e-4113-439b-bb22-6ba8b7fc4129', 'module' => 'registry', 'name' => 'restorePassword'),
      ),
      '{{core_mail_template_l10n}}' => array(
        array('templateID' => 'ad7fcfe6-ab7d-45c4-b228-fd4d732ef683', 'languageID' => 'en', 'isHtml' => '0', 'subject' => 'New credentials for access to site {siteName}', 'body' => 'Dear {userName}!

    This is confirmation letter that you\'re just changed your password for site {siteName}.

    New login credentials:

    Login URL: {url}
    Username: {username}
    Password: {password}

    Please login as soon as possible and change assigned password to new one.

    In case that you does not made any requests please contact site administrator
    by email {siteAdminEmail}

    With best wishes,
    {siteName} {siteUrl}
    '),
        array('templateID' => 'ad7fcfe6-ab7d-45c4-b228-fd4d732ef683', 'languageID' => 'ru', 'isHtml' => '0', 'subject' => 'Новые учетные данные для доступа к сайту {siteName}', 'body' => 'Уважаемый {userName}!

    Это письмо, подтверждающее, что вы восстановили пароль на сайте {siteName}.

    Ваши новые учетные данные:

    URL для входа: {url}
    Имя пользователя: {username}
    Пароль: {password}

    В целях безопасности, пожалуйста, авторизуйтесь на сайте так быстро, как только возможно и измените Ваш пароль.

    В случае, если Вы не производили восстановления пароля, пожалуста, свяжитесь с администратором сайта
    по электронной почте {siteAdminEmail}

    С наилучшими пожеланиями,
    {siteName} {siteUrl}
    '),
        array('templateID' => '81f3f70e-4113-439b-bb22-6ba8b7fc4129', 'languageID' => 'en', 'isHtml' => '0', 'subject' => 'Restore access to site {siteName}', 'body' => 'Dear {userName}!

    You\'re made a request to restore access for site {siteName}.
    Please follow this link to complete password retrieval:

    {url}

    This link can be accessed only once and it\'s expiring at {activeBefore}.

    If you are don\'t know what it is about please ignore this letter.

    In case that you does not made any requests please contact site administrator
    by email {siteAdminEmail}

    With best wishes,
    {siteName} {siteUrl}
    '),
        array('templateID' => '81f3f70e-4113-439b-bb22-6ba8b7fc4129', 'languageID' => 'ru', 'isHtml' => '0', 'subject' => 'Восстановление доступа к сайту {siteName}', 'body' => 'Уважаемый {userName}!

    Вы сделали запрос на восстановление доступа к сайту {siteName}.
    Пожалуйста, пройдите по следующей ссылке, или скопируйте ее в адресную строку Вашего браузера:

    {url}

    Ссылка может быть открыта только один раз и срок ее действия истекает {activeBefore}.

    Если Вы не знаете о чем идет речь, просто игнорируйте это письмо.

    В случае, если Вы не делали никаких запросов на восстановление, просим обратиться к администратору сайта
    по электронной почте {siteAdminEmail}

    С наилучшими пожеланиями,
    {siteName} {siteUrl}
    '),
      ),
      '{{core_account_role}}' => array(
        array('id' => 'superadmin', 'title' => 'Суперадминистратор', 'system' => '1', 'allowAll' => '1'),
      ),
    );
  }
}
