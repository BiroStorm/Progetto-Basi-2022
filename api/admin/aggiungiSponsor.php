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

// if $_POST is set:
if (!isset($_POST["nome"], $_FILES["logo"])) {
    header('Location: /errorPage.php?error="Problema con i valori del POST"');
    exit;
}


$target_dir = __DIR__ . "/../../assets/imgs/sponsor/";
$targetfinale = $target_dir . basename($_FILES["logo"]["name"]);
$imageFileType = strtolower(pathinfo($targetfinale, PATHINFO_EXTENSION));

if (UPLOAD_ERR_OK !== $_FILES["logo"]['error']) {
    //errore nell'upload
    header('Location: /errorPage.php?error="Errore durante l\'upload!"' . $_FILES["logo"]['error']);
    exit;
}

include '../utilities/databaseSetup.php';

// controlliamo se esiste già lo sponsor
$sql = 'SELECT 1 FROM Sponsor WHERE Nome=?';
$res = $pdo->prepare($sql);
$res->bindValue(1, $_POST["nome"]);
$res->execute();
if ($res->rowCount() == 1) {
    // Nome Sponsor Già Presente!
    header('Location: /errorPage.php?error="Sponsor già presente!"');
    exit;
}

$logopath = "/assets/imgs/sponsor/default.jpg";

// UPLOAD FILE
$check = getimagesize($_FILES["logo"]["tmp_name"]);
if ($check == false || ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg")) {
    // non è un img
    header('Location: /errorPage.php?error="Non è un immagine!"');
    exit;
} else {
    if ($_FILES["logo"]["size"] > 400000) {
        // file troppo grande!
    } else {
        if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target_dir . $_POST["nome"] . "." . $imageFileType)) {
            $logopath = "/assets/imgs/sponsor/" . $_POST["nome"] . "." . $imageFileType;
        } else {
            //errore con l'uploading del file
            header('Location: /errorPage.php?error="Errore durante il salvataggio del file!"');
            exit;
        }
    }
}

$sql = 'CALL NuovoSponsor(?, ?)';
$res = $pdo->prepare($sql);
$res->bindValue(1, $_POST["nome"]);
$res->bindValue(2, $logopath);
if ($res->execute()) {
    // INSERIMENTO LOG IN MONGO
    include_once "../../utilities/mongoDBSetup.php";
    $mongodb->Users->insertOne(
        [
            "action" => "New Sponsor",
            "user" => $_SESSION["username"],
            "sponsor" => $_POST["nome"],
            "data" => date("Y-m-d H:i:s", time())
        ]
    );
    // END LOG IN MONGO;
    echo "Sponsor Creato con successo!<br>Redirecting...";
    header("Refresh: 0.7; URL=/conferenze.php");
    exit;
} else {
    header('Location: /errorPage.php?error="Errore Inserimento nel DB."');
    exit;
}
