<?php
// database.php — MySQL PDO connection singleton for TurfHub

class Database {
    private static ?PDO $instance = null;

    private const HOST = 'localhost';
    private const DB   = 'turfhub';
    private const USER = 'root';
    private const PASS = '';            // Set your MySQL password
    private const PORT = 3306;

    public static function connect(): PDO {
        if (self::$instance === null) {
            $dsn = 'mysql:host=' . self::HOST
                 . ';port=' . self::PORT
                 . ';dbname=' . self::DB
                 . ';charset=utf8mb4';

            self::$instance = new PDO($dsn, self::USER, self::PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }
        return self::$instance;
    }
}
