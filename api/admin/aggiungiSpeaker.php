<?php
include '../../utilities/databaseSetup.php';
session_start();
if (isset($_SESSION['authorized'])) {
    if (!strcmp("Admin", $_SESSION["role"]) == 0) {
        header('Location: /403.php');
        exit;
    }
} else {
    header("Location: /login.php");
    exit;
}
if (!isset($_POST["username"], $_POST["codice"]) || empty($_POST["username"]) || empty($_POST["codice"])) {
    header('Location: /errorPage.php?error="Problema con i valori del POST"');
    exit;
}
$username = $_POST["username"];
$codice = $_POST["codice"];


$sql = 'CALL AggiungiSpeaker(?, ?)';
try {
    $st = $pdo->prepare($sql);
    $st->bindParam(1, $codice);
    $st->bindValue(2, $username);
    $st->execute();

    header("Location: /conferenze/tutorial.php?Codice=$codice");
    exit;
} catch (PDOException $e) {
    echo ("[ERRORE] Query SQL (Select) non riuscita. Errore: " . $e->getMessage());
    exit;
}

