<?php
if (!isset($_GET["Codice"])) {
    header("Location: /conferenze.php");
    exit;
}
include 'databaseSetup.php';
$codice = $_GET["Codice"];
// controlliamo se è un tutorial o articolo:
$sql1 = "SELECT 1 FROM Tutorial WHERE Codice = ?";
try {
    $st = $pdo->prepare($sql1);
    $st->bindParam(1, $codice, PDO::PARAM_INT);
    $st->execute();
    if ($st->rowCount() > 0) {
        // è un tutorial
        header("HTTP/1.1 302 Found");
        header("Location: /conferenze/tutorial.php?Codice=" . $codice);
        exit;
    }
} catch (PDOException $e) {
    echo ("[ERRORE] Query SQL (Controllo se è Tutorial) non riuscita. Errore: " . $e->getMessage());
    exit();
}

// proviamo se è un Articolo
$sql1 = "SELECT 1 FROM Articolo WHERE Codice = ?";
try {
    $st = $pdo->prepare($sql1);
    $st->bindParam(1, $codice, PDO::PARAM_INT);
    $st->execute();
    if ($st->rowCount() > 0) {
        // è un tutorial
        header("HTTP/1.1 302 Found");
        header("Location: /conferenze/articolo.php?Codice=" . $codice);
        exit;
    }
} catch (PDOException $e) {
    echo ("[ERRORE] Query SQL (Controllo se è Articolo) non riuscita. Errore: " . $e->getMessage());
    exit();
}
// non esiste una presentazione con questo codice:
header("Location: /404.php");
exit;
