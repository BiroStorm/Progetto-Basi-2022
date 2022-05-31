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

/* Se un argomento è Empty return true */
function areEmpty()
{
    foreach (func_get_args() as $arg)
        if (empty($arg))
            return true;
        else
            continue;
    return false;
}

if (
    !isset($_POST["Titolo"], $_POST["Inizio"], $_POST["Fine"], $_POST["NPagine"], $_POST["paroleChiave"], $_POST["CodSessione"], $_FILES["filePDF"], $_POST["cognomeAutore"])
    ||
    areEmpty($_POST["Titolo"], $_POST["Inizio"], $_POST["Fine"], $_POST["NPagine"], $_POST["paroleChiave"], $_POST["CodSessione"], $_POST["cognomeAutore"])
) {
    header('Location: /errorPage.php?error="Mancano i valori nel POST"');
    exit;
}
$inizio = strtotime($_POST["Inizio"]);
$fine = strtotime($_POST["Fine"]);
if ($inizio > $fine) {
    header('Location: /errorPage.php?error="L\'ora di inizio non può essere dopo la fine"');
    exit;
}


##### Gestione file PDF #####

$target_dir = __DIR__ . "/../../assets/pdf/articolo/";
$targetfinale = $target_dir . basename($_FILES["filePDF"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($targetfinale, PATHINFO_EXTENSION));

if (UPLOAD_ERR_OK !== $_FILES["filePDF"]['error']) {
    //errore nell'upload
    header('Location: /errorPage.php?error="Errore durante l\'upload!"' . $_FILES["filePDF"]['error']);
    exit;
}


// UPLOAD FILE
if ($imageFileType != "pdf") {
    // non è un pdf
    header('Location: /errorPage.php?error="Non è un file PDF!"');
    exit;
} else {
    if (move_uploaded_file($_FILES["filePDF"]["tmp_name"], $target_dir . pathinfo($_FILES["filePDF"]["name"], PATHINFO_FILENAME)."Sess". $_POST["CodSessione"]  .".". $imageFileType)) {
        $filepath = "/assets/pdf/articolo/" . pathinfo($_FILES["filePDF"]["name"], PATHINFO_FILENAME)."Sess". $_POST["CodSessione"]  .".". $imageFileType;
    } else {
        //errore con l'uploading del file
        header('Location: /errorPage.php?error="'. htmlspecialchars($_FILES["filePDF"]["error"]) . '"');
        exit;
    }
}

$sql = "CALL NuovoArticolo(?, ?, ?, ?, ?, ?)";

try {
    $st = $pdo->prepare($sql);
    $st->bindParam(1, $_POST["Titolo"]);
    $st->bindValue(2, $_POST["Inizio"]);
    $st->bindParam(3, $_POST["Fine"]);
    $st->bindParam(4, $_POST["CodSessione"]);
    $st->bindParam(5, $filepath);
    $st->bindParam(6, $_POST["NPagine"]);
    $st->execute();

    $result = $st->fetch();
    $st->closeCursor();
} catch (PDOException $e) {
    echo ("[ERRORE] Query SQL (CALL NuovoArticolo) non riuscita. Errore: " . $e->getMessage());
    exit;
}

if (isset($result["Errore"])) {
    header('Location: /errorPage.php?error="' . htmlspecialchars($result["Errore"]) . '"');
    exit;
}
########## Aggiunto l'Articolo senza Problemi ##########
########## Aggiunta Autori ############
if (!isset($result["CodicePresentazione"])) {
    header('Location: /errorPage.php?error="Manca il return del Codice della Presentazione appena aggiunta."');
    exit;
}
$codiceArticolo = $result["CodicePresentazione"];
$sqlAutori = "CALL InserisciAutore(?,?,?)";
$cognomiAutori = $_POST["cognomeAutore"];
foreach ($_POST["nomeAutore"] as $index => $nomeAutore) {
    try {
        $st = $pdo->prepare($sqlAutori);
        $st->bindParam(1, $codiceArticolo);
        $st->bindValue(2, $nomeAutore);
        $st->bindParam(3, $cognomiAutori[$index]);
        $st->execute();
        $st->closeCursor();
    } catch (PDOException $e) {
        echo ("[ERRORE] Query SQL (CALL InserisciAutore) non riuscita. Errore: " . $e->getMessage());
        exit;
    }
}

########## Aggiunta Parole Chiave (Hashtag) ########
$sqlParole = "CALL InserisciHashtag(?, ?)";
$paroleChiave = explode(",", $_POST["paroleChiave"]);

foreach ($paroleChiave as $parola) {
    try {
        $st = $pdo->prepare($sqlParole);
        $st->bindValue(1, 14, PDO::PARAM_INT);
        $st->bindValue(2, trim($parola));
        $st->execute();
        $st->closeCursor();
    } catch (PDOException $e) {
        echo ("[ERRORE] Query SQL (CALL InserisciHashtag) non riuscita. Errore: " . $e->getMessage());
        exit;
    }
}

header('Location: /admin/modificaSessione.php?Codice=' . $_POST["CodSessione"]);
exit;
