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

}
