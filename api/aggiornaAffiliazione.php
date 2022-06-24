<?php
include '../utilities/databaseSetup.php';
session_start();
if (isset($_SESSION['authorized'])) {
    if ((!strcmp("Speaker", $_SESSION["role"]) == 0) && (!strcmp("Presenter", $_SESSION["role"]) == 0)) {
        header('Location: /403.php');
        exit;
    }
} else {
    header("Location: /login.php");
    exit;
}

if (!isset($_POST["nomeUni"]) || empty($_POST["dipartimento"])) {
    header('Location: /errorPage.php?error="Problema con i valori del POST"');
    exit;
}



try {
    $sql = 'UPDATE ' . $_SESSION["role"] . ' SET NomeUni = :uninome, Dipartimento = :dip WHERE Username = :usr1';
    $res = $pdo->prepare($sql);
    $res->bindValue(":usr1", $_SESSION["username"]);
    $res->bindValue(":uninome", $_POST["nomeUni"]);
    $res->bindValue(":dip", $_POST["dipartimento"]);
    $res->execute();
    header('Location: /user/modificaProfilo.php');
} catch (PDOException $e) {
    echo ("[ERRORE] Update Affiliazione Università non riuscita. Errore: " . $e->getMessage());
    exit;
}
