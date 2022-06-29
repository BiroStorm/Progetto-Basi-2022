<?php
include '../utilities/databaseSetup.php';
session_start();
if (!isset($_SESSION['authorized'])) {
    header('HTTP/1.1 403 Unauthorized');
    exit;
}

if(!isset($_POST["Message"], $_POST["SessionID"]) || empty($_POST["Message"])){
    header('HTTP/1.1 500');
    exit;
}

$message = strip_tags($_POST["Message"]);
$username = $_SESSION["username"];
$sessione = $_POST["SessionID"];
$time = date("H:i:s",time());

try {
    $sql = "CALL InserisciMessaggio(?, ?, ?, ?)";
    $res = $pdo->prepare($sql);
    $res->bindValue(1, $sessione, PDO::PARAM_INT);
    $res->bindValue(2, $username, PDO::PARAM_STR);
    $res->bindValue(3, $time);
    $res->bindValue(4, $message, PDO::PARAM_STR);
    $res->execute();
} catch (PDOException $e) {
    echo ("[ERRORE] InserisciMessaggio non riuscita. Errore: " . $e->getMessage());
    header('HTTP/1.1 500');
    exit;
}
