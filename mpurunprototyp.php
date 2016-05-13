<?php

// NOTE: only prototyp for my tests

$startBasic = new Basic();
$startBasic->run();


class BasicEngine {

    public function runUnits(array $tests) {
        foreach ($tests as $test) {
            $command = escapeshellcmd('phpunit '. $test);
            exec($command, $result);
        }
    }
}

// todo
interface IEngine {
    public function runUnits(array $tests, array $options=null);
}


class Basic {

    public function run() {
        $folder = $_SERVER['PWD'] . EnvSettings::FOLDER_SEPARATOR . EnvSettings::FOLDER;

        $engine = new BasicEngine();

        $mprunner = new Mprunner($engine, $folder);
        $mprunner->run();
    }
}

//[TODO, do some PRinter
//    AND Timer (timer will work only for one thread)
//    AND saver/loader of measured times

class EnvSettings {
    const FOLDER_SEPARATOR = '/';
    const THREAD_COUNT = 3;
    const THREAD_COUNT_MAIN = 2;

    //const FOLDER = './portal_test/unit/afield';
    const FOLDER = './portal_test/unit/afield/impl/IW/AField/Core/Validator';
    //const FOLDER = './portal_test/unit/afield/impl/IW/AField/Core/Validator/Zend';
}


// todo implements Engine
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

class Mprunner {

    const FOLDER_SEPARATOR = '/';
    const DEFAULT_EXEC_TIME = '5';

    private $_path   = '';
    private $_engine = null;

    // todo interface Engine
    public function __construct($engine, $folder) {
        $this->_path = $folder;
        $this->_engine = $engine;
    }

    public function run() {
        echo "\ncreate file trees: \n";
        echo date('i:s', time());

        $tree = new Mputree($this->_path);

        $this->_prepareFileTree($this->_path, $tree);

        $this->_preprocessTree($tree);

        $tests = $this->_getTestsArray($tree, false);

        echo "created file trees: \n";
        echo "running tests: \n";
        echo date('i:s', time());

        $this->_engine->runUnits($tests);

        echo "\nfinished tests \n";
        echo date('i:s', time());
    }

    // TODO -> make sorted array according exec times
    // think about better array keys -> because of recognize fast the subfolders (maybe keep the tree for running?)
    private function _getTestsArray($tree, $onlyDir=false) {
        $tests = array();

        $subnodes = $tree->getNodes();

        if ($subnodes) {
            if ($onlyDir) {
                $tests[] = $tree->getFullPath();
            }

            foreach ($subnodes as $subnode) {
                $subtests = $this->_getTestsArray($subnode, $onlyDir);

                $tests = array_merge($subtests, $tests);
            }
        } else {
            if (!$onlyDir) {
                $tests[] = $tree->getFullPath();
            }
        }

        return $tests;
    }

    // to set folder times to folders
    // TODO = filter empty folders, if there are?
    private function _preprocessTree($node) {
        $subnodes = $node->getNodes();

        if ($subnodes) {
            $totalExecCount = 0;

            foreach ($subnodes as $subnode) {
                $execCount = $this->_preprocessTree($subnode);
                $totalExecCount += $execCount;
            }

            $node->setExecTime($totalExecCount);
            $execCount = $totalExecCount;
        } else {
            $execCount = $node->getExecTime();
        }

        return $execCount;
    }

    private function _prepareFileTree($path, $tree) {
        $data = $this->_getDirContent($path);

        foreach ($data as $file) {
            $fullPath = $path . EnvSettings::FOLDER_SEPARATOR . $file;

            if (is_dir($fullPath)) {
                $newTree = new Mputree($fullPath);

                $tree->addNode($newTree);

                $this->_prepareFileTree($fullPath, $newTree);
            } else {
                if ($this->_isPhpFile($file)) {
                    $newTree = new Mputree($fullPath);
                    $newTree->setFilename($file);
                    $newTree->setExecTime(self::DEFAULT_EXEC_TIME);

                    $tree->addNode($newTree);
                }
            }
        }
    }

    private function _isPhpFile($name) {
        $result = false;

        $pos = strpos($name, '.php');

        if ($pos !== false && strlen($name) - $pos == 4) {
            $result = true;
        }

        return $result;
    }

    /**
     * Load files from folder.
     *
     * @param string $folder absolute path to folder, where search files
     *
     * @return array with file names without path in requested folder
     */
    private function _getDirContent($folder) {
        $result = false;

        $result = array_diff(scandir($folder), array('..', '.'));

        if (count($result) == 0) {
            $result = false;
        } else {
            $result = array_values($result);
        }

        return $result;
    }
}

class Mputree {
    private $_nodes = array();

    private $_fullPath = null;
    private $_filename = null;

    private $_execTimeRating = null;
    private $_execTime       = null;

    public function __construct($fullPath) {
        $this->_fullPath = $fullPath;
    }

    public function setFullPath($path) {
        $this->_fullPath = $path;
    }

    public function setFilename($name) {
        $this->_filename = $name;
    }

    public function setNodes(array $nodes) {
        $this->_nodes[$this->fullPath] = $nodes;
    }

    public function setExecTimeRating($rating) {
        $this->_execTimeRating = $rating;
    }

    public function setExecTime($time) {
        $this->_execTime = $time;
    }

    public function addNode(Mputree $node) {
        $this->_nodes[] = $node;
    }

    public function getExecTime() {
        return $this->_execTime;
    }

    public function getNodes() {
        return $this->_nodes;
    }

    public function getFullPath() {
        return $this->_fullPath;
    }

    public function getFilename() {
        return $this->_filename;
    }
}
