<?php
/**
 * autoloader for developer.
 */
$autoloaderScript = dirname(__DIR__).'/vendor/autoload.php';

$loader = require $autoloaderScript;
$loader->add('Iamapen', __DIR__);
