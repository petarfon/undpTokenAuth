<?php

require('db.php');
require('../model/Response.php');

$conn = DB::connectDB();

// ruta 1: /sessions/3  DELETE
// ruta 2: /sessions/4  PATCH
// ruta 3: /sessions    POST

if (isset($_GET['sessionid'])) {
    // implementiramo DELETE i PATCH
} else {
    // implementiramo POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $response = new Response();
        $response->addMessage('Only POST method is allowed');
        $response->send();
        exit;
    }

    $rawPostData = file_get_contents('php://input');
    $jsonData = json_decode($rawPostData);

    // provera podataka
    if (!isset($jsonData->username) || !isset($jsonData->password)) {
        $response = new Response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        $response->addMessage('Missing username or password');
        $response->send();

        exit;
    }

    if (strlen($jsonData->username) < 1 || strlen($jsonData->password) < 5) {
        $response = new Response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        $response->addMessage('Username or password not long enough');
        $response->send();

        exit;
    }

    $username = trim($jsonData->username);
    $password = $jsonData->password;

    try {
        $query = "SELECT id, fullname, username, password, useractive, loginattempts FROM tblusers WHERE username='$username'";
        $result = $conn->query($query);
        $rowCount = mysqli_num_rows($result);
        if ($rowCount === 0) {
            $response = new Response();
            $response->setHttpStatusCode(409);
            $response->setSuccess(false);
            $response->addMessage('Username or password is incorrect'); // dobra praksa
            $response->send();

            exit;
        }
        $row = $result->fetch_assoc();
        $db_id = $row['id'];
        $db_fullname = $row['fullname'];
        $db_username = $row['username'];
        $db_password = $row['password'];
        $db_useractive = $row['useractive'];
        $db_loginattempts = $row['loginattempts'];

        if (!password_verify($password, $db_password)) {
            //implmentirati login attempts
            $response = new Response();
            $response->setHttpStatusCode(409);
            $response->setSuccess(false);
            $response->addMessage('Username or password is incorrect'); // dobra praksa
            $response->send();

            exit;
        }
        //uspesan login
        //treba da pokrenemo sesiju
    } catch (Exception $ex) {
        //implementirati
    }
}
