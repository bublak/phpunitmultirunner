<?php

// NOTE: only prototyp for my tests, without threading

$mprunner = new Mprunner();
$mprunner->run();

class Mprunner {
    const THREAD_COUNT = 3;
    const THREAD_COUNT_MAIN = 2;


    const FOLDER = './portal_test/unit/afield';
    const FOLDER_SEPARATOR = '/';

    const DEFAULT_EXEC_TIME = '5';

    private $_path = '';

    public function __construct() {
        $this->_path = $_SERVER['PWD'] . self::FOLDER_SEPARATOR . self::FOLDER;
    }

    public function run() {

        $fileTree = new Mputree($this->_path);

        $this->_prepareFileTree($this->_path, $fileTree);

        //var_export($fileTree);
        $this->_runUnits($fileTree);
    }

    private function _runUnits($tree) {
        $stop = 1;

        $i = 0;

        foreach ($tree->getNodes() as $nodes) {
            foreach ($nodes as $node) {
                var_dump($node->getFullPath());
                exec(escapeshellcmd('phpunit '. $node->getFullPath()).'&', $result);

                if ($i++ == $stop) {
                    var_export($result);
                    break;
                }
            }
        }
    }

    private function _prepareFileTree($path, $tree) {
        $data = $this->_getDirContent($path);

        foreach ($data as $file) {
            $fullPath = $path . self::FOLDER_SEPARATOR . $file;

            if (is_dir($fullPath)) {
                // TODO -> add support for save paths -> then is posible run units for whole path
                //$newTree = new Mputree($fullPath);

                $this->_prepareFileTree($fullPath, $tree);
            } else {
                $newTree = new Mputree($fullPath);
                $newTree->setFilename($file);
                $newTree->setExecTime(self::DEFAULT_EXEC_TIME);

                $tree->addNode($newTree);
            }
        }
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
    private $_nodes = null;

    private $_fullPath = null;
    private $_filename = null;

    private $_execTimeRating = null;
    private $_pathExecTime   = null;

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
        $this->_nodes[$this->_fullPath][] = $node;
    }

    public function getNodes() {
        return $this->_nodes;
    }

    public function getFullPath() {
        return $this->_fullPath;
    }
}
