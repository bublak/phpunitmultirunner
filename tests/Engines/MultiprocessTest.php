<?php

use bublak\phpunitmultirunner\Engines\Multiprocess;
use bublak\phpunitmultirunner\Engines\Executors\PrinterForTest;

class MultiprocessTest extends PHPUnit_Framework_TestCase {

    public function testEmptyTests() {
        $multiEngine = new Multiprocess(new PrinterForTest());
        $result = $multiEngine->runUnits(array());

        self::assertEquals(array(), $result);
    }

    public function testOneTestAndMoreBootstraps() {

        $bootstraps = array('bootstraps' => array(
            'a' => ' --bootstrap="/iw/workspace/00701bparalel/portal_test/TestPreloadC.php" ',
            'b' => ' --bootstrap="/iw/workspace/00701bparalel/portal_test/TestPreloadD.php" ',
        ));

        $testNameA = 'qbc.test';
        $testChunks = array(1 => array($testNameA));

        $multiEngine = new Multiprocess(new PrinterForTest());

        $results = $multiEngine->runUnits($testChunks, $bootstraps);

        self::assertInternalType('array', $results);
        self::assertInternalType('array', $results[0]);
        self::assertTrue(isset($results[0]['execTime']));
        self::assertEquals(array('OK', 'phpunit ' . $testNameA), $results[0]['result']);
    }

    public function testOneTest() {

        $bootstraps = array('bootstraps' => array(
            'a' => ' --bootstrap="/iw/workspace/00701bparalel/portal_test/TestPreloadC.php" ',
            'b' => ' --bootstrap="/iw/workspace/00701bparalel/portal_test/TestPreloadD.php" ',
            //'c' => ' --bootstrap="/iw/workspace/00701bparalel/portal_test/TestPreloadE.php" '
        ));

        $testNameA = 'qbc.test';
        $testChunks = array(1 => array($testNameA));

        $multiEngine = new Multiprocess(new PrinterForTest());
        $results = $multiEngine->runUnits($testChunks, $bootstraps);

        self::assertInternalType('array', $results);
        self::assertInternalType('array', $results[0]);
        self::assertTrue(isset($results[0]['execTime']));
        self::assertEquals(array('OK', 'phpunit ' . $testNameA), $results[0]['result']);
    }

    public function testTwoTestsInOneChunk() {
        $bootstraps = array('bootstraps' => array(
            'a' => ' --bootstrap="/iw/workspace/00701bparalel/portal_test/TestPreloadC.php" ',
            'b' => ' --bootstrap="/iw/workspace/00701bparalel/portal_test/TestPreloadD.php" ',
        ));

        $testNameA = 'qbc.test';
        $testNameB = 'qbcd.test';
        $multiEngine = new Multiprocess(new PrinterForTest());

        $testChunks = array(1 => array($testNameA, $testNameB));

        $results = $multiEngine->runUnits($testChunks, $bootstraps);

        self::assertInternalType('array', $results);
        self::assertEquals(2, count($results));

        self::assertInternalType('array', $results[0]);
        self::assertTrue(isset($results[0]['execTime']));
        self::assertEquals(array('OK', 'phpunit ' . $testNameA), $results[0]['result']);

        self::assertInternalType('array', $results[1]);
        self::assertTrue(isset($results[1]['execTime']));
        self::assertEquals(array('OK', 'phpunit ' . $testNameB), $results[1]['result']);

    }

    public function testThreeTestsInTwoChunks() {
        $bootstraps = array('bootstraps' => array(
            'a' => ' --bootstrap="/iw/workspace/00701bparalel/portal_test/TestPreloadC.php" ',
            'b' => ' --bootstrap="/iw/workspace/00701bparalel/portal_test/TestPreloadD.php" ',
        ));

        $testNameA = 'qbc.test';
        $testNameB = 'qbcd.test';
        $testNameC = 'qbcde.test';

        $multiEngine = new Multiprocess(new PrinterForTest());

        $testChunks = array(1 => array($testNameA, $testNameB), 2 => array($testNameC));

        $results = $multiEngine->runUnits($testChunks, $bootstraps);

        self::assertInternalType('array', $results);
        self::assertEquals(3, count($results));

        $expected = array(
            array('OK', 'phpunit ' . $testNameA),
            array('OK', 'phpunit ' . $testNameB),
            array('OK', 'phpunit ' . $testNameC)
        );

        foreach ($results as $result) {
            self::assertInternalType('array', $result);
            self::assertTrue(isset($result['execTime']));

            self::assertTrue(in_array($result['result'], $expected));
        }
    }

    public function testFiveTestsInTwoChunks() {
        $bootstraps = array('bootstraps' => array(
            'a' => ' --bootstrap="/iw/workspace/00701bparalel/portal_test/TestPreloadC.php" ',
            'b' => ' --bootstrap="/iw/workspace/00701bparalel/portal_test/TestPreloadD.php" ',
        ));

        $testNameA = 'qbc.test';
        $testNameB = 'qbcd.test';
        $testNameC = 'qbcde.test';
        $testNameD = 'qbcdef.test';
        $testNameE = 'qbcdefg.test';

        $multiEngine = new Multiprocess(new PrinterForTest());

        $testChunks = array(1 => array($testNameA, $testNameD, $testNameE), 2 => array($testNameC, $testNameB));

        $results = $multiEngine->runUnits($testChunks, $bootstraps);

        self::assertInternalType('array', $results);
        self::assertEquals(5, count($results));

        $expected = array(
            array('OK', 'phpunit ' . $testNameA),
            array('OK', 'phpunit ' . $testNameB),
            array('OK', 'phpunit ' . $testNameC),
            array('OK', 'phpunit ' . $testNameD),
            array('OK', 'phpunit ' . $testNameE)
        );

        foreach ($results as $result) {
            self::assertInternalType('array', $result);
            self::assertTrue(isset($result['execTime']));

            self::assertTrue(in_array($result['result'], $expected));
        }
    }


    public function testSevenTestsInThreeChunks() {
        $bootstraps = array('bootstraps' => array(
            'a' => ' --bootstrap="/iw/workspace/00701bparalel/portal_test/TestPreloadC.php" ',
            'b' => ' --bootstrap="/iw/workspace/00701bparalel/portal_test/TestPreloadD.php" ',
            'c' => ' --bootstrap="/iw/workspace/00701bparalel/portal_test/TestPreloadE.php" ',
        ));

        $testNameA = 'qbc.test';
        $testNameB = 'qbcd.test';
        $testNameC = 'qbcde.test';
        $testNameD = 'qbcdef.test';
        $testNameE = 'qbcdefg.test';
        $testNameF = 'qbcdefgh.test';
        $testNameG = 'qbcdefghi.test';

        $multiEngine = new Multiprocess(new PrinterForTest());

        $testChunks = array(
            1 => array($testNameA, $testNameD, $testNameE),
            2 => array($testNameC, $testNameB),
            3 => array($testNameF, $testNameG)
        );

        $results = $multiEngine->runUnits($testChunks, $bootstraps);

        self::assertInternalType('array', $results);
        self::assertEquals(7, count($results));

        $expected = array(
            array('OK', 'phpunit ' . $testNameA),
            array('OK', 'phpunit ' . $testNameB),
            array('OK', 'phpunit ' . $testNameC),
            array('OK', 'phpunit ' . $testNameD),
            array('OK', 'phpunit ' . $testNameE),
            array('OK', 'phpunit ' . $testNameF),
            array('OK', 'phpunit ' . $testNameG)
        );

        foreach ($results as $result) {
            self::assertInternalType('array', $result);
            self::assertTrue(isset($result['execTime']));

            self::assertTrue(in_array($result['result'], $expected));
        }
    }
}
