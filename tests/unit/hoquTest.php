<?php
use PHPUnit\Framework\TestCase;

class HoquTest extends TestCase
{
    public function testClassExists()
    {
        $this->assertTrue(class_exists('hoqu'));
    }
}

