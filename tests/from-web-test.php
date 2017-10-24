<?php
/**
 * Created by PhpStorm.
 * User: bryce
 * Date: 10/22/2017
 * Time: 12:30 PM
 */

require_once('../vendor/phpunit-6.4.3.phar');
class from_web_test extends PHPUnit_Framework_TestCase {

    public function testTest() {
        $this->assertEquals(true,true);
    }
}