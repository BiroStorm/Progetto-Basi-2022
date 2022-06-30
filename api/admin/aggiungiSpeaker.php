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
// controlliamo che sia uno Speaker:
$sql = "SELECT 1 FROM Speaker WHERE Username = ?";
try {
    $st = $pdo->prepare($sql);
    $st->bindValue(1, $username);
    $st->execute();
    if ($st->rowCount() == 0) {
        header('Location: /errorPage.php?error="Non è uno Speaker!"');
        exit;
    }
} catch (PDOException $e) {
    echo ("[ERRORE] Query SQL (Select) non riuscita. Errore: " . $e->getMessage());
    exit;
}
// Controlliamo se è già uno Speaker del Tutorial
$sql = "SELECT 1 FROM Insegnamento WHERE Username = ? AND CodiceTutorial = ?";
try {
    $st = $pdo->prepare($sql);
    $st->bindValue(1, $username);
    $st->bindValue(2, $codice);
    $st->execute();
    if ($st->rowCount() > 0) {
        header('Location: /errorPage.php?error="Risulta già come Speaker!"');
        exit;
    }
} catch (PDOException $e) {
    echo ("[ERRORE] Query SQL (Select) non riuscita. Errore: " . $e->getMessage());
    exit;
}


$sql = 'CALL AggiungiSpeaker(?, ?)';
try {
    $st = $pdo->prepare($sql);
    $st->bindParam(1, $codice);
    $st->bindValue(2, $username);
    $st->execute();
    // INSERIMENTO LOG IN MONGO
    include_once "../../utilities/mongoDBSetup.php";
    $mongodb->Presentazione->insertOne(
        [
            "action" => "Associazione Speaker",
            "user" => $_SESSION["username"],
            "presenter" => $username,
            "presentazione" => $codice,
            "data" => date("Y-m-d H:i:s", time())
        ]
    );
    header("Location: /conferenze/tutorial.php?Codice=$codice");
    exit;
} catch (PDOException $e) {
    echo ("[ERRORE] Query SQL (Select) non riuscita. Errore: " . $e->getMessage());
    exit;
}
