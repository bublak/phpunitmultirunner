<?php

use bublak\phpunitmultirunner\Engines\Timer;

class TimerTest extends PHPUnit_Framework_TestCase {

    public function testResetNotExistingTime() {
        $timer = new Timer();

        $result = $timer->resetTime('nothing');
        self::assertFalse($result);
    }

    public function testGetNotExistingTime() {
        $timer = new Timer();

        $result = $timer->getTime('nothing');
        self::assertFalse($result);
    }

    public function testSetTime() {
        $timer = new Timer();

        $timer->setTime('a');
        usleep(400000);

        $timeA = $timer->getTime('a');

        $timer->setTime('b');
        sleep(1);

        $timeA = $timer->getTime('a');
        self::assertTrue($timeA < 1.6);

        $timer->setTime('c');
        usleep(200000);

        $timeA = $timer->getTime('a');
        self::assertTrue($timeA > 1.6);

        $timeB = $timer->getTime('b');
        self::assertTrue($timeB > 1.2);

        $timeC = $timer->getTime('c');
        self::assertTrue($timeC > 0.2);
        self::assertTrue($timeC < 1);
    }

    public function testResetAll() {
        $timer = new Timer();

        $timer->setTime('a');
        $timer->setTime('b');
        $timer->setTime('c');

        $timer->resetAll();

        self::assertFalse($timer->getTime('a'));
        self::assertFalse($timer->getTime('b'));
        self::assertFalse($timer->getTime('c'));

        self::assertFalse($timer->resetTime('a'));
        self::assertFalse($timer->resetTime('b'));
        self::assertFalse($timer->resetTime('c'));
    }
}
