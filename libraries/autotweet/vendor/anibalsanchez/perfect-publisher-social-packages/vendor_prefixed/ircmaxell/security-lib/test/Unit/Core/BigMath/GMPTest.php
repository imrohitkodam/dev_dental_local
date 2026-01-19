<?php /* This file has been prefixed by <PHP-Prefixer> for "XT Social Libraries" */

require_once __DIR__ . '/../BigMathTest.php';

class Unit_Core_BigMath_GMPTest extends Unit_Core_BigMathTest {
    
    protected static $mathImplementations = array();
    
    protected function setUp() {
        if (!extension_loaded('gmp')) {
            $this->markTestSkipped('BCMath is not loaded');
        }
    }
    
    /**
     * @dataProvider provideAddTest
     */
    public function testAdd($left, $right, $expected) {
        $obj = new \XTS_BUILD\SecurityLib\BigMath\GMP;
        $this->assertEquals($expected, $obj->add($left, $right));
    }
    
    /**
     * @dataProvider provideSubtractTest
     */
    public function testSubtract($left, $right, $expected) {
        $obj = new \XTS_BUILD\SecurityLib\BigMath\GMP;
        $this->assertEquals($expected, $obj->subtract($left, $right));
    }
}