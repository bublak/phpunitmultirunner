<?php

// NOTE: only prototyp for my tests, without threading

$mprunner = new Mprunner();
$mprunner->run();

class Mprunner {
    const THREAD_COUNT = 3;
    const THREAD_COUNT_MAIN = 2;


    //const FOLDER = './portal_test/unit/afield';
    const FOLDER = './portal_test/unit/afield/impl/IW/AField/Core/Validator/Zend';
    const FOLDER_SEPARATOR = '/';

    const DEFAULT_EXEC_TIME = '5';

    private $_path = '';

    public function __construct() {
        $this->_path = $_SERVER['PWD'] . self::FOLDER_SEPARATOR . self::FOLDER;
    }

    public function run() {

        $tree = new Mputree($this->_path);

        $this->_prepareFileTree($this->_path, $tree);

        $this->_preprocessTree($tree);
        $tests = $this->_getTestsArray($tree, true);

        $this->_runUnits($tests);

    }

    private function _runUnits(array $tests) {
        $stop = 1;

        $i = 0;

        foreach ($tests as $test) {
            // todo THREADS, need download
            $command = escapeshellcmd('phpunit '. $test).'&';
            exec($command, $result);

            var_export($result);

            if ($i++ == $stop) {
                break;
            }
        }
    }


    // TODO -> make sorted array according exec times
    private function _getTestsArray($tree, $onlyDir=false) {
        $tests = array();

        $subnodes = $tree->getNodes();

        if ($subnodes) {
            foreach ($subnodes as $subnode) {
                $subtests = $this->_getTestsArray($subnode, $onlyDir);

                if ($onlyDir) {
                    $dirtest[] = $tree->getFullPath();
                    $tests = array_merge($dirtest, $tests);
                } else {
                    $tests = array_merge($subtests, $tests);
                }
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
