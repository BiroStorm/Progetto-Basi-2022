<?php
include '../../utilities/databaseSetup.php';
session_start();
if (isset($_SESSION['authorized'])) {
    if (!strcmp("Admin", $_SESSION["role"]) == 0) {
        header('Location: /403.php');
        exit;
    }
} else {
    header("Location: /login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

if(!isset($_POST["voto"], $_POST["note"], $_POST["Codice"]) || empty($_POST["voto"])){
    header('Location: /errorPage.php?error="Problema con i valori del POST"');
    exit;
}

// valutazione presentazione
$sql = "SELECT 1 FROM Valutazione WHERE UsernameAdmin = ? AND CodPresentazione = ?";
try {
    $st = $pdo->prepare($sql);
    $st->bindValue(1, $_SESSION["username"], PDO::PARAM_STR);
    $st->bindValue(2, $_POST["Codice"], PDO::PARAM_INT);
    $st->execute();
    
} catch (PDOException $e) {
    echo ("[ERRORE] Query SQL (get Valutazione) non riuscita. Errore: " . $e->getMessage());
    exit;
}
if ($st->rowCount() == 1) {
    header('Location: /errorPage.php?error="Hai già votato per questa Presentazione!"');
    exit;
}

$sql = "CALL AggiungiValutazione(?, ?, ?, ?)";
try {
    $st = $pdo->prepare($sql);
    $st->bindValue(1, $_SESSION["username"], PDO::PARAM_STR);
    $st->bindValue(2, $_POST["Codice"], PDO::PARAM_INT);
    $st->bindValue(3, $_POST["voto"]);
    $st->bindValue(4, $_POST["note"]);
    $st->execute();
    // INSERIMENTO LOG IN MONGO
    include_once "../../utilities/mongoDBSetup.php";
    $mongodb->Presentazione->insertOne(
        [
            "action" => "New Valutazione",
            "user" => $_SESSION["username"],
            "presentazione" => $_POST["Codice"],
            "voto" => $_POST["voto"],
            "note" =>$_POST["note"],
            "data" => date("Y-m-d H:i:s",time())
        ]
    );
    // END LOG IN MONGO;
} catch (PDOException $e) {
    echo ("[ERRORE] Stored Procedure (AggiungiValutazione) non riuscita. Errore: " . $e->getMessage());
    exit;
}
header('Location: /conferenze/tutorial.php?Codice='.$_POST["Codice"]);
