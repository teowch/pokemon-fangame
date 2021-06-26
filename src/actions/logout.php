<?php

include_once "../classes/Database/Connection.php";
include_once "../classes/Session.php";

$conn = new Connection("root", "");
$conn -> start();

session_start();

$session = new Session($conn -> connectionString, $_SESSION['user_id']);
$session -> stop();

$conn -> stop();

header('Location: ../../');
exit;
