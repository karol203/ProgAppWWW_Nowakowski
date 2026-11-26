<?php
$dbHost = '127.0.0.1';
$dbPort = 3306;
$dbName = 'moja_strona';
$dbUser = 'uzupelnij_uzytkownika';
$dbPass = 'uzupelnij_haslo';
$dbCharset = 'utf8mb4';

$dsn = "mysql:host=$dbHost;port=$dbPort;dbname=$dbName;charset=$dbCharset";
$opcjePdo = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

$pdo = new PDO($dsn, $dbUser, $dbPass, $opcjePdo);