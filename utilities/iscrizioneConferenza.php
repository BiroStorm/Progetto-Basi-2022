<?php
session_start();
if (!isset($_SESSION['authorized'])) {
    header("Location: /login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
};
// utente loggato

if(!isset($_GET["Anno"], $_GET["Acronimo"], $_SESSION["username"]) || empty($_GET["Anno"]) || empty($_GET["Acronimo"])){
    header('Location: /errorPage.php?error="Problema con i valori del POST"');
    exit();
}

//inserimento nel db.
$anno = $_GET["Anno"];
$acronimo = $_GET["Acronimo"];
$username = $_SESSION["username"];

include '../utilities/databaseSetup.php';
//...

$sql = 'CALL IscrizioneConferenza(:us, :ac, :an)';
$res = $pdo->prepare($sql);

$res->bindValue(":an", $anno);
$res->bindValue(":ac", $acronimo);
$res->bindValue(":us", $username);

$res->execute();
if ($res->rowCount() > 0){
    echo "Sei già registrato a questa conferenza.";
    header("Refresh: 2; URL=/conferenze/dettagli.php?Anno=$anno&Acronimo=$acronimo");
    exit;
}
echo "Registrato correttamente!";

echo "Registrato correttamente!<br>Sarai rendeirizzato automaticamente alla pagina della conferenza";
header("Refresh: 2; URL=/conferenze/dettagli.php?Anno=$anno&Acronimo=$acronimo");
?>