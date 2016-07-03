<?php
namespace bublak\phpunitmultirunner\Engines;

use bublak\phpunitmultirunner\Engines\Executors\IExecutor;

abstract class AbstractEngine implements IEngine {

    private $_timer    = null;
    private $_executor = null;

    public function __construct(IExecutor $executor, Timer $timer=null) {
        $this->_timer    = $timer;
        $this->_executor = $executor;
    }

    public function runUnits(array $tests, array $options=null) {
        $results = array();

        foreach ($tests as $identificator=>$test) {
            $this->execTest($test, $identificator, $results);
        }

        return $results;
    }

    public function execTest($name, $identificator, &$results) {
        $command = escapeshellcmd('phpunit '. $name);

        $result = array();

        isset($this->_timer) ? $this->_timer->setTime($identificator):0;
        $this->_executor->execTest($command, $result);
        $execTime = isset($this->_timer) ? $this->_timer->getTime($identificator):0;

        $results[$identificator] = array('execTime'=>$execTime, 'result'=>$result);
    }
}
