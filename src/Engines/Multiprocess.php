<?php
namespace bublak\phpunitmultirunner\Engines;

use bublak\phpunitmultirunner\Engines\Executors\IExecutor;


class Multiprocess extends AbstractEngine
{

    public function runUnits(array $tests, array $options=null) {
        $file = 'ble.txt';

        if (file_exists($file)) {
            unlink($file);
        }


        $fileHandler = fopen($file, 'w+');
        fclose($fileHandler);

        $results = array();

        $bootstraps = $this->_getBootstraps($options);

        $bootstrapsKeys = array_keys($bootstraps);

        $hasEnoughTests = count($bootstraps) > count($tests) ? false : true;

        while (!$hasEnoughTests) {
            array_pop($bootstraps);

            $hasEnoughTests = count($bootstraps) > count($tests) ? false : true;
        }

        foreach ($bootstraps as $boostrap) {
            $testChunk = array_pop($tests);

            $pid = pcntl_fork();

            if (!$pid) {
                $result = parent::runUnits($testChunk, array());

                $fileHandler = fopen($file, 'w+');

                flock($fileHandler, LOCK_EX);

                $existingResults = $this->_load($file);

                //while(!feof($fileHandler)) {
                    //$existingResults = $existingResults.unserialize(fgets($fileHandler));
                //}

                $allResults = is_array($existingResults) ? array_merge($existingResults, $result) : $result;
                $res        = serialize($allResults);

                fwrite($fileHandler, $res);
                fflush($fileHandler);

                flock($fileHandler, LOCK_UN);
                fclose($fileHandler);

                exit($pid);
            }
        }

        while (pcntl_waitpid(0, $status) != -1) {
        }

        //load results
        $results = $this->_load($file);

        return $results;
    }

    private function _getBootstraps($options) {
        return is_array($options['bootstraps']) ? $options['bootstraps'] : array();
    }

    private function _load($file) {
        $fileHandler = fopen($file, 'r');

        if ($fileHandler === false) {
            throw new \Exception('Unable to open file: '.$file);
        }

        while(!feof($fileHandler)) {
            $data = $data.fgets($fileHandler);
        }

        fclose($fileHandler);

        return unserialize($data);
    }
}
