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

$username = trim($_POST["username"]);
$email = trim($_POST["email"]);

$password = $_POST["password"];
$passwordConfirm = $_POST["passwordConfirm"];

/**
 *  Verificar se as senhas coincidem
 */

if ($password !== $passwordConfirm) {
    $res -> setStatus(400);
    $res -> setError("Passwords doesn't match");

    exit($res -> json());
}

/**
 *  Verificar se os dados inseridos já estão no banco
 */

include_once "../classes/Database/Connection.php";
include_once "../classes/Database/Query.php";

$conn = new Connection("root", "");
$conn -> start();

$query = new Query($conn -> connectionString);

$data = $query -> raw(
    "select user.username ".
    "from user ".
    "where user.username = '".$username."';"
);

if ($data -> num_rows > 0) {
    $res -> setStatus(400);
    $res -> setError("This username are already in use");

    exit($res -> json());
}

unset($data);

$data = $query -> raw(
    "select user.email ".
    "from user ".
    "where user.email = '".$email."';"
);

if ($data -> num_rows > 0) {
    $res -> setStatus(400);
    $res -> setError("This email are already in use");

    exit($res -> json());
}

/**
 *  Cadastrar os dados no banco
 */

$queryString =
    "insert into user (username, email, password) ".
    "values ('".$username."', '".$email."', '".password_hash($password, PASSWORD_BCRYPT)."');";

if (!$query -> raw($queryString)) {
    $res -> setStatus(500);
    $res -> setError('Occurred an server error');
}

/**
 *  Encerrar conexão e enviar resposta em JSON ao front-end
 */

$conn -> stop();

exit($res -> json());
