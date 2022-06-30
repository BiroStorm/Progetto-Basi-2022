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


$sql = 'CALL AggiornaPresenter(?, ?)';
try {
    $st = $pdo->prepare($sql);
    $st->bindParam(1, $codice);
    $st->bindValue(2, $username);
    $st->execute();
    // INSERIMENTO LOG IN MONGO
    include_once "../../utilities/mongoDBSetup.php";
    $mongodb->Presentazione->insertOne(
        [
            "action" => "Associazione Presenter",
            "user" => $_SESSION["username"],
            "presenter" => $username,
            "presentazione" => $codice,
            "data" => date("Y-m-d H:i:s",time())
        ]
    );
    // END LOG IN MONGO;

    header("Location: /conferenze/articolo.php?Codice=$codice");
    exit;
} catch (PDOException $e) {
    echo ("[ERRORE] Stored Procedure (AggiornaPresenter) non riuscita. Errore: " . $e->getMessage());
    exit;
}

