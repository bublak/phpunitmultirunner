<?php

namespace bublak\phpunitmultirunner\Tree;

class Mputree {
    private $_nodes = array();

    private $_isRoot   = false;
    private $_fullPath = null;
    private $_filename = null;

    private $_execTimeRating = null;
    private $_execTime       = null;

    public function __construct($fullPath, $isRoot=false) {
        $this->_fullPath = $fullPath;
        $this->_isRoot = $isRoot;
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

    public function getExecTimeRating() {
        return $this->_execTimeRating;
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

    public function save($file) {
        if ($this->_isRoot === TRUE) {
            $res = serialize($this);

            $fileHandler = fopen($file, 'w');
            fwrite($fileHandler, $res);
            fclose($fileHandler);
        } else {
            return false;
        }

        return true;
    }
}
