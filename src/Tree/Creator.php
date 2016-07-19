<?php

namespace bublak\phpunitmultirunner\Tree;

use bublak\phpunitmultirunner\Tree\Helpers\File;
use bublak\phpunitmultirunner\Settings;

class Creator {

    private $_path = '';
    private $_sorter = null;

    public function __construct($fullPath, $sorter=null) {
        $this->_path = $fullPath;

        $this->_sorter = $sorter;
    }

    public function getTree() {
        $tree = new Mputree($this->_path, true);

        $this->_prepareFileTree($this->_path, $tree);

        $this->_preprocessTree($tree);

        return $tree;
    }

    public function getTreeArray() {
        $tree = $this->getTree();
        $tests = $this->_getTestsArray($tree, false);

        // TODO missing tests
        $this->_sortTestsForProcesses($tests);

        return $tests;
    }

    public function load($file) {
        $fileHandler = fopen($file, 'r');

        if ($fileHandler === false) {
            throw new \Exception('Unable to open file: '.$file);
        }

        $data = fgets($fileHandler);

        fclose($fileHandler);

        return unserialize($data);
    }

    public function save($file, $tree) {
        return $tree->save($file);
    }

    private function _sortTestsForProcesses($tests) {
        if (!is_null($this->_sorter)) {
            $this->_sorter->sort($tests);
        }
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
        $data = File::getDirContent($path);

        foreach ($data as $file) {
            $fullPath = $path . Settings::FOLDER_SEPARATOR . $file;

            if (is_dir($fullPath)) {
                $newTree = new Mputree($fullPath);

                $tree->addNode($newTree);

                $this->_prepareFileTree($fullPath, $newTree);
            } else {
                if (File::isPhpFile($file)) {
                    $newTree = new Mputree($fullPath);
                    $newTree->setFilename($file);
                    $newTree->setExecTime(Settings::DEFAULT_EXEC_TIME_SEC);

                    $tree->addNode($newTree);
                }
            }
        }
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

}
