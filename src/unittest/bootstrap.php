<?php
/**
 * autoloader for developer.
 */
$autoloaderScript = dirname(__FILE__).'/../vendor/autoload.php';

$loader = require $autoloaderScript;
$loader->add('Iamapen', dirname(__FILE__).'/../lib');
