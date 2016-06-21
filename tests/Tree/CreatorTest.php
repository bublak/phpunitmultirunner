<?php

use bublak\phpunitmultirunner\Tree\Creator;

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
}
