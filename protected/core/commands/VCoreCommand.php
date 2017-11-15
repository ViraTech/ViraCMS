<?php
/**
 * ViraCMS Core Management Commands
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VCoreCommand extends VApiBaseCommand
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
   * Enable or disable sites maintenance mode
   * @param array $args command arguments
   */
  public function actionMaintenance($args)
  {
    $args = array_map('trim', $args);
    if (count($args) == 0) {
      if (Yii::app()->maintenance) {
        echo PHP_EOL . 'Site maintenance mode is enabled.' . PHP_EOL;
      }
      else {
        echo PHP_EOL . 'Site maintenance mode is disabled.' . PHP_EOL;
      }
    }
    elseif (in_array($args[0], array('on', 'off'))) {
      $this->setMaintenance($args[0] == 'on');
    }
    else {
      echo $this->getHelp();
    }
  }

  /**
   * Return local configuration overrides file path
   * @return string
   */
  protected function getConfigFile()
  {
    return Yii::app()->basePath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'local.php';
  }

  /**
   * Check if configuration file is writeable
   */
  protected function checkIfConfigWriteable()
  {
    echo 'Checking for config file...';
    $file = $this->getConfigFile();
    if (file_exists($file) && is_writable($file)) {
      echo 'done!' . PHP_EOL;
    }
    else {
      echo 'error!' . PHP_EOL;
      echo strtr('File {file} is not writeable, can not continue.', array(
        '{file}' => $file,
      )) . PHP_EOL;
      exit(1);
    }
  }

  /**
   * Enable or disable maintenance mode
   * @param boolean $mode true for enable, false otherwise
   * @param boolean $checkConfig first check configuration file is writeable
   */
  protected function setMaintenance($mode, $checkConfig = true)
  {
    $mode = (bool) $mode;

    if ($checkConfig) {
      $this->checkIfConfigWriteable();
    }

    $file = $this->getConfigFile();
    $config = require($file);

    if (!isset($config['maintenance']) || $mode != $config['maintenance']) {
      $config['maintenance'] = $mode;

      file_put_contents($file, '<?php' . PHP_EOL . 'return ' . var_export($config, true) . ';');

      VCacheHelper::flushConfigCache();

      if ($mode) {
        echo 'Maintenance mode has been enabled successfully.' . PHP_EOL;
      }
      else {
        echo 'Maintenance mode has been disabled successfully.' . PHP_EOL;
      }
    }
    else {
      echo 'Maintenance mode is already in this state.' . PHP_EOL;
    }
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
    $help .= $this->strpad('  maintenance', 25) . 'show maintenance mode.' . PHP_EOL;
    $help .= $this->strpad('  maintenance [on|off]', 25) . 'enable or disable maintenance mode.' . PHP_EOL;

    return $help;
  }
}
