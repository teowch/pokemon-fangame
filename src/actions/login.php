<?php

/**
 *  Criar objeto de resposta para o front-end
 */

include_once "../classes/Response.php";

header("Content-Type: application/json");
$res = new Response();

/**
 *  Verificar se a requisão foi por meio do método POST
 */

if (empty($_POST)) {
    $res -> setStatus(400);
    $res -> setError('No POST method detected');

    exit($res -> json());
}

/**
 *  Atribuir dados a variáveis
 */

$user = trim($_POST["user"]);
$password = $_POST["password"];

/**
 *  Verificar se foi inserido email ou usuario
 */

$usingEmail = strrpos($user, "@");

!is_bool($usingEmail) && $usingEmail >= 0 ? $column = "email" : $column = "username";

/**
 *  Solicitar dados do usuario ao banco
 */

include_once "../classes/Database/Connection.php";
include_once "../classes/Database/Query.php";
include_once "../classes/Session.php";

$conn = new Connection("root", "");
$conn -> start();

$query = new Query($conn -> connectionString);

$data = $query -> raw(
    "select user.user_id, user.password ".
    "from user ".
    "where user.".$column." = '".$user."' ".
    "limit 1;"
);

if ($data -> num_rows > 0) {
    $row = $data -> fetch_assoc();

    /**
     *  Verificar se as senhas coincidem e iniciar sessão
     */

    if (password_verify($password, $row["password"])) {
        $session = new Session($conn -> connectionString, $row["user_id"]);

        $session -> start();
    } else {
        $res -> setStatus(400);
        $res -> setError("Wrong password");
    }
} else {
    $res -> setStatus(400);
    $res -> setError("User '$user' not found");
}

/**
 *  Encerrar conexão e enviar resposta em JSON ao front-end
 */

$conn -> stop();

exit($res -> json());
