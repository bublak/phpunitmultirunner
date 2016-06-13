<?php

class BasicEngine {

    public function runUnits(array $tests) {
        foreach ($tests as $test) {
            $command = escapeshellcmd('phpunit '. $test);
            exec($command, $result);
        }
    }
}
