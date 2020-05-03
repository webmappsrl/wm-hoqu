<?php
/*
 * Main class
 */
final class hoqu {
    private $start;
    public static function Instance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new hoqu();
        }
        return $inst;
    }

    private function __construct()
    {
        // Set start time
        $this->start=time();

        // READ configuration file

        // CONNECT TO DB


    }

    public function getStart() {
        return $this->start;
    }

}