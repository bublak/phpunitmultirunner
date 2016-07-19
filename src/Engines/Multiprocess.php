<?php
namespace bublak\phpunitmultirunner\Engines;

use bublak\phpunitmultirunner\Engines\Executors\IExecutor;


class Multiprocess extends AbstractEngine
{

    public function runUnits(array $tests, array $options=null) {
        $file = 'ble.txt';

        $fileHandler = fopen($file, 'r+');

        if (file_exists($file)) {
            // delete content of file
            ftruncate($fileHandler, 0);
        }

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
                $result = parent::runUnits($testChunk, null);

                $fileHandler = fopen($file, 'r+');

                if (flock($fileHandler, LOCK_EX)) {  // acquire an exclusive lock
                    $existingResults = unserialize($this->_readDataFromFileHandler($fileHandler));

                    $allResults = is_array($existingResults) ? array_merge($existingResults, $result) : $result;
                    $res        = serialize($allResults);

                    ftruncate($fileHandler, 0);
                    rewind($fileHandler);
                    fwrite($fileHandler, $res);
                    fflush($fileHandler);            // flush output before releasing the lock
                    flock($fileHandler, LOCK_UN);    // release the lock
                }

                fclose($fileHandler);

                exit($pid);
            }
        }

        if (count($bootstraps )) {
            while (pcntl_waitpid(0, $status) != -1) {
            }

            //load results
            $results = $this->_loadFileContent($file);
        }

        return $results;
    }

    private function _getBootstraps($options) {
        return is_array($options['bootstraps']) ? $options['bootstraps'] : array();
    }

    private function _loadFileContent($file) {
        $fileHandler = fopen($file, 'r');

        $data = $this->_readDataFromFileHandler($fileHandler);

        fclose($fileHandler);

        return unserialize($data);
    }

    private function _readDataFromFileHandler($fileHandler) {
        if ($fileHandler === false) {
            throw new \Exception('Unable to open file: '.$file);
        }

        $data = '';

        while(!feof($fileHandler)) {
            $data = $data.fgets($fileHandler);
        }

        return $data;
    }
}
