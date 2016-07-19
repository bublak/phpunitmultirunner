<?php

namespace bublak\phpunitmultirunner\Tree\Sorters;

class Basic {

    private $_processesCount = 1;

    public function __construct($processesCount=1) {
        $this->_processesCount = $processesCount;
    }

    public function sort(&$tests) {
        if ($this->_processesCount === 1) {
            return;
        }

        $result = array();

        $count = 0;
        foreach ($tests as $test) {

            $key = $count % $this->_processesCount;
            $count++;
            $result[$key][] = $test;
        }

        $tests = $result;
    }
}
