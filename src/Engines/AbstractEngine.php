<?php
namespace bublak\phpunitmultirunner\Engines;

abstract class AbstractEngine implements IEngine {

    private $_timer = null;

    public function __construct(Timer $timer=null) {
        $this->_timer = $timer;
    }

    public function runUnits(array $tests, array $options=null) {
        foreach ($tests as $test) {
            $this->execTest($test);
        }
    }

    public function execTest($name) {
        $command = escapeshellcmd('phpunit '. $test);
        exec($command, $result);

        return $result;
    }
}
