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

    public function getInfo() {
        $info = array();
        $info['version'] = constant('HOQU_VERSION');
        $info['mysql'] = $this->db->server_version;
        $info['php'] = phpversion();
        return json_encode($info);
    }

}