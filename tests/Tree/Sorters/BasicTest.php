<?php
use bublak\phpunitmultirunner\Tree\Sorters\Basic;

class SorterTest extends PHPUnit_Framework_TestCase {

    public function testSortForOneProcess() {
        $sorter = new Basic(1);

        $tests    = array('abc', 'def', 'ijk');
        $expected = $tests;

        $sorter->sort($tests);

        self::assertEquals($expected, $tests);
    }

    public function testSortThreeTestsForTwoProcesses() {
        $sorter = new Basic(2);

        $tests    = array('abc', 'def', 'ijk');

        $sorter->sort($tests);

        $expected = array(0 => array('abc', 'ijk'), 1 => array('def'));
        self::assertEquals($expected, $tests);
    }

    public function testSortFiveTestsForTwoProcesses() {
        $sorter = new Basic(2);

        $tests    = array('abc', 'def', 'ijk', 'lmn', 'opq');

        $sorter->sort($tests);

        $expected = array(0 => array('abc', 'ijk', 'opq'), 1 => array('def', 'lmn'));
        self::assertEquals($expected, $tests);
    }

    public function testSortFiveTestsForThreeProcesses() {
        $sorter = new Basic(3);

        $tests    = array('abc', 'def', 'ijk', 'lmn', 'opq');

        $sorter->sort($tests);

        $expected = array(
            0 => array('abc', 'lmn'),
            1 => array('def', 'opq'),
            2 => array('ijk')
        );
        self::assertEquals($expected, $tests);
    }
}

