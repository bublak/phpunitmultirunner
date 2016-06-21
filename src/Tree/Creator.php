<?php

namespace bublak\phpunitmultirunner\Tree;

use bublak\phpunitmultirunner\Tree\Helpers\File;
use bublak\phpunitmultirunner\Settings;

class Creator {

    private $_path = '';

    public function __construct($fullPath) {
        $this->_path = $fullPath;
    }

    public function getTree() {
        $tree = new Mputree($this->_path);

        $this->_prepareFileTree($this->_path, $tree);

        $this->_preprocessTree($tree);

        return $tree;
    }

    public function getTreeArray() {
        $tree = $this->getTree();
        $tests = $this->_getTestsArray($tree, false);

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