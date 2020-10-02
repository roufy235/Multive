<?php
if (!in_array($_SERVER['REMOTE_ADDR'], REMOTE_ADDR)) {
    require_once 'access/Online.php';
} else {
    require_once 'access/LocalAccess.php';
}

$GLOBALS = [
    // connection
    'localhost' => 'localhost', 'dbName' => DB_NAME, 'user' => USER, 'password' => PASSWORD,

    //multiveMailer
    'phpmailerHost' => 'example.com',
    'phpmailerPort' => '465',
    'phpmailerUsername' => '',
    'phpmailerPassword' => '',
];
