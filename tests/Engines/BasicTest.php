<?php

use bublak\phpunitmultirunner\Engines\Basic;

class BasicTest extends PHPUnit_Framework_TestCase {

    public function testA() {
        $basicEngine = new Basic();
        $basicEngine->runUnits(array());

        self::assertTrue(True);
    }
}
