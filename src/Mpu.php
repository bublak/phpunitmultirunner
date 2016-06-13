<?php


$startBasic = new Basic();
$startBasic->run();


class Basic {

    public function run() {
        $folder = $_SERVER['PWD'] . EnvSettings::FOLDER_SEPARATOR . EnvSettings::FOLDER;

        $engine = new BasicEngine();

        $mprunner = new Mprunner($engine, $folder);
        $mprunner->run();
    }
}

class Mpu {

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
