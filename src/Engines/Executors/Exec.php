<?php
namespace bublak\phpunitmultirunner\Engines\Executors;

class Exec implements IExecutor {

    public function execCommand($command, &$result) {
        exec($command, $result);
    }
}
