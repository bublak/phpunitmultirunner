<?php
namespace bublak\phpunitmultirunner;

require 'vendor/autoload.php';

use bublak\phpunitmultirunner\Engines\Timer;
use bublak\phpunitmultirunner\Engines\Basic;
use bublak\phpunitmultirunner\Engines\Multiprocess;
use bublak\phpunitmultirunner\Engines\Executors\Exec;
use bublak\phpunitmultirunner\Engines\Executors\Printer;
use bublak\phpunitmultirunner\Tree\Sorters\Basic as BasicSorter;
use bublak\phpunitmultirunner\Tree\Creator;


// *****************  ONE PROCESS  *********************
echo "\n\n ONE PROCESS \n";
///*
$path = $_SERVER['PWD'] . Settings::FOLDER_SEPARATOR . 'tests_for_tests';
$sorter = new BasicSorter();
$creator = new Creator($path, $sorter);

echo "\ncreate file trees: \n";
echo date('i:s', time());
$tests = $creator->getTreeArray();

echo "created file trees: \n";
echo "running tests: \n";
echo date('i:s', time());

$basicEngine = new Basic(new Exec(), new Timer());
$results = $basicEngine->runUnits($tests, array());

//var_dump($results);

echo "\nfinished tests \n";
echo date('i:s', time());
//*/

// *****************  MULTIPROCESS *********************
echo "\n\n MULTIPROCESS \n";

///*
$path = $_SERVER['PWD'] . Settings::FOLDER_SEPARATOR . 'tests_for_tests';
$sorter = new BasicSorter(3);
$creator = new Creator($path, $sorter);

echo "\ncreate file trees: \n";
echo date('i:s', time());
$tests = $creator->getTreeArray();

echo "created file trees: \n";
echo "running tests: \n";
echo date('i:s', time());

$bootstraps = array('bootstraps' => array('aa', 'bb', 'cc'));
$multiEngine = new Multiprocess(new Exec(), new Timer());
$results = $multiEngine->runUnits($tests, $bootstraps);

//var_dump($results);

echo "\nfinished tests \n";
echo date('i:s', time());
//*/
