<?php
namespace bublak\phpunitmultirunner\Engines\Executors;

class Printer implements IExecutor {

    public function execTest($command, array &$result) {
        echo ($command . "\n");
        $result = array('OK');
    }
}
