<?php
 
require_once __DIR__.'/vendor/symfony/class-loader/Symfony/Component/ClassLoader/UniversalClassLoader.php';
 
use Symfony\Component\ClassLoader\UniversalClassLoader;
 
$loader = new UniversalClassLoader();

$loader->registerNamespaces(array(
	'Symfony\Component\Console'					=> __DIR__.'/vendor/symfony/console',
	'Symfony\Component\EventDispatcher'			=> __DIR__.'/vendor/symfony/event-dispatcher',
	'Symfony\Component\Process'					=> __DIR__.'/vendor/symfony/process',
	'JsonSchema'								=> __DIR__.'/vendor/justinrainbow/json-schema/src/',
	'Seld'										=> __DIR__.'/vendor/seld/jsonlint/src/',
	'Deploy'									=> __DIR__.'/src/',
));

$loader->register();
