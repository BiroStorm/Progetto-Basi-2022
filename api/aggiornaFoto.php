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

if (!isset($_FILES["fotoProfilo"])) {
    header('Location: /errorPage.php?error="Manca la foto"');
    exit;
}



// SETUP LOADING LOGO
$target_dir = __DIR__ . "/../assets/imgs/profili/";
$targetfinale = $target_dir . basename($_FILES["fotoProfilo"]["name"]);

$imageFileType = strtolower(pathinfo($targetfinale, PATHINFO_EXTENSION));

if (UPLOAD_ERR_OK !== $_FILES["fotoProfilo"]['error']) {
    //errore nell'upload
    header('Location: /errorPage.php?error="Errore durante il Caricamento della foto."');
    exit;
}
$check = getimagesize($_FILES["fotoProfilo"]["tmp_name"]);
if ($check == false || ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg")) {
    header('Location: /errorPage.php?error="Non è un immagine"');
    exit;
}
$logopath = $target_dir . "default.jpg";
if (move_uploaded_file($_FILES["fotoProfilo"]["tmp_name"], $target_dir . $_SESSION["username"] . "." . $imageFileType)) {
    $logopath = "/assets/imgs/profili/" . $_SESSION["username"] . "." . $imageFileType;
}

try {
    $sql = 'UPDATE ' . $_SESSION["role"]. ' SET Foto = :foto WHERE Username = :usr1';
    $res = $pdo->prepare($sql);
    $res->bindValue(":usr1", $_SESSION["username"]);
    $res->bindValue(":foto", $logopath, PDO::PARAM_STR);
    $res->execute();
    header('Location: /user/modificaProfilo.php');
} catch (PDOException $e) {
    echo ("[ERRORE] Update Foto non riuscita. Errore: " . $e->getMessage());
    exit;
}
