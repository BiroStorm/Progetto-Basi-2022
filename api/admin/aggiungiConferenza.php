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

if (
    !isset($_POST['acronimo'], $_POST["annoEdizione"], $_POST["nome"], $_POST["inizio"], $_POST["fine"], $_FILES["logo"])
) {
    header('Location: /errorPage.php?error="Problema con i valori del POST"');
    exit;
}

// controllo date inizio < fine:
if ($_POST["inizio"] > $_POST["fine"]) {
    header('Location: /errorPage.php?error="Inizio è dopo la Fine!"');
    exit;
}
try {
    // Controlliamo che l'Acronimo scelto sia Univoco:
    $sql = 'SELECT 1 FROM Conferenza WHERE Acronimo=? AND AnnoEdizione = ?';
    $res = $pdo->prepare($sql);
    $res->bindValue(1, $_POST["acronimo"]);
    $res->bindValue(2, $_POST["annoEdizione"]);
    $res->execute();
    if ($res->rowCount() == 1) {
        header('Location: /errorPage.php?error="Acronimo o AnnoEdizione già presente!"');
        exit;
    } else {
        $res->closeCursor();
        $sql = 'CALL NuovaConferenza(?, ?, ?, ?, ?, ?, ?)';
        $res = $pdo->prepare($sql);

        $logopath = "/assets/imgs/conferenza/default.png";
        $res->bindValue(1, $_SESSION["username"]);
        $res->bindValue(2, $_POST["acronimo"]);
        $res->bindValue(3, $_POST["annoEdizione"]);
        $res->bindParam(4, $logopath);
        $res->bindValue(5, $_POST["nome"]);
        $res->bindValue(6, $_POST["inizio"]);
        $res->bindValue(7, $_POST["fine"]);


        $target_dir = __DIR__ . "/../../assets/imgs/conferenza/";
        $targetfinale = $target_dir . basename($_FILES["logo"]["name"]);

        $imageFileType = strtolower(pathinfo($targetfinale, PATHINFO_EXTENSION));

        if (UPLOAD_ERR_OK !== $_FILES["logo"]['error']) {
            header('Location: /errorPage.php?error="Errore durante il caricamento."');
            exit;
        }
        $check = getimagesize($_FILES["logo"]["tmp_name"]);
        if ($check == false || ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg")) {

            header('Location: /errorPage.php?error="Non è un immagine"');
            exit;
        }
        if ($_FILES["logo"]["size"] > 800000) {
            // file troppo grande!
            header('Location: /errorPage.php?error="File Troppo Grande!"');
            exit;
        } else {
            if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target_dir . $_POST["acronimo"] . $_POST["annoEdizione"] . "." . $imageFileType)) {
                $logopath = "/assets/imgs/conferenza/" . $_POST["acronimo"] . $_POST["annoEdizione"] . "." . $imageFileType;
            } else {
                //errore con l'uploading del file
                echo "error";
            }
        }

        if ($res->execute()) {
            // INSERIMENTO LOG IN MONGO
            include_once "../../utilities/mongoDBSetup.php";
            $mongodb->Conferenze->insertOne(
                [
                    "action" => "New Conferenza",
                    "user" => $_SESSION["username"],
                    "acronimo" => $_POST["acronimo"],
                    "annoEdizione" => $_POST["annoEdizione"],
                    "nome" => $_POST["nome"],
                    "dataInizio" => $_POST["inizio"],
                    "dataFine" => $_POST["fine"],
                    "data" => date("Y-m-d H:i:s", time())
                ]
            );
            // END LOG IN MONGO;
            echo "Creazione conferenza Completata! <br> Redirect in corso...";
            header("Refresh: 0.7; URL=/conferenze.php");
        } else {
            header('Location: /errorPage.php?error="Inserimento nel DB Fallito!"');
            exit;
        }
    }
} catch (PDOException $e) {
    echo ("[ERRORE] Inserimento nuova Conferenza non riuscita. Errore: " . $e->getMessage());
    exit();
}
