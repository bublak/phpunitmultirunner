<?php
namespace bublak\phpunitmultirunner\Engines\Executors;

class Exec implements IExecutor {

    public function execTest($command, array &$result) {
        exec($command, $result);
    }
}
