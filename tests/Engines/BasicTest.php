<?php

use bublak\phpunitmultirunner\Engines\Basic;
use bublak\phpunitmultirunner\Engines\Executors\PrinterForTest;

class BasicTest extends PHPUnit_Framework_TestCase {

    public function testA() {
        $basicEngine = new Basic(new PrinterForTest());
        $basicEngine->runUnits(array());

        self::assertTrue(True);
    }

    public function testBB() {
        $testNameA = 'qbc.test';
        $basicEngine = new Basic(new PrinterForTest());
        $results = $basicEngine->runUnits(array($testNameA), array());

        self::assertInternalType('array', $results);
        self::assertInternalType('array', $results[0]);
        self::assertTrue(isset($results[0]['execTime']));
        self::assertEquals(array('OK', 'phpunit ' . $testNameA), $results[0]['result']);
    }
}
