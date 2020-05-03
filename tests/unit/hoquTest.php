<?php
use PHPUnit\Framework\TestCase;

class HoquTest extends TestCase
{
    public function testClassExists()
    {
        $this->assertTrue(class_exists('hoqu'));
    }

    public function testSingleton() {
        $h1 = hoqu::Instance();
        sleep(2);
        $h2 = hoqu::Instance();
        $this->assertEquals($h1->getStart(),$h2->getStart());
    }
}

