<?php

use bublak\phpunitmultirunner\Tree\Creator;
use bublak\phpunitmultirunner\Tree\Mputree;

class CreatorTest extends PHPUnit_Framework_TestCase {

    public function testGetTreeArray() {
        $path = 'tests_for_tests';

        $creator = new Creator($path);

        $result = $creator->getTreeArray();

        $expectedResult = array(
            "tests_for_tests/DTest.php",
            "tests_for_tests/CTest.php",
            "tests_for_tests/BTest.php",
            "tests_for_tests/BFolder/FTest.php",
            "tests_for_tests/BFolder/DFolder/GTest.php",
            "tests_for_tests/BFolder/DFolder/ETest.php",
            "tests_for_tests/ATest.php",
            "tests_for_tests/AFolder/CFolder/HTest.php"
        );

        self::assertEquals($expectedResult, $result);
    }

    public function testSaveTree() {
        $path = 'tests_for_tests/BFolder';

        $creator = new Creator($path);

        $tree = $creator->getTree();

        $result = $creator->save('file_for_test.data', $tree);

        self::assertTrue($result);
        //[TODO] do this -> check file
        //TODO -> delete file
    }

    public function testLoadTree() {
        $path = 'tests_for_tests/BFolder';

        //TODO -> create the file,
        $creator = new Creator($path);

        $result = $creator->load('file_for_test.data');

        self::assertEquals($this->_getObjectsOfBFolder(), $result);
        //TODO -> delete file
    }


    public function testGetTreeObjects() {
        $path = 'tests_for_tests/BFolder';

        $creator = new Creator($path);

        $result = $creator->getTree();

        self::assertEquals($this->_getObjectsOfBFolder(), $result);
    }

    private function _getObjectsOfBFolder() {

        $root = new Mputree('tests_for_tests/BFolder', true);
        $root->setExecTime(15);

        $fTest = new Mputree('tests_for_tests/BFolder/FTest.php');
        $fTest->setExecTime(5);
        $fTest->setFilename('FTest.php');

        $eTest = new Mputree('tests_for_tests/BFolder/DFolder/ETest.php');
        $eTest->setExecTime(5);
        $eTest->setFilename('ETest.php');

        $gTest = new Mputree('tests_for_tests/BFolder/DFolder/GTest.php');
        $gTest->setExecTime(5);
        $gTest->setFilename('GTest.php');

        $dFolder = new Mputree('tests_for_tests/BFolder/DFolder');
        $dFolder->setExecTime(10);

        $dFolder->addNode($eTest);
        $dFolder->addNode($gTest);

        $root->addNode($dFolder);
        $root->addNode($fTest);

        return $root;
    }
}
