<?php
include '../../utilities/databaseSetup.php';
session_start();
if (isset($_SESSION['authorized'])) {
    if (!strcmp("Speaker", $_SESSION["role"]) == 0) {
        header('Location: /403.php');
        exit;
    }
} else {
    header("Location: /login.php");
    exit;
}

if(!isset($_GET["Link"], $_GET["Codice"]) || empty($_GET["Link"]) || empty($_GET["Codice"])){
    header('Location: /errorPage.php?error="Problema con i valori del POST"');
    exit;
}



$sql = "CALL RimuoviRisorsa(?, ?)";
try {
    $st = $pdo->prepare($sql);
    $st->bindValue(1, $_GET["Link"]);
    $st->bindValue(2, $_GET["Codice"]);
    $st->execute();
    header('Location: /conferenze/tutorial.php?Codice='.$_GET["Codice"]);
} catch (PDOException $e) {
    echo ("[ERRORE] Stored Procedure (RimuoviRisorsa) non riuscita. Errore: " . $e->getMessage());
    exit;
}
