<?php
/**
 * ViraCMS Authentication Log Viewer Controller
 *
 * @package vira.core.core
 * @subpackage vira.core.registry
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class AuthController extends VSystemLogController
{
  protected $accessRules = array(
    '*' => array('registryAuthLog'),
  );

  protected function prepareDownloadFile()
  {
    $userNameCache = array();
    $sites = CHtml::listData(VSite::model()->findAll(), 'id', 'title');
    $model = $this->getPlainModel();
    $tmpFile = tmpfile();

    $query = Yii::app()->db->createCommand()->select('*')->from($model->tableName())->query();
    foreach ($query as $row) {
      if (!isset($userNameCache[$row['authorType']][$row['authorID']])) {
        $name = null;

        switch ($row['authorType']) {
          case VAccountTypeCollection::ADMINISTRATOR:
            if (($subject = VSiteAdmin::model()->findByPk($row['authorID'])) !== null) {
              $name = Yii::app()->collection->accountType->itemAt(VAccountTypeCollection::ADMINISTRATOR) . ': [' . $subject->id . '] ' . $subject->username;
            }
            break;
        }

        if ($name !== null) {
          $userNameCache[$row['authorType']][$row['authorID']] = $name;
        }
      }

      $params = @unserialize($row['params']);

      fputcsv($tmpFile, array(
        $row['id'],
        iconv(Yii::app()->charset, self::CSV_ENCODING, isset($sites[$row['siteID']]) ? $sites[$row['siteID']] : $row['siteID']),
        iconv(Yii::app()->charset, self::CSV_ENCODING, Yii::app()->collection->authLogType->itemAt($row['type'])),
        iconv(Yii::app()->charset, self::CSV_ENCODING, Yii::app()->format->formatBoolean($row['result'])),
        iconv(Yii::app()->charset, self::CSV_ENCODING, empty($userNameCache[$row['authorType']][$row['authorID']]) ? $row['authorType'] . '/' . $row['authorID'] : $userNameCache[$row['authorType']][$row['authorID']]),
        date('c', $row['time']),
        long2ip($row['remote']),
        ), ';');
    }

    return $tmpFile;
  }

  public function getTitle($view, $params = array())
  {
    extract($params);

    switch ($view) {
      case 'index':
        $title[] = Yii::t('admin.registry.titles', 'Authentication Log');
        break;

      case 'clear':
        $title[] = Yii::t('admin.registry.titles', 'Clear Authentication Log');
        break;
    }

    return implode(' | ', $title);
  }

  public function getClearSuccessMessage($params = array())
  {
    extract($params);
    return Yii::t('admin.registry.titles', 'Authentication log has been cleared');
  }

  public function getClearErrorMessage($params = array())
  {
    extract($params);
    return Yii::t('admin.registry.titles', 'An error occurred while clearing authentication log');
  }

  public function getModel($scenario = 'search')
  {
    return new VLogAuth($scenario);
  }

  public function getPlainModel()
  {
    return VLogAuth::model();
  }

  protected function afterClear($model)
  {
    parent::afterClear($model);
    Yii::app()->systemLog->logEvent('Authentication log has been cleared', array(), 'admin.registry.titles');
  }
}
