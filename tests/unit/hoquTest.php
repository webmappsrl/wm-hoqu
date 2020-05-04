<?php
use PHPUnit\Framework\TestCase;

class hoquTest extends TestCase
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

    public function testConfig() {
        $h = hoqu::Instance();
        $conf = $h->getConfiguration();
        $this->assertTrue(isset($conf['mysql']));
        $this->assertTrue(isset($conf['mysql']['host']));
        $this->assertTrue(isset($conf['mysql']['db']));
        $this->assertTrue(isset($conf['mysql']['user']));
        $this->assertTrue(isset($conf['mysql']['password']));
    }
}

