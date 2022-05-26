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
    header("Location: /login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

if (isset($_POST["AnnoEdizione"], $_POST["Acronimo"], $_POST["NomeSponsor"])) {
    $sql = 'CALL RimuoviSponsor(?, ?, ?)';
    try {
        $st = $pdo->prepare($sql);
        // DEBUG: echo $_POST["Acronimo"]." + ".$_POST["AnnoEdizione"]." + " .$_POST["NomeSponsor"];
        $st->bindParam(1, $_POST["Acronimo"], PDO::PARAM_STR);
        $st->bindValue(2, $_POST["AnnoEdizione"], PDO::PARAM_INT);
        $st->bindParam(3, $_POST["NomeSponsor"], PDO::PARAM_STR);
        $st->execute();
        header('Location: /admin/modificaConferenza.php?Anno='.$_POST["AnnoEdizione"].'&Acronimo='.$_POST["Acronimo"]);
        exit;
    } catch (PDOException $e) {
        echo ("[ERRORE] Query SQL (Select) non riuscita. Errore: " . $e->getMessage());
        exit;
    }
}

header('Location: /403.php');
exit;
