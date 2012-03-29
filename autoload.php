<?php
require_once(__DIR__."/vendor/autoload.php");

use Symfony\Component\ClassLoader\UniversalClassLoader;
 
$loader = new UniversalClassLoader();

$loader->registerNamespaces(array(
	'Deploy'									=> __DIR__.'/src/',
	'Jcid'										=> __DIR__.'/src/',
));

$loader->register();

set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__.'/vendor/phpseclib');