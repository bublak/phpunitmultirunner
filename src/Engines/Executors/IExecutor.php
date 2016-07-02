<?php

namespace bublak\phpunitmultirunner\Engines\Executors;

interface IExecutor {
    public function execTest($command, array &$result);
}
