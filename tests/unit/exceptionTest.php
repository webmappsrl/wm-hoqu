<?php
use PHPUnit\Framework\TestCase;

class exceptionTest extends TestCase
{
    public function testClassExists()
    {
        $this->assertTrue(class_exists('hoquException'));
        $this->assertTrue(class_exists('hoquExceptionDB'));
    }

}

