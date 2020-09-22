<?php

if (!in_array($_SERVER['REMOTE_ADDR'], REMOTE_ADDR)) {
    require_once 'access/Online.php';
    //die($_SERVER['REMOTE_ADDR']);
} else {
    require_once 'access/LocalAccess.php';
}

$GLOBALS = [
    // connection
    'localhost' => 'localhost', 'dbName' => DB_NAME, 'user' => USER, 'password' => PASSWORD,
];
