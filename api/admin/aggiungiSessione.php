<?php
include '../../utilities/databaseSetup.php';
session_start();
########### [Autorizzazione] #############
if (isset($_SESSION['authorized'])) {
    // Utente loggato
    if (!strcmp("Admin", $_SESSION["role"]) == 0) {
        // ma non è un admin --> Code Error 403
        header('Location: /403.php');
        exit;
    }
} else {
    // Utente non loggato.
    header("Location: /login.php");
    exit;
}
/* Se un argomento è Empty return true */
function areEmpty()
{
    foreach (func_get_args() as $arg)
        if (empty($arg))
            return true;
        else
            continue;
    return false;
};

# <!-- Codice, Link, Titolo, OraInizio, OraFine, Data, AcronimoConf, AnnoEdizione -->
if (!isset(
    $_POST["AnnoEdizione"],
    $_POST["Acronimo"],
    $_POST["Titolo"],
    $_POST["Data"],
    $_POST["Inizio"],
    $_POST["Fine"],
    $_POST["Link"]
) || areEmpty(
    $_POST["AnnoEdizione"],
    $_POST["Acronimo"],
    $_POST["Titolo"],
    $_POST["Data"],
    $_POST["Inizio"],
    $_POST["Fine"]
)) {
    header('Location: /errorPage.php?error="Problema con i valori del POST"');
    exit;
}
// controllo degli orari: Inizio < Fine
$inizio = strtotime($_POST["Inizio"]);
$fine = strtotime($_POST["Fine"]);
if ($inizio > $fine) {
    header('Location: /errorPage.php?error="L\'ora di inizio non può essere dopo la fine"');
    exit;
}
// la data deve essere compresa tra i giorni di svolgimento della conferenza:
// Fortunatamente il controllo viene fatto già dentro alla Stored Procedure.

$sql = 'CALL NuovaSessione(?, ?, ?, ?, ?, ?, ?)';
try {
    $st = $pdo->prepare($sql);
    $st->bindParam(1, $_POST["Acronimo"]);
    $st->bindValue(2, $_POST["AnnoEdizione"]);
    $st->bindParam(3, $_POST["Titolo"]);
    $st->bindParam(4, $_POST["Data"]);
    $st->bindParam(5, $_POST["Inizio"]);
    $st->bindParam(6, $_POST["Fine"]);
    $st->bindParam(7, $_POST["Link"]);
    $st->execute();


    if ($st->rowCount() == 0) {
        // INSERIMENTO LOG IN MONGO
        include_once "../../utilities/mongoDBSetup.php";
        $mongodb->Conferenze->insertOne(
            [
                "action" => "Nuova Sessione",
                "user" => $_SESSION["username"],
                "titolo" => $_POST["Titolo"],
                "dataSessione" => $_POST["Data"],
                "inizio" => $_POST["Inizio"],
                "fine" => $_POST["Fine"],
                "conferenza" => $_POST["Acronimo"] . " " .$_POST["AnnoEdizione"],
                "data" => date("Y-m-d H:i:s", time())
            ]
        );
        // END LOG IN MONGO;

        header('Location: /admin/modificaConferenza.php?Anno=' . $_POST["AnnoEdizione"] . '&Acronimo=' . $_POST["Acronimo"]);
        exit;
    }
    $row = $st->fetch();
    header('Location: /errorPage.php?error="' . htmlspecialchars($row["Errore"]) . '"');
    exit;
} catch (PDOException $e) {
    echo ("[ERRORE] Query SQL (Select) non riuscita. Errore: " . $e->getMessage());
    exit;
}
