<?php
error_reporting(E_ALL | E_STRICT);

if (class_exists('PHPUnit_Runner_Version', true)) {
    $phpUnitVersion = PHPUnit_Runner_Version::id();
    if ('@package_version@' !== $phpUnitVersion && version_compare($phpUnitVersion, '3.6.0', '<')) {
        echo 'This version of PHPUnit (' . PHPUnit_Runner_Version::id() . ') is not supported in Zend Framework 2.x unit tests.' . PHP_EOL;
        exit(1);
    }
    unset($phpUnitVersion);
}

include realpath(__DIR__ . '/../../../init_autoloader.php');

chdir(dirname(__DIR__));

//Paths to other modules for which autoloading has to be set up
$otherModulePaths = array();

/**
 * Configures autoloading as defined in the module
 * Autoloading is configured for the module source
 * as well as for the module tests (if autoload_config_test.php is present)
 * @param string $modulePath
 */
function configureAutoloaderForModule($modulePath) {
    $modulePath = realpath($modulePath);
    $namespace = basename($modulePath);

    //Autoloader for source of the module
    $moduleFile = $modulePath . '/Module.php';
    $moduleClass = $namespace . '\Module';
    require_once($moduleFile);
    $module = new $moduleClass();
    $autoloadConfig = $module->getAutoloaderConfig();
    Zend\Loader\AutoloaderFactory::factory($autoloadConfig);

    //Autoloader for tests of the module
    $autoloadConfigFile = $modulePath . '/tests/autoload_config_test.php';
    if(file_exists($autoloadConfigFile)) {
        $autoloadConfig = require $autoloadConfigFile;
        Zend\Loader\AutoloaderFactory::factory($autoloadConfig);
    }
}

//Autoloading for this module
$thisModulePath = realpath(__DIR__ . '/../');
configureAutoloaderForModule($thisModulePath);

//Autoloading for other modules
foreach($otherModulePaths as $otherModulePath) {
    configureAutoloaderForModule($otherModulePath);
}

//Unset local variables
unset($thisModulePath, $otherModulePath, $otherModulePaths);