<?php
/*
 * Main class
 */
final class hoqu {

    /**
     * @var int Timestamp containing initial start execution time
     */
    private $start;

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
     * @return int Getter for start property
     */
    public function getStart() {
        return $this->start;
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