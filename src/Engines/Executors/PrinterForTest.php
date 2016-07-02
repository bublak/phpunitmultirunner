<?php
namespace bublak\phpunitmultirunner\Engines\Executors;

class PrinterForTest implements IExecutor {

    public function execTest($command, array &$result) {
        $result = array('OK', $command);
    }
}
