<?php

use bublak\phpunitmultirunner\Engines\Basic;
use bublak\phpunitmultirunner\Engines\Executors\PrinterForTest;

class BasicTest extends PHPUnit_Framework_TestCase {

    public function testEmptyTests() {
        $basicEngine = new Basic(new PrinterForTest());
        $result = $basicEngine->runUnits(array());

        self::assertEquals(array(), $result);
    }

    public function testOneTest() {
        $testNameA = 'qbc.test';
        $basicEngine = new Basic(new PrinterForTest());
        $results = $basicEngine->runUnits(array($testNameA), array());

        self::assertInternalType('array', $results);
        self::assertInternalType('array', $results[0]);
        self::assertTrue(isset($results[0]['execTime']));
        self::assertEquals(array('OK', 'phpunit ' . $testNameA), $results[0]['result']);
    }

    public function testTwoTests() {
        $testNameA = 'qbc.test';
        $testNameB = 'qbcd.test';
        $basicEngine = new Basic(new PrinterForTest());
        $results = $basicEngine->runUnits(array($testNameA, $testNameB), array());

        self::assertInternalType('array', $results);
        self::assertEquals(2, count($results));

        self::assertInternalType('array', $results[0]);
        self::assertTrue(isset($results[0]['execTime']));
        self::assertEquals(array('OK', 'phpunit ' . $testNameA), $results[0]['result']);

        self::assertInternalType('array', $results[1]);
        self::assertTrue(isset($results[1]['execTime']));
        self::assertEquals(array('OK', 'phpunit ' . $testNameB), $results[1]['result']);

    }
}
