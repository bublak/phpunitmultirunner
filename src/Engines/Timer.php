<?php
namespace bublak\phpunitmultirunner\Engines;

class Timer {
    private $_times = array();

    // return seconds
    public function getTime($identificator) {
        $endTime = microtime(true);

        if (isset($this->_times[$identificator])) {
            $startTime = $this->_times[$identificator];
        } else {
            // TODO add log warning
            return false;
        }

        return ($endTime - $startTime); // seconds
    }

    public function resetTime($identificator) {
        $diffTime = $this->getTime($identificator);

        unset($this->_times[$identificator]);

        return $diffTime;
    }

    public function setTime($identificator) {
        $this->_times[$identificator] = microtime(true);
    }

    public function resetAll() {
        $this->_times = array();
    }
}
