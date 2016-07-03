<?php

use bublak\phpunitmultirunner\Engines\Basic;
<<<<<<< HEAD
use bublak\phpunitmultirunner\Engines\Executors\PrinterForTest;
=======
>>>>>>> 8ef53db528c5df748347a93479d596a7c5eca651

class BasicTest extends PHPUnit_Framework_TestCase {

    public function testA() {
<<<<<<< HEAD
        $basicEngine = new Basic(new PrinterForTest());
=======
        $basicEngine = new Basic();
>>>>>>> 8ef53db528c5df748347a93479d596a7c5eca651
        $basicEngine->runUnits(array());

        self::assertTrue(True);
    }
<<<<<<< HEAD

    public function testBB() {
        $testNameA = 'qbc.test';
        $basicEngine = new Basic(new PrinterForTest());
        $results = $basicEngine->runUnits(array($testNameA), array());

        self::assertInternalType('array', $results);
        self::assertInternalType('array', $results[0]);
        self::assertTrue(isset($results[0]['execTime']));
        self::assertEquals(array('OK', 'phpunit ' . $testNameA), $results[0]['result']);
    }
=======
>>>>>>> 8ef53db528c5df748347a93479d596a7c5eca651
}
