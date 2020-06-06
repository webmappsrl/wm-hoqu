<?php
/*
 * Main class
 */
final class hoqu {

    /**
     * @var Connection to MYSQL DB
     */
    private $db;

    /**
     * @var array Array containing all configuration settings, constructor reads data form config.json
     */
    private $configuration = array();

    /**
     * @return hoqu|null Singleton implementation
     */
    public static function Instance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new hoqu();
        }
        return $inst;
    }

    /**
     * hoqu constructor.
     */
    private function __construct()
    {
        // Set start time
        $this->start=time();

        // READ configuration file
        $this->configuration=json_decode(file_get_contents(__DIR__.'/../config.json'),true);

        // CONNECT TO DB
        $db = $this->configuration['mysql'];
        $this->db = new mysqli($db['host'],$db['user'],$db['password'],$db['db']);

        // Check if table queue exists, if not create it
        if(!$this->tableQueueExists()) {
            // Create TABLE
            $this->createQueue();
        }

    }

    /**
     * @return array Getter for configuration property
     */
    public function getConfiguration() {
        return $this->configuration;
    }

    /**
     * Return array with number of items in queue (new, processing, completed, error)
     * @return array
     */
    public function getStatus() {
        //array('new'=>,'processing'=>,'completed'=>,'error'=>);
        $q = 'select process_status,count(*) from queue group by process_status';
        $r = $this->db->query($q);
        if($r->num_rows==0) return array('new'=>0,'processing'=>0,'completed'=>0,'error'=>0);
        $s = array();
        while($stats = $r->fetch_row()) {
            $s[$stats[0]]=$stats[1];
        }
        foreach (array('new','processing','completed','error') as $status) {
            if(!isset($s[$status])) $s[$status]=0;
        }
        return $s;
    }

    public function getInfo() {
        $info = array();
        $info['version'] = constant('HOQU_VERSION');
        $info['mysql'] = $this->db->server_version;
        $info['php'] = phpversion();
        $info['queue_fields']=implode(',',$this->getQueueFields());
        return json_encode($info);
    }

    /**
     * Add new item to queue, returns queue ID
     * @param $instance
     * @param $task
     * @param $parameters
     * @return mixed
     * @throws hoquExceptionDB
     */
    public function add($instance,$task,$parameters) {
        // TODO: check parameters validity

        $instance = $this->db->real_escape_string($instance);
        $task = $this->db->real_escape_string($task);
        $parameters = $this->db->real_escape_string($parameters);
        $q = "INSERT INTO queue (instance,task,parameters) VALUES ('$instance','$task','$parameters')";
        $r = $this->db->query($q);
        if(!$r) {
            throw new hoquExceptionDB($this->db->error);
        }
        return $this->db->insert_id;
    }

    /**
     * Process Next item: get next new item set process_status to processing, add time to start_process in process_log
     * @return mixed|null
     * @throws hoquExceptionDB
     */
    public function processNext() {
        // Get next
        $q = "SELECT id FROM QUEUE WHERE process_status='new' ORDER BY created_at ASC LIMIT 1";
        $r = $this->db->query($q);
        if(!$r) {
            throw new hoquExceptionDB($this->db->error);
        }
        if($r->num_rows==0) return null;
        $data = $r->fetch_assoc();
        $id = $data['id'];

        // Set status processing and Add time to start_process in process_log
        $log = $this->db->real_escape_string(json_encode(array('start_process'=>date('Y-m-d H:i:s'))));
        $q = "UPDATE queue SET process_status='processing',process_log='$log' WHERE id=$id";
        $r = $this->db->query($q);
        if(!$r) {
            throw new hoquExceptionDB($this->db->error);
        }

        return $id;
    }

    /**
     * Return item queue by id (false if no item found)
     * @param $id
     * @return mixed
     * @throws hoquExceptionDB
     * @throws hoquExceptionDBNoID
     */
    public function getQueue($id) {
        $q = "SELECT * from queue where id=$id";
        $r = $this->db->query($q);
        if(!$r) {
            throw new hoquExceptionDB($this->db->error);
        }
        if($r->num_rows==0) throw new hoquExceptionDBNoID("NOID $id");
        return $r->fetch_assoc();
    }

    /**
     * Check if db table QUEUE exists (return true) or not (return false)
     * @return bool
     */
    private function tableQueueExists() {
        $tables = array_column($this->db->query('SHOW TABLES')->fetch_all(),0);
        return in_array('queue',$tables);
    }

    /**
     * Get array with queue table's name
     * @return array
     */
    public function getQueueFields() {
        $q = 'DESCRIBE queue';
        return array_column($this->db->query($q)->fetch_all(),0);
    }

    /**
     * Removes all queue items
     * @return bool
     * @throws hoquExceptionDB
     */
    public function cleanQueue() {
        $q='DELETE FROM queue';
        if(!$this->db->query($q)) {
            throw new hoquExceptionDB($this->db->error);
        }
        return true;
    }

    /**
     * Creates DB queue table
     * @return bool
     * @throws hoquExceptionDB
     */
    private function createQueue() {
        $q = <<<EOQ
create table queue (
  id integer not null auto_increment primary key,
  instance varchar(256),
  task varchar(256),
  parameters blob,
  created_at timestamp,
  process_status varchar(256) DEFAULT 'new',
  process_log blob
);
EOQ;
    if(!$this->db->query($q)) {
        throw new hoquExceptionDB($this->db->error);
    }
    return true;
    }
}