<?php

try {
    include './utilities/credentials.php';
    $pdo = new PDO('mysql:host=' . $dbAdress . ';dbname=' . $dbName, $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo ("[ERRORE] Connessione al DB non riuscita. Errore: " . $e->getMessage());
    exit();
}
