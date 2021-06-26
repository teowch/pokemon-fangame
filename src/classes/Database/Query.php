<?php

class Query {
    private $connection;

    function __construct($connection) {
        $this -> connection = $connection;
    }

    /**
     *  Getters e Setters
     */

    function getConnection() {
        return $this -> connection;
    }

    function setConnection($conection) {
        $this -> connection = $connection;
    }

    /**
     *  Métodos
     */
    
    function raw($queryString) {
        return mysqli_query($this -> connection, $queryString);
    }
}