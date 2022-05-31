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
    $_POST["Titolo"],
    $_POST["Data"],
    $_POST["Inizio"],
    $_POST["Fine"],
    $_POST["Link"]
) || areEmpty(
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

$sql = 'CALL AggiornaSessione(?, ?, ?, ?, ?, ?)';
try {
    $st = $pdo->prepare($sql);
    $st->bindParam(1, $_POST["Codice"]);
    $st->bindParam(2, $_POST["Titolo"]);
    $st->bindParam(3, $_POST["Data"]);
    $st->bindParam(4, $_POST["Inizio"]);
    $st->bindParam(5, $_POST["Fine"]);
    $st->bindParam(6, $_POST["Link"]);
    $st->execute();

    if ($st->rowCount() == 0) {
        header('Location: /admin/modificaSessione.php?Codice=' . $_POST["Codice"]);
        exit;
    }
    $row = $st->fetch();
    header('Location: /errorPage.php?error="' . htmlspecialchars($row["Errore"]) . '"');
    exit;
} catch (PDOException $e) {
    echo ("[ERRORE] Query SQL (AggiornaSessione) non riuscita. Errore: " . $e->getMessage());
    exit;
}


