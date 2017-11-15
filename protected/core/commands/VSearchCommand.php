<?php
/**
 * ViraCMS Search Index Management Commands
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VSearchCommand extends CConsoleCommand
{
  public $defaultAction = 'help';

  public function behaviors()
  {
    return array(
      array(
        'class' => 'core.behaviors.VConsoleTableBehavior',
      ),
    );
  }

  public function actionHelp()
  {
    echo $this->getHelp();
  }

  /**
   * Clear search index
   * @param array $args command arguments
   */
  public function actionClear($args)
  {
    $searchIndex = $this->getSearchIndexComponent();

    echo 'Clearing search index...';
    $searchIndex->clear();
    echo 'OK' . PHP_EOL;
  }

  /**
   * Rebuild search index
   * @param array $args command arguments
   */
  public function actionRebuild($args)
  {
    $searchIndex = $this->getSearchIndexComponent();

    // workaround for createUrl functions in console application
    Yii::app()->request->setBaseUrl('');

    echo 'Rebuilding search index...';
    $searchIndex->rebuild();
    echo 'OK' . PHP_EOL;
  }

  /**
   * Returns search index component
   * @return VSearchIndex
   */
  protected function getSearchIndexComponent()
  {
    if (!Yii::app()->hasComponent('searchIndex')) {
      echo 'No search index component has been defined, exiting.' . PHP_EOL;
      exit(1);
    }

    return Yii::app()->searchIndex;
  }

  /**
   * Returns textual help block
   * @return string
   */
  public function getHelp()
  {
    $help = PHP_EOL . 'Usage:';
    $help .= ' php ' . $this->getCommandRunner()->getScriptName() . ' ' . $this->getName() . ' [command] [args]' . PHP_EOL . PHP_EOL;
    $help .= 'Available commands:' . PHP_EOL;
    $help .= $this->strpad('  help ', 25) . 'this message.' . PHP_EOL;
    $help .= $this->strpad('  clear', 25) . 'clear search index.' . PHP_EOL;
    $help .= $this->strpad('  rebuild', 25) . 'rebuild search index.' . PHP_EOL;

    return $help;
  }
}
