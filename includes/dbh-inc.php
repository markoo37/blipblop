<?php

require_once 'loadenv.php';
loadEnv(__DIR__ . '/../.env');

function getConnection() {

    $db_username = getenv('DB_USERNAME');
    $db_password = getenv('DB_PASSWORD');
    
    //.env mukodes teszt
    //echo "User: " . $db_username . "<br>";
    //echo "Pass: " . $db_password . "<br>";

    $dsn = "oci:dbname=//localhost:1521/xe;charset=AL32UTF8";

    try {

        $conn = new PDO($dsn, $db_username, $db_password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;

    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
}

