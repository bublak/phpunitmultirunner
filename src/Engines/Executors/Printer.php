<?php
namespace bublak\phpunitmultirunner\Engines\Executors;

class Printer implements IExecutor {

    public function execTest($command, array &$result) {
        echo ($command);
        $result = array('OK');
    }
}
