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

if (!isset($_POST["Titolo"], $_POST["Inizio"], $_POST["Fine"], $_POST["Abstract"], $_POST["CodSessione"]) || areEmpty($_POST["Titolo"], $_POST["Inizio"], $_POST["Fine"], $_POST["CodSessione"])) {
    header('Location: /errorPage.php?error="Mancano i valori nel POST"');
    exit;
}
$inizio = strtotime($_POST["Inizio"]);
$fine = strtotime($_POST["Fine"]);
if ($inizio > $fine) {
    header('Location: /errorPage.php?error="L\'ora di inizio non può essere dopo la fine"');
    exit;
}

$sql = "CALL NuovoTutorial(?,?,?,?,?)";

try {
    $st = $pdo->prepare($sql);
    $st->bindParam(1, $_POST["Titolo"]);
    $st->bindValue(2, $_POST["Inizio"]);
    $st->bindParam(3, $_POST["Fine"]);
    $st->bindParam(4, $_POST["CodSessione"]);
    $st->bindParam(5, $_POST["Abstract"]);
    $st->execute();

    if ($st->rowCount() == 0) {
        // INSERIMENTO LOG IN MONGO
        include_once "../../utilities/mongoDBSetup.php";
        $mongodb->Conferenze->insertOne(
            [
                "action" => "Nuovo Tutorial",
                "user" => $_SESSION["username"],
                "titolo" => $_POST["Titolo"],
                "sessione" => $_POST["CodSessione"],
                "inizio" => $_POST["Inizio"],
                "fine" => $_POST["Fine"],
                "abstract" => $_POST["Abstract"],
                "data" => date("Y-m-d H:i:s", time())
            ]
        );

        header('Location: /admin/modificaSessione.php?Codice=' . $_POST["CodSessione"]);
        exit;
    }
    $row = $st->fetch();
    header('Location: /errorPage.php?error="' . htmlspecialchars($row["Errore"]) . '"');
    exit;
} catch (PDOException $e) {
    echo ("[ERRORE] Query SQL (CALL NuovoTutorial) non riuscita. Errore: " . $e->getMessage());
    exit;
}
