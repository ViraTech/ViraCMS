<?php
/**
 * ViraCMS Item SEO Options Widget
 *
 * @package vira.core.core
 * @subpackage vira.core.widgets
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VSeoWidget extends CWidget
{
  const MODE_SINGLE_LANGUAGE = 1;
  const MODE_MULTI_LANGUAGE = 2;

  public $mode = self::MODE_MULTI_LANGUAGE;
  public $model;
  public $form;
  public $uneditable = false;

  public function run()
  {
    if (empty($this->model) || empty($this->form)) {
      return;
    }

    if ($this->mode == self::MODE_MULTI_LANGUAGE) {
      $this->render('seo', array(
        'model' => $this->model,
        'form' => $this->form,
        'languages' => VLanguageHelper::getLanguages(),
        'currentLanguageID' => Yii::app()->getLanguage(),
      ));
    }
    else {
      $this->render('seo-form', array(
        'model' => $this->model,
        'form' => $this->form,
      ));
    }
  }
}
