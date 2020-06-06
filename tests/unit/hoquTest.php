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

    public function testGetInfo() {
        $h = hoqu::Instance();
        $info = json_decode($h->getInfo(),TRUE);
        $this->assertTrue(isset($info['version']));
        $this->assertTrue(isset($info['mysql']));
        $this->assertTrue(isset($info['php']));
        $this->assertTrue(isset($info['queue_fields']));
        $this->assertEquals('id,instance,task,created_at,process_status,process_log',$info['queue_fields']);
    }

    public function testQueueTable() {
        $h = hoqu::Instance();
        $fields = $h->getQueueFields();
        $this->assertTrue(in_array('id',$fields));
        $this->assertTrue(in_array('instance',$fields));
        $this->assertTrue(in_array('task',$fields));
        $this->assertTrue(in_array('created_at',$fields));
        $this->assertTrue(in_array('process_status',$fields));
        $this->assertTrue(in_array('process_log',$fields));
    }
}

