<?php
/**
 * ViraCMS Page Area Model
 *
 * @package vira.core.core
 * @subpackage vira.core.content
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 *
 * @property string $id the primary key
 * @property string $title the page area title
 * @property string $tag the HTML tag
 * @property string $classes the tag classes
 * @property boolean $type the area type (see TYPE_ constants)
 * @property string $container the container type
 * @property integer $position the area position on the page
 */
class VPageArea extends VActiveRecord
{
  private $_layouts;

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
          'createMessage' => 'Content area [{id}] "{title}" has been created',
          'createParams' => array('{id}' => '$this->id', '{title}' => '$this->title'),
          'updateMessage' => 'Content area [{id}] "{title}" has been updated',
          'updateParams' => array('{id}' => '$this->id', '{title}' => '$this->title'),
          'deleteMessage' => 'Content area [{id}] "{title}" has been removed',
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
   * @return VPageArea
   */
  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }

  public function tableName()
  {
    return '{{core_page_area}}';
  }

  public function rules()
  {
    return array(
      array('id', 'unsafe', 'except' => 'create'),
      array('id', 'length', 'is' => 36),
      array('type,container,title,tag,position', 'required'),
      array('type,position', 'numerical', 'integerOnly' => true),
      array('classes', 'length', 'max' => 1022),
      array('tag,container', 'length', 'max' => 64),
      array('title', 'length', 'max' => 255),
      array('position', 'unique'),
      array('title,type,position', 'safe', 'on' => 'search'),
    );
  }

  public function relations()
  {
    return array(
      'blockQty' => array(self::STAT, 'VPageBlock', 'pageAreaID'),
      'layouts' => array(self::MANY_MANY, 'VSiteLayout', '{{core_layout_area}}(pageAreaID,layoutID)', 'order' => 'layouts.siteID ASC', 'condition' => 'layouts.siteID=layouts_layouts.siteID', 'with' => 'site'),
      'layoutQty' => array(self::STAT, 'VLayoutArea', 'pageAreaID'),
    );
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

  public function attributeLabels()
  {
    return array(
      'id' => Yii::t('common', 'ID'),
      'title' => Yii::t('admin.content.labels', 'Title'),
      'type' => Yii::t('admin.content.labels', 'Content Area Type'),
      'tag' => Yii::t('admin.content.labels', 'HTML Tag'),
      'classes' => Yii::t('admin.content.labels', 'Container CSS Classes'),
      'container' => Yii::t('admin.content.labels', 'Container Type'),
      'position' => Yii::t('admin.content.labels', 'Position'),
    );
  }

  protected function beforeValidate()
  {
    if (parent::beforeValidate()) {
      $layouts = $this->getLayouts();
      if ($this->isNewRecord && $this->type == VPageAreaTypeCollection::PRIMARY && $layouts != array()) {
        $criteria = new CDbCriteria();
        foreach ($layouts as $siteID => $siteLayouts) {
          foreach ($siteLayouts as $layoutID => $title) {
            $criteria = new CDbCriteria;
            $criteria->with = array(
              'site',
              'layout',
              'pageArea',
            );
            $criteria->compare('pageArea.type', $this->type);
            $criteria->compare('layout.id', $layoutID);
            $criteria->compare('site.id', $siteID);
            $layoutArea = VLayoutArea::model()->findAll($criteria);
            if (count($layoutArea)) {
              foreach ($layoutArea as $area) {
                $this->addError('type', Yii::t('admin.content.errors', 'Site "{site}" layout "{layout}" already has primary content area. Only one primary content area per layout is allowed.', array(
                    '{site}' => $area->site->title,
                    '{layout}' => $area->layout->title,
                )));
              }
            }
          }
        }
      }

      return true;
    }

    return false;
  }

  protected function beforeDelete()
  {
    if (parent::beforeDelete()) {
      if ($this->type == VPageAreaTypeCollection::PRIMARY && $this->layoutQty > 0) {
        $this->addError('id', Yii::t('admin.content.errors', 'Can not delete primary content area while it is have any layouts!'));
        return false;
      }

      if ($this->blockQty > 0) {
        $this->addError('id', Yii::t('admin.content.errors', 'Can not delete this content area while it contain {n} block!|Can not delete this content area while it contain {n} blocks!', array($this->blockQty)));
        return false;
      }

      return true;
    }

    return false;
  }

  protected function afterDelete()
  {
    $this->removeLayouts();
    parent::afterDelete();
  }

  public function setLayouts($layouts)
  {
    $this->_layouts = array();
    foreach ($layouts as $siteID => $siteLayouts) {
      foreach ($siteLayouts as $layoutID => $title) {
        $this->_layouts[$siteID][$layoutID] = $title;
      }
    }
  }

  public function getLayouts()
  {
    if ($this->_layouts === null) {
      $this->_layouts = CHtml::listData($this->layouts, 'id', 'title', 'siteID');
    }

    return $this->_layouts;
  }

  public function removeLayouts()
  {
    VLayoutArea::model()->deleteAllByAttributes(array('pageAreaID' => $this->id));
  }

  public function addLayouts()
  {
    foreach ($this->_layouts as $siteID => $siteLayouts) {
      foreach ($siteLayouts as $layoutID => $title) {
        $layout = new VLayoutArea();
        $layout->pageAreaID = $this->id;
        $layout->siteID = $siteID;
        $layout->layoutID = $layoutID;
        $layout->save();
      }
    }
  }

  /**
   * Performs search over records
   * @return CActiveDataProvider
   */
  public function search()
  {
    $criteria = new CDbCriteria();
    $criteria->with = array(
      'layouts',
    );
    $criteria->compare('t.id', $this->id);
    $criteria->compare('t.title', $this->title, true);
    $criteria->compare('t.type', $this->type);
    $criteria->compare('t.position', $this->position);

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'pagination' => array(
        'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params['defaultPageSize']),
        'pageVar' => 'page',
      ),
      'sort' => array(
        'defaultOrder' => $this->quoteColumn('t.position') . ' ASC',
      ),
    ));
  }
}
