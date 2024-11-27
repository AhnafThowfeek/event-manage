<?php

define("DATABASE_HOST", 'localhost');
define("DATABASE_NAME", 'event_pro');
define("DATABASE_USER", 'root');
define("DATABASE_PASSWORD", '');

function connection() {
    $db = new PDO("mysql:host=" . DATABASE_HOST . ";dbname=" . DATABASE_NAME, DATABASE_USER, DATABASE_PASSWORD);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
}
