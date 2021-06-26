<?php

class Session {
    private $connection;
    private $userID;

    function __construct($connection, $userID) {
        $this -> connection = $connection;
        $this -> userID = $userID;
    }

    /**
     *  Getters e Setters
     */

    function getConnection() {
        return $this -> connection;
    }

    function getUserID() {
        return $this -> userID;
    }

    function setConnection($connection) {
        $this -> connection = $connection;
    }

    function setUserID($userID) {
        $this -> userID = $userID;
    }

    /**
     *  Métodos
     */

    function start() {
        session_start();

        $_SESSION['user_id'] = $this -> userID;

        mysqli_query(
            $this -> connection,
            "insert into online_user (session_id, user_id, logged_at)".
            "values ('".session_id()."', '".$this -> userID."', '".date("Y-m-d H:i:s")."');"
        );
    }

    function stop() {
        mysqli_query(
            $this -> connection,
            "delete from online_user ".
            "where online_user.user_id = '".$this -> userID."';"
        );

        session_unset();
        session_destroy();
    }
}