<?php
use PHPUnit\Framework\TestCase;

class hoquTest extends TestCase
{
    public function testClassExists()
    {
        $this->assertTrue(class_exists('hoqu'));
    }

    /**
     * Add $n*4 items ($n new, $n process, $n completed, $n error) in queue according to the queue model
     *
     * {
         "instance": "https:\/\/montepisanotree.org",
         "task": "mptupdatepoi",
            "parameters": {
                 "id": 1987
             }
      }
     *
     * @param int $n
     */
    private function mockDB($n=10) {
        $h = hoqu::Instance();
        $h->cleanQueue();

        // Add $n New
        for ($i = 0; $i < $n; $i++) {
            $instance = "$i".'.new.test.instance.it';
            $id = $h->add($instance,'testTask','{"testpar" : "testparval"}');
        }

        // TODO Add $n process
        // TODO Add $n completed
        // TODO Add $n error
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
        $this->assertEquals('id,instance,task,parameters,created_at,process_status,process_log',$info['queue_fields']);
    }

    public function testQueueTable() {
        $h = hoqu::Instance();
        $fields = $h->getQueueFields();
        $this->assertTrue(in_array('id',$fields));
        $this->assertTrue(in_array('instance',$fields));
        $this->assertTrue(in_array('task',$fields));
        $this->assertTrue(in_array('parameters',$fields));
        $this->assertTrue(in_array('created_at',$fields));
        $this->assertTrue(in_array('process_status',$fields));
        $this->assertTrue(in_array('process_log',$fields));
    }

    public function testGetStatus() {

        $this->mockDB();

        $h = hoqu::Instance();
        $s = $h->getStatus();
        $this->assertEquals(10,$s['new']);
        $this->assertEquals(10,$s['processing']);
        $this->assertEquals(10,$s['completed']);
        $this->assertEquals(10,$s['error']);
    }

    public function testCleanQueue() {
        $h = hoqu::Instance();
        $h->cleanQueue();
        $s = $h->getStatus();
        $this->assertEquals(0,$s['new']);
        $this->assertEquals(0,$s['processing']);
        $this->assertEquals(0,$s['completed']);
        $this->assertEquals(0,$s['error']);

    }

    public function testAddAndGetQueue() {
        $h = hoqu::Instance();
        $h->cleanQueue();
        $id = $h->add('test.instance.it','testTask','{"testpar" : "testparval"}');

        // Retrieve and check values
        $r = $h->getQueue($id);
        $this->assertTrue(isset($r['id']));
        $this->assertTrue(isset($r['instance']));
        $this->assertTrue(isset($r['task']));
        $this->assertTrue(isset($r['parameters']));
        $this->assertTrue(isset($r['created_at']));
        $this->assertTrue(isset($r['process_status']));
        $this->assertTrue(array_key_exists('process_log',$r));

        $this->assertEquals($id,$r['id']);
        $this->assertEquals('test.instance.it',$r['instance']);
        $this->assertEquals('testTask',$r['task']);
        $p = json_decode($r['parameters'],true);
        $this->assertTrue(array_key_exists('testpar',$p));
        $this->assertEquals('testparval',$p['testpar']);
        $this->assertEquals('new',$r['process_status']);

        // Test status 1,0,0,0
        $s=$h->getStatus();
        $this->assertEquals(1,$s['new']);
        $this->assertEquals(0,$s['processing']);
        $this->assertEquals(0,$s['completed']);
        $this->assertEquals(0,$s['error']);

    }


    public function testProcessNext() {
        $h = hoqu::Instance();
        $h->cleanQueue();
        $id = $h->add('test.instance.it','testTask','{"testpar" : "testparval"}');
        $id_new = $h->processNext();

        $this->assertTrue($id==$id_new);

        // Retrieve and check values
        $r = $h->getQueue($id);
        $this->assertTrue(isset($r['id']));
        $this->assertTrue(isset($r['instance']));
        $this->assertTrue(isset($r['task']));
        $this->assertTrue(isset($r['parameters']));
        $this->assertTrue(isset($r['created_at']));
        $this->assertTrue(isset($r['process_status']));
        $this->assertTrue(array_key_exists('process_log',$r));

        $this->assertEquals($id_new,$r['id']);
        $this->assertEquals('test.instance.it',$r['instance']);
        $this->assertEquals('testTask',$r['task']);
        $p = json_decode($r['parameters'],true);
        $this->assertTrue(array_key_exists('testpar',$p));
        $this->assertEquals('testparval',$p['testpar']);
        $this->assertEquals('processing',$r['process_status']);
        $log = json_decode($r['process_log'],TRUE);
        $this->assertTrue(isset($log['start_process']));

        // Test status 0,1,0,0
        $s=$h->getStatus();
        $this->assertEquals(0,$s['new']);
        $this->assertEquals(1,$s['processing']);
        $this->assertEquals(0,$s['completed']);
        $this->assertEquals(0,$s['error']);

    }
}

