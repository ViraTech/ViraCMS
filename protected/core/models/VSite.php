<?php
/**
 * ViraCMS Site Model
 *
 * @package vira.core.core
 * @subpackage vira.core.content
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 *
 * @property string $id primary key
 * @property string $title site title
 * @property string $host primary host name
 * @property string $domains domains list
 * @property boolean $redirect redirect to primary host
 * @property boolean $default default site
 * @property string $theme frontend theme
 * @property string $webroot webroot directory
 */
class VSite extends VActiveRecord
{
  const SHORT_TITLE_LENGTH = 30;

  protected $_originalTheme;

  /**
   * Initialize model
   */
  public function init()
  {
    parent::init();

    // attach administrative CRUD behaviours only when created inside the system CRUD controller
    if (is_a(Yii::app()->getController(), 'VSystemController')) {
      $this->attachBehaviors(array(
        'SystemLogBehavior' => array(
          'class' => 'VSystemLogBehavior',
          'createMessage' => 'Site [{id}] "{title}" has been created',
          'createParams' => array('{id}' => '$this->id', '{title}' => '$this->title'),
          'updateMessage' => 'Site [{id}] "{title}" has been updated',
          'updateParams' => array('{id}' => '$this->id', '{title}' => '$this->title'),
          'deleteMessage' => 'Site [{id}] "{title}" has been removed',
          'deleteParams' => array('{id}' => '$this->id', '{title}' => '$this->title'),
          'translateCategory' => 'admin.content.events',
        ),
        'HistoryBehavior' => array(
          'class' => 'VHistoryBehavior',
        ),
      ));
    }
  }

  /**
   * @param string $className
   * @return VSite
   */
  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }

  public function tableName()
  {
    return '{{core_site}}';
  }

  public function behaviors()
  {
    return CMap::mergeArray(
        array(
        'GuidBehavior' => array(
          'class' => 'VGuidBehavior',
        ),
        ), parent::behaviors()
    );
  }

  public function rules()
  {
    return array(
      array('id', 'unsafe', 'except' => 'create'),
      array('id', 'length', 'is' => 36),
      array('title', 'required'),
      array('title', 'length', 'max' => 255),
      array('host', 'length', 'max' => 1022, 'allowEmpty' => true),
      array('domains', 'length', 'max' => 65530),
      array('webroot', 'length', 'max' => 4094),
      array('theme', 'length', 'max' => 1022),
      array('default,redirect', 'boolean'),
      array('id,title,host,domains,default,redirect', 'safe', 'on' => 'search'),
    );
  }

  public function getShortTitle()
  {
    return mb_strlen($this->title, Yii::app()->charset) > self::SHORT_TITLE_LENGTH ?
      trim(mb_strcut($this->title, 0, self::SHORT_TITLE_LENGTH * 2, Yii::app()->charset)) . '...' :
      $this->title;
  }

  public function attributeLabels()
  {
    return array(
      'id' => Yii::t('common', 'ID'),
      'title' => Yii::t('admin.content.labels', 'Site Title'),
      'host' => Yii::t('admin.content.labels', 'Primary Host'),
      'domains' => Yii::t('admin.content.labels', 'Additional Domains'),
      'redirect' => Yii::t('admin.content.labels', 'Redirect From Additional Domains To Primary Host'),
      'theme' => Yii::t('admin.content.labels', 'Site Theme'),
      'webroot' => Yii::t('admin.content.labels', 'Root Directory'),
      'default' => Yii::t('admin.content.labels', 'Make This Site Default'),
    );
  }

  protected function afterFind()
  {
    parent::afterFind();
    $this->domains = implode("\n", explode(',', $this->domains));
    $this->_originalTheme = $this->theme;
  }

  protected function beforeValidate()
  {
    if (parent::beforeValidate()) {
      $this->domains = array_map('strtolower', array_map('trim', array_filter(explode("\n", $this->domains))));
      if (!empty($this->domains)) {
        foreach ($this->domains as $domain) {
          if (!preg_match('/([a-z]+\.)+[a-z]+/', $domain)) {
            $this->addError('domains', Yii::t('admin.content.errors', 'Incorrect domain name "{domain}"', array('{domain}' => $domain)));
            $this->domains = implode("\n", $this->domains);
            return false;
          }
        }
      }

      $this->domains = is_array($this->domains) ? implode(',', $this->domains) : '';

      return true;
    }

    return false;
  }

  protected function afterValidate()
  {
    parent::afterValidate();
    if (!$this->default && empty($this->host) && empty($this->domains)) {
      $this->addError('domains', Yii::t('admin.content.errors', 'You must enter at least one domain.'));
    }
  }

  protected function beforeSave()
  {
    if (parent::beforeSave()) {
      if ($this->default) {
        self::model()->updateAll(array('default' => '0'));
      }

      return true;
    }

    return false;
  }

  protected function afterSave()
  {
    if ($this->getScenario() != 'auto') {
      parent::afterSave();
      if ($this->isNewRecord) {
        $homepage = new VPage;
        $homepage->setAttributes(array(
          'siteID' => $this->id,
          'layoutID' => 'default',
          'class' => Yii::app()->collection->pageRenderer->getDefaultRenderer(),
          'cacheable' => false,
          'url' => '/',
          'redirectRoute' => '',
          'redirectParam' => '',
          'redirectValue' => '',
          'redirectUrl' => '',
          'parentID' => '',
          'homepage' => true,
          'visibility' => 0,
          'accessibility' => 0,
          'position' => 0,
          ), false);
        $homepage->save(false);
      }
      $this->clearCache();
    }
  }

  protected function beforeDelete()
  {
    if (parent::beforeDelete()) {
      if (self::model()->count() < 2) {
        $this->addError('id', Yii::t('admin.content.errors', 'You can not delete the last site.'));
        return false;
      }

      if ($this->default) {
        $this->addError('id', Yii::t('admin.content.errors', 'You can not delete default site.'));
        return false;
      }

      return true;
    }

    return false;
  }

  public function autoFilter()
  {
    if (Yii::app()->user->getAttribute('siteAccess') == 0) {
      $siteAccessList = Yii::app()->user->getModel()->getSiteAccessList();
      $criteria = new CDbCriteria();
      $criteria->addInCondition('t.id', $siteAccessList);
      $criteria->order = 't.title ASC';
      $this->getDbCriteria()->mergeWith($criteria);
    }

    return $this;
  }

  /**
   * Performs search over records
   * @return CActiveDataProvider
   */
  public function search()
  {
    $criteria = new CDbCriteria;
    $criteria->compare('t.host', $this->host, true);
    $criteria->compare('t.domains', $this->domains, true);
    $criteria->compare('t.default', $this->default);
    $criteria->compare('t.redirect', $this->redirect);
    $criteria->compare('t.title', $this->title, true);

    $this->addSiteCondition('id', $criteria);

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'pagination' => array(
        'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params['defaultPageSize']),
        'pageVar' => 'page',
      ),
      'sort' => array(
        'defaultOrder' => $this->quoteColumn('t.default') . ' DESC',
      ),
    ));
  }

  public function getName()
  {
    return $this->title;
  }

  protected function clearCache()
  {
    if (Yii::app()->hasComponent('cache')) {
      Yii::app()->cache->deleteTag('Vira.Site');
      if ($this->_originalTheme != $this->theme) {
        VCacheHelper::flushAppCache();
      }
    }
  }

  /**
   * Returns defaut site model
   * @return VSite
   */
  public static function findDefault()
  {
    $criteria = new CDbCriteria();
    $criteria->compare('t.default', '>0');

    return self::model()->find($criteria);
  }
}
