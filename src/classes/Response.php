<?php

class Response {
    private $data;
    private $error;
    private $status;

    function __construct() {
        $this -> data = null;
        $this -> error = null;
        $this -> status = 200;
    }

    /**
     *  Getters e Setters
     */

    function getData() {
        return $this -> data;
    }

    function getError() {
        return $this -> error;
    }

    function getStatus() {
        return $this -> status;
    }

    function setData($data) {
        $this -> data = $data;
    }

    function setError($error) {
        $this -> error = $error;
    }

    function setStatus($status) {
        $this -> status = $status;
    }

    /**
     *  Métodos
     */

    function hasError() {
        return $this -> status >= 400 && $this -> status < 600 ? true : false;
    }

    function json() {
        $response = array(
            "status" => $this -> status,
            "error" => $this -> error,
            "data" => $this -> data
        );
        
        if (!$this -> data) {
            unset($response['data']);
        }
        
        if (!$this -> error) {
            unset($response['error']);
        }

        return json_encode($response);
    }
}