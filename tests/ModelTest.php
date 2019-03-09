<?php
use CloudsDotEarth\Bundles\SwooleTests\{Diff, Test};

/**
 * A simple test sample
 * Class ItemTest
 */
class ItemTest extends Test {

    public function aLittleTestCase() {

        $this->assertTrue(true);
        $this->assert(array(0), "===", array(0));
        $this->assert(array(1), "===", array(1));
        $this->assert(array('1'), "!==", array(1));
        $this->assert(array('1'), "==", array(1));
        $this->assert(new stdClass(), "!=", new Test());

    }

    public function anOtherCase() {
        $this->assertTrue(true);
    }
}
