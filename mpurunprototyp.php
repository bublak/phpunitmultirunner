<?php

// NOTE: only prototyp for my tests

$mprunner = new Mprunner();
$mprunner->run();

class Mprunner {
    const THREAD_COUNT = 3;
    const THREAD_COUNT_MAIN = 2;


    const FOLDER = './portal_test/unit/afield';
    //const FOLDER = './portal_test/unit/afield/impl/IW/AField/Core/Validator';
    //const FOLDER = './portal_test/unit/afield/impl/IW/AField/Core/Validator/Zend';
    const FOLDER_SEPARATOR = '/';

    const DEFAULT_EXEC_TIME = '5';

    private $_path = '';

    public function __construct() {
        $this->_path = $_SERVER['PWD'] . self::FOLDER_SEPARATOR . self::FOLDER;
    }

    public function run() {
        echo "\ncreate file trees: \n";
        echo date('i:s', time());

        $tree = new Mputree($this->_path);

        $this->_prepareFileTree($this->_path, $tree);

        $this->_preprocessTree($tree);

        $tests = $this->_getTestsArray($tree, false);

        echo 'created file trees: \n';
        echo "\nrunning tests: \n";
        echo date('i:s', time());

        $this->_runUnits($tests);

        echo "\nfinished tests (3 still running): \n";
        echo date('i:s', time());
    }

    // TODO refactore this
    private function _runUnits(array $tests) {

        $bootstraps_init = array(
            'a' => ' --bootstrap="/iw/workspace/00701bparalel/portal_test/TestPreloadC.php" ',
            'b' => ' --bootstrap="/iw/workspace/00701bparalel/portal_test/TestPreloadD.php" ',
            //'c' => ' --bootstrap="/iw/workspace/00701bparalel/portal_test/TestPreloadE.php" '
        );

        $bootstraps = $bootstraps_init;

        while (count($tests) > 0) {
            if (count($bootstraps) == 0) {
                //TODO -> make it better -> because this waits for last process ending in group
                while (pcntl_waitpid(0, $status) != -1) {
                    $status = pcntl_wexitstatus($status);
                    //echo "Child $status completed\n";
                }

                $bootstraps = $bootstraps_init;
            }

            $boot = array_pop($bootstraps);

            $test = array_pop($tests);

            //$command = escapeshellcmd('phpunit '. $boot . $test);
            $command = 'phpunit '. $boot . $test;
            //var_dump($command);

            $pid = pcntl_fork();
            if (!$pid) {
                exec($command, $result);

                // TODO -> how process result messages - print only errors immediately
                //echo(implode($result, "\n"));
                exit($pid);
            }
        }

        $stop = 3;

        $i = 0;

        //foreach ($tests as $test) {
            //$command = escapeshellcmd('phpunit '. $bootstraps['a'] . $test).'&';

            //$pid = pcntl_fork();
            //if (!$pid) {
                //exec($command, $result);
                //var_export($result);
                //exit($pid);
            //}


            //if ($i++ == $stop) {
                //break;
            //}
        //}
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
            $fullPath = $path . self::FOLDER_SEPARATOR . $file;

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
