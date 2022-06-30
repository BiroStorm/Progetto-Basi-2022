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

if(!isset($_POST["Link"], $_POST["Descrizione"], $_POST["Codice"]) || empty($_POST["Link"])){
    header('Location: /errorPage.php?error="Problema con i valori del POST"');
    exit;
}

$sql = "SELECT 1 FROM Insegnamento WHERE Username = ? AND CodiceTutorial = ?";
try {
    $st = $pdo->prepare($sql);
    $st->bindValue(1, $_SESSION["username"]);
    $st->bindValue(2, $_POST["Codice"]);
    $st->execute();
    if ($st->rowCount() == 0) {
        header('Location: /errorPage.php?error="Non risulti assegnato a questo Tutorial"');
        exit;
    }
} catch (PDOException $e) {
    echo ("[ERRORE] Query SQL (Select) non riuscita. Errore: " . $e->getMessage());
    exit;
}


$sql = "CALL AggiungiRisorsa(?, ?, ?, ?)";
try {
    $st = $pdo->prepare($sql);
    $st->bindValue(1, $_POST["Link"]);
    $st->bindValue(2, $_POST["Codice"]);
    $st->bindValue(3, $_POST["Descrizione"]);
    $st->bindValue(4, $_SESSION["username"]);
    $st->execute();

    // INSERIMENTO LOG IN MONGO
    include_once "../../utilities/mongoDBSetup.php";
    $mongodb->Presentazione->insertOne(
        [
            "action" => "New Risorsa",
            "user" => $_SESSION["username"],
            "presentazione" => $_POST["Codice"],
            "link" => $_POST["Link"],
            "descrizione" => $_POST["Descrizione"],
            "data" => date("Y-m-d H:i:s",time())
        ]
    );
    // END LOG IN MONGO;

    header('Location: /conferenze/tutorial.php?Codice='.$_POST["Codice"]);
} catch (PDOException $e) {
    echo ("[ERRORE] Stored Procedure (AggiungiRisorsa) non riuscita. Errore: " . $e->getMessage());
    exit;
}
