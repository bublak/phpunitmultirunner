<?php

class MultiprocessEngine {
    // TODO refactore this
    // TODO ->  need to save measured times to be possible: see next todo:
    // TODO -> start process for the one of the slowest test and in other process for example 10 of the fastest unit tests
    public function runUnits(array $tests, $options) {

        $bootstraps_init = array(
            'a' => ' --bootstrap="/iw/workspace/00701bparalel/portal_test/TestPreloadC.php" ',
            'b' => ' --bootstrap="/iw/workspace/00701bparalel/portal_test/TestPreloadD.php" ',
            //'c' => ' --bootstrap="/iw/workspace/00701bparalel/portal_test/TestPreloadE.php" '
        );

        $bootstraps   = $bootstraps_init;
        $bootFreeKeys = array_keys($bootstraps);

        $processes = array();

        // first requested count of processes
        $i = 0;
        while ($i++ < count($bootstraps)) {
            $test        = array_pop($tests);
            $bootFreeKey = array_pop($bootFreeKeys);

            $boot = $bootstraps[$bootFreeKey];

            $command = escapeshellcmd('phpunit '. $boot . $test);

            $pid = pcntl_fork();

            if (!$pid) {
                //echo "child forked\n";
                echo "processing command: $command\n";
                exec($command, $result);

                // TODO -> how process result messages - print only errors immediately
                //var_dump($result);
                //echo(implode($result, "\n"));
                exit();
            } else {
                //echo "created $pid\n";
                //var_dump($bootFreeKeys);
                $processes[$pid] = $bootFreeKey;
            }
        }

        // then if some of processes ends, start new with the same bootstrap
        while (($processId = pcntl_waitpid(-1, $status)) != -1) {
            if (count($tests)) {
                $test        = array_pop($tests);
                $bootFreeKey = $processes[$processId];

                $boot = $bootstraps[$bootFreeKey];

                $command = escapeshellcmd('phpunit '. $boot . $test);

                $pid = pcntl_fork();

                if (!$pid) {
                    //echo "child forked\n";
                    echo "processing command: $command\n";
                    exec($command, $result);

                    // TODO -> how process result messages - print only errors immediately
                    //var_dump($result);
                    exit();
                } else {
                    //echo "created $pid\n";
                    //var_dump($bootFreeKeys);
                    $processes[$pid] = $bootFreeKey;
                }

                //echo "child ends $processId\n";

                // return bootstrap
                unset($processes[$processId]);

                $status = pcntl_wexitstatus($status);
            }
        }
    }
}
