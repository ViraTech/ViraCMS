<?php
/**
 * ViraCMS Static Page Editor Save Contents Action Handler
 *
 * @package vira.core.core
 * @subpackage vira.core.editor
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class ViraEditorSaveAction extends CAction
{
  protected $page;
  protected $layout;
  protected $system;
  protected $languageID;

  /**
   * Executes the action
   * @throws CHttpException
   */
  public function run()
  {
    $r = Yii::app()->request;

    $siteID = $r->getParam('siteID');
    $pageID = $r->getParam('pageID');
    $layoutID = $r->getParam('layoutID');
    $systemID = $r->getParam('systemID');
    $this->languageID = $r->getParam('languageID');

    $data = $r->getParam('data', array());

    if ($pageID) {
      $this->page = VPage::model()->findByPk($pageID);
    }

    if ($layoutID) {
      $this->layout = VSiteLayout::model()->findByPk(array(
        'id'     => $layoutID,
        'siteID' => $siteID,
      ));
    }

    if ($systemID) {
      $this->system = VSystemPage::model()->with(array('l10n'))->findByPk($systemID);
    }

    if (empty($this->page) && empty($this->layout) && empty($this->system)) {
      throw new CHttpException(400, 'Mandatory parameter omitted');
    }

    // check for content stub
    if ($this->layout) {
      $ok = false;
      foreach ($data as $area) {
        if ($area[ 'type' ] == VPageAreaTypeCollection::PRIMARY) {
          foreach ($area[ 'rows' ] as $contents) {
            if (stripos($contents[ 'template' ], '###VIRA_CONTENT_STUB###') !== false) {
              $ok = true;
              break;
            }
          }
        }
        if ($ok) {
          break;
        }
      }
      if (!$ok) {
        throw new CHttpException(400, Yii::t('admin.content.errors', 'Content stub block must be in primary content area.'));
      }
    }

    $rows = array();
    if ($this->page) {
      $rows = VPageRow::model()->findAllByAttributes(array(
        'pageID'     => $this->page->id,
        'languageID' => $this->languageID,
      ));
    }
    elseif ($this->system) {
      
    }
    elseif ($this->layout) {
      $rows = VPageRow::model()->findAllByAttributes(array(
        'siteID'     => $this->layout->siteID,
        'layoutID'   => $this->layout->id,
        'languageID' => $this->languageID,
      ));
    }

    foreach ($rows as $row) {
      $row->delete();
    }

    if ($systemID) {
      foreach ($data as $area) {
        foreach ($area[ 'rows' ] as $rows) {
          foreach ($rows[ 'blocks' ] as $block) {
            if ($block[ 'orig' ] == 'system') {
              $l10n = $this->system->getL10nModel($this->languageID, false);
              $l10n->content = $block[ 'content' ];
              $l10n->save();
              break;
            }
          }
        }
      }
    }
    else {
      foreach ($data as $area) {
        foreach ($area[ 'rows' ] as $i => $contents) {
          $row = new VPageRow();
          $row->siteID = $siteID;
          $row->pageID = $this->page ? $this->page->id : '';
          $row->layoutID = $this->layout ? $this->layout->id : '';
          $row->languageID = $this->languageID;
          $row->pageAreaID = $area[ 'area' ];
          $row->row = $i + 1;
          $row->template = $contents[ 'template' ];
          $map = array();
          if (isset($contents[ 'blocks' ]) && is_array($contents[ 'blocks' ])) {
            foreach ($contents[ 'blocks' ] as $content) {
              if ($content[ 'type' ] != 'stub') {
                $block = new VPageBlock();
                $block->siteID = $siteID;
                $block->pageID = $this->page ? $this->page->id : '';
                $block->layoutID = $this->layout ? $this->layout->id : '';
                $block->pageAreaID = $area[ 'area' ];
                $block->languageID = $this->languageID;
                switch ($content[ 'type' ]) {
                  case 'widget':
                    $block->class = VBlockRendererCollection::WIDGET_RENDERER;
                    $block->content = serialize(array(
                      'class'  => $content[ 'widget' ],
                      'params' => empty($content[ 'config' ]) ? array() : $content[ 'config' ],
                    ));
                    break;
                  case 'block':
                  default:
                    $block->class = VBlockRendererCollection::STATIC_RENDERER;
                    $block->content = $content[ 'content' ];
                }
                $block->save(false);
                $map[ '###VIRA_BLK_' . $content[ 'bc' ] . '_###' ] = '###VIRA#' . $block->id . '###';
              }
            }
          }
          $row->template = strtr($row->template, $map);
          $row->save(false);
        }
      }
    }

    if ($this->page) {
      foreach ($this->page->l10n as $l10n) {
        $l10n->save();
      }
    }

    if (Yii::app()->hasComponent('cache')) {
      Yii::app()->cache->deleteTag('Vira.Pages');
    }

    Yii::app()->user->setFlash('success', $this->getSuccessMessage());

    echo CJSON::encode(array(
      'ok' => true,
    ));
  }

  /**
   * Returns the success message text
   * @return string
   */
  protected function getSuccessMessage()
  {
    if ($this->page) {
      return Yii::t('admin.content.messages', 'Page "{id}" has been updated successfully', array(
          '{id}' => $this->page->title,
      ));
    }

    if ($this->system) {
      return Yii::t('admin.content.messages', 'System page "{id}" has been updated successfully.', array(
          '{id}' => $this->system->id,
      ));
    }

    if ($this->layout) {
      return Yii::t('admin.content.messages', 'Layout "{id}" has been updated successfully.', array(
          '{id}' => $this->layout->id,
      ));
    }
  }
}
