<?php /* This file has been prefixed by <PHP-Prefixer> for "XT Social Libraries" */

use XTS_BUILD\SecurityLib\Strength;

class Unit_Core_StrengthTest extends PHPUnit_Framework_TestCase {

    public function testConstruct() {
        $obj = new Strength(Strength::LOW);
        $this->assertTrue($obj instanceof \XTS_BUILD\SecurityLib\Strength);
        $this->assertTrue($obj instanceof \XTS_BUILD\SecurityLib\Enum);
    }

    public function testGetConstList() {
        $obj = new Strength();
        $const = $obj->getConstList();
        $this->assertEquals(array(
            'VERYLOW' => 1,
            'LOW' => 3,
            'MEDIUM' => 5,
            'HIGH' => 7,
        ), $const);
    }

    public function testGetConstListWithDefault() {
        $obj = new Strength();
        $const = $obj->getConstList(true);
        $this->assertEquals(array(
            '__DEFAULT' => 1,
            'VERYLOW' => 1,
            'LOW' => 3,
            'MEDIUM' => 5,
            'HIGH' => 7,
        ), $const);
    }
}