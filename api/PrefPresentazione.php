<?php
if (!isset($_POST["Codice"], $_POST["add"])) {
    echo "manca codice e add";
    exit;
}
if (!isset($_COOKIE["PHPSESSID"])) {
    echo "NON C'è LA SESSIONE!";
    exit;
}
session_start();
$username = $_SESSION["username"];
include '../utilities/databaseSetup.php';
if ($_POST["add"] == 0) {
    $sql = 'CALL RimuoviPresentazionePreferita(?, ?)';
} else {
    $sql = 'CALL InserisciPresentazionePreferita(?, ?)';
}
try {
    $st = $pdo->prepare($sql);
    $st->bindParam(1, $username);
    $st->bindValue(2, $_POST["Codice"]);
    $st->execute();
    exit;
} catch (PDOException $e) {
    echo ("Risulta già tra i preferiti!");
    exit;
}
