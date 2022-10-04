<?php

class Connection {
    public $connectionString;

    protected $path;
    protected $port;
    protected $username;
    protected $password;
    protected $database;

    function __construct($username, $password) {
        $this -> path = 'localhost';
        $this -> port = '3306';
        $this -> username = $username;
        $this -> password = $password;
        $this -> database = 'dev_stadium';
    }

    /**
     *  Métodos
     */

    function start() {
        $this -> connectionString = mysqli_connect(
            $this -> path . ":" . $this -> port,
            $this -> username,
            $this -> password,
            $this -> database
        );

        return $this -> connectionString;
    }

    function stop() {
        $this -> connectionString = null;
    }
}