<?php
/**
 * ViraCMS Cache Management Command
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VCacheCommand extends CConsoleCommand
{
  public function run($args)
  {
    // set default webroot alias
    Yii::setPathOfAlias('webroot', dirname(Yii::app()->basePath) . DIRECTORY_SEPARATOR . 'public');

    $executed = false;
    $args = array_map('trim', $args);

    if (in_array('all', $args)) {
      $args = array('app', 'opcode', 'config', 'image', 'assets');
    }

    if (in_array('app', $args)) {
      $executed = true;
      echo 'Flushing application cache...';
      VCacheHelper::flushAppCache();
      echo 'done!' . PHP_EOL;
    }

    if (in_array('opcode', $args)) {
      $executed = true;
      echo 'Flushing opcode cache...';
      VCacheHelper::flushOpcodeCache();
      echo 'done!' . PHP_EOL;
    }

    if (in_array('config', $args)) {
      $executed = true;
      echo 'Flushing configuration cache...';
      VCacheHelper::flushConfigCache();
      echo 'done!' . PHP_EOL;
    }

    if (in_array('image', $args)) {
      $executed = true;
      echo 'Flushing images cache...';
      VCacheHelper::flushImageCache();
      echo 'done!' . PHP_EOL;
    }

    if (in_array('assets', $args)) {
      $executed = true;
      echo 'Flushing assets cache...';
      VCacheHelper::flushAssetsCache();
      echo 'done!' . PHP_EOL;
    }

    if (!$executed) {
      echo $this->getHelp();
      exit(1);
    }
  }

  public function getHelp()
  {
    $help = PHP_EOL . 'Usage:';
    $help .= ' php ' . $this->getCommandRunner()->getScriptName() . ' ' . $this->getName() . ' [all] [app] [opcode] [config] [image]' . PHP_EOL;
    $help .= 'Where:' . PHP_EOL;
    $help .= ' * all    - ' . 'flush all of caches' . '.' . PHP_EOL;
    $help .= ' * app    - ' . 'flush application cache' . '.' . PHP_EOL;
    $help .= ' * opcode - ' . 'flush opcode cache' . '.' . PHP_EOL;
    $help .= ' * config - ' . 'flush configuration cache' . '.' . PHP_EOL;
    $help .= ' * image  - ' . 'flush images cache' . '.' . PHP_EOL;
    $help .= ' * assets - ' . 'flush published assets' . '.' . PHP_EOL;

    return $help;
  }
}
