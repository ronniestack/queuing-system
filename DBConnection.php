<?php

// Define DB directory correctly
$dbDir = __DIR__ . '/db';

if (!is_dir($dbDir)) {
    mkdir($dbDir, 0777, true);
}

// Define constants
if (!defined('db_file')) define('db_file', $dbDir . '/cashier_queuing_db.db');
if (!defined('tZone')) define('tZone', 'Asia/Manila');
if (!defined('dZone')) define('dZone', ini_get('date.timezone'));

// Custom SQLite function
function my_udf_md5($string) {
    return md5($string);
}

class DBConnection extends SQLite3 {

    function __construct() {
        parent::__construct(db_file);

        $this->createFunction('md5', 'my_udf_md5');
        $this->exec("PRAGMA foreign_keys = ON;");

        // USER TABLE
        $this->exec("
            CREATE TABLE IF NOT EXISTS user_list (
                user_id INTEGER PRIMARY KEY AUTOINCREMENT,
                fullname TEXT NOT NULL,
                username TEXT NOT NULL UNIQUE,
                password TEXT NOT NULL,
                status INTEGER NOT NULL DEFAULT 1,
                date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        // CASHIER TABLE
        $this->exec("
            CREATE TABLE IF NOT EXISTS cashier_list (
                cashier_id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                log_status INTEGER NOT NULL DEFAULT 0,
                status INTEGER NOT NULL DEFAULT 1
            )
        ");

        // QUEUE TABLE
        $this->exec("
            CREATE TABLE IF NOT EXISTS queue_list (
                queue_id INTEGER PRIMARY KEY AUTOINCREMENT,
                queue TEXT NOT NULL,
                customer_name TEXT NOT NULL,
                status INTEGER NOT NULL DEFAULT 0,
                date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        // Default admin account
        $this->exec("
            INSERT OR IGNORE INTO user_list 
            (user_id, fullname, username, password, status) 
            VALUES (1, 'Administrator', 'admin', md5('password'), 1)
        ");
    }

    function __destruct() {
        $this->close();
    }
}

// Initialize connection
$conn = new DBConnection();
