<?php

use bublak\phpunitmultirunner\Tree\Helpers\File;

class FileTest extends PHPUnit_Framework_TestCase {

    public function testIsPhpFileNotPhpFiles() {
        $result = File::isPhpFile('');
        self::assertFalse($result);

        $result = File::isPhpFile(null);
        self::assertFalse($result);

        $result = File::isPhpFile('/cesta/');
        self::assertFalse($result);

        $result = File::isPhpFile('/cesta/file.phpp');
        self::assertFalse($result);

        // TODO -> this should not assert
        //$result = File::isPhpFile('/cesta/.php');
        //self::assertTrue($result);
    }

    public function testIsPhpFileSuccess() {
        $result = File::isPhpFile('/cesta/file.php');
        self::assertTrue($result);
    }

    public function testGetDirContentEmptyDirName() {
        $result = File::getDirContent('');

        self::assertFalse($result); // scandir(): Directory name cannot be empty

    }

    public function testGetDirContent() {
        $result = File::getDirContent('tests_for_tests');

        $expectedResult = array (
            0 => 'AFolder',
            1 => 'ATest.php',
            2 => 'BFolder',
            3 => 'BTest.php',
            4 => 'CTest.php',
            5 => 'DTest.php'
        );

        self::assertEquals($expectedResult, $result);
    }

}
