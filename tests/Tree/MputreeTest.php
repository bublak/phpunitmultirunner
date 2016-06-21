<?php

use bublak\phpunitmultirunner\Tree\Mputree;

class MputreeTest extends PHPUnit_Framework_TestCase {

    public function testCreateInstance() {
        $path = '../cesta/z/mesta';
        $mputree = new Mputree($path);

        self::assertEquals($path, $mputree->getFullPath());
        self::assertNull($mputree->getFilename());
        self::assertEquals(array(), $mputree->getNodes());
        self::assertNull($mputree->getExecTime());
        self::assertNull($mputree->getExecTimeRating());
    }

    public function testAddNode() {
        $pathRoot = '../cesta/z';
        $mputreeRoot = new Mputree($pathRoot);

        $pathChild = '../cesta/z/mesta';
        $mputreeChild = new Mputree($pathChild);

        $mputreeRoot->addNode($mputreeChild);

        self::assertEquals($pathRoot, $mputreeRoot->getFullPath());
        self::assertNull($mputreeRoot->getFilename());
        self::assertEquals(array($mputreeChild), $mputreeRoot->getNodes());
        self::assertNull($mputreeRoot->getExecTime());
        self::assertNull($mputreeRoot->getExecTimeRating());

        self::assertEquals($pathChild, $mputreeChild->getFullPath());
        self::assertNull($mputreeChild->getFilename());
        self::assertEquals(array(), $mputreeChild->getNodes());
        self::assertNull($mputreeChild->getExecTime());
        self::assertNull($mputreeChild->getExecTimeRating());
    }

}
