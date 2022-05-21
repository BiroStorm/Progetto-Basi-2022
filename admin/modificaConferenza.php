<?php
include '../utilities/databaseSetup.php';
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


######################################################
########### [Controllo Iniziale del POST] #############

// Modifica dettagli della conferenza.
if (isset($_POST["modificaTipo"])) {

    switch ($_POST["modificaTipo"]) {
        case "DettagliConferenza":
            if (!isset(
                $_POST["Acronimo"],
                $_POST["AnnoEdizione"],
                $_POST["Nome"],
                $_POST["DataInizio"],
                $_POST["DataFine"]
            )) {
                header('Location: /errorPage.php?error="Errore nel POST del Modifica Conferenza"');
                exit;
            }
            try {
                $sql = 'CALL ModificaConferenza(?, ?, ?, ? ,?)';
                $res = $pdo->prepare($sql);
                $res->bindValue(1, $_POST["Acronimo"]);
                $res->bindValue(2, $_POST["AnnoEdizione"]);
                $res->bindValue(3, $_POST["Nome"]);
                $res->bindValue(4, $_POST["DataInizio"]);
                $res->bindValue(5, $_POST["DataFine"]);
                $res->execute();
                /* header("Location: /conferenze/dettagli.php?Anno=" . $_POST["AnnoEdizione"] . "&Acronimo=" . $_POST["Acronimo"]);
                exit; */
            } catch (PDOException $e) {
                echo ("[ERRORE] Call Modifica Conferenza non riuscita. Errore: " . $e->getMessage());
                exit;
            }
            break;
        case "aggiungiSponsor":
            if (!isset(
                $_POST["Acronimo"],
                $_POST["AnnoEdizione"],
                $_POST["NomeSponsor"],
                $_POST["ImportoSponsor"],
            )) {
                header('Location: /errorPage.php?error="Errore nel POST del Modifica Conferenza"');
                exit;
            }
            try {
                $sql = 'CALL AggiungiSponsor(?, ?, ?, ? )';
                $res = $pdo->prepare($sql);
                $res->bindValue(1, $_POST["Acronimo"]);
                $res->bindValue(2, $_POST["AnnoEdizione"]);
                $res->bindValue(3, $_POST["NomeSponsor"]);
                $res->bindValue(4, $_POST["ImportoSponsor"]);
                $res->execute();
                /* header("Location: /conferenze/dettagli.php?Anno=" . $_POST["AnnoEdizione"] . "&Acronimo=" . $_POST["Acronimo"]);
                exit; */
            } catch (PDOException $e) {
                echo ("[ERRORE] Call Modifica Conferenza non riuscita. Errore: " . $e->getMessage());
                exit;
            }
            break;
    }
}
######################################################
########### [Controllo Iniziale del GET] #############
if (!isset($_GET["Anno"], $_GET["Acronimo"])) {
    //non son settati anno ed acronimo
    header('Location: /404.php');
    exit;
}
$anno = $_GET["Anno"];
$acronimo = $_GET["Acronimo"];
$sql = 'SELECT Logo, Nome, Svolgimento, Totale_Sponsorizzazioni, DataInizio, DataFine, Creatore FROM Conferenza WHERE Acronimo=:x1 AND AnnoEdizione=:x2';

try {
    $st = $pdo->prepare($sql);
    $st->bindParam(":x1", $acronimo, PDO::PARAM_STR);
    $st->bindValue(":x2", $anno, PDO::PARAM_INT);
    $st->execute();
} catch (PDOException $e) {
    echo ("[ERRORE] Query SQL (Select) non riuscita. Errore: " . $e->getMessage());
    exit();
}
if ($st->rowCount() != 1) {
    // conferenza non esistente
    header('Location: /404.php');
    exit;
}
$row = $st->fetch(PDO::FETCH_OBJ);
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Conferenza</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

</head>

<body>
    <?php
    $currentPage = __FILE__;
    include "../utilities/navigationBar.php";
    ?>

    <h4 class="text-center mt-4">Modifica la conferenza</h4>
    <?php
    //Conferenza (Acronimo, AnnoEdizione, Logo, Nome, Svolgimento, Totale_Sponsorizzazioni, DataInizio, DataFine, Creatore)
    // TODO: manca da aggiungere il Logo da modificare.
    ?>

    <!-- CARD DEL DETTAGLI CONFERENZA -->
    <div class="card m-4">
        <div class="card-header">
            Dettagli Conferenza
        </div>
        <div class="card-body">
            <form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Acronimo</label>
                    <input type="text" class="form-control" name="Acronimo" value="<?php echo $_GET["Acronimo"] ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Anno Edizione</label>
                    <input type="text" class="form-control" name="AnnoEdizione" value="<?php echo $_GET["Anno"] ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Titolo</label>
                    <input type="text" class="form-control" name="Nome" value="<?php echo $row->Nome ?>">
                </div>
                <div class="form-group">
                    <label>Data di Inizio</label>
                    <input type="date" class="form-control" name="DataInizio" value="<?php echo $row->DataInizio ?>">
                </div>
                <div class="form-group">
                    <label>Data di Fine</label>
                    <input type="date" class="form-control" name="DataFine" value="<?php echo $row->DataFine ?>">
                </div>
                <input type="text" name="modificaTipo" value="DettagliConferenza" hidden>
                <button type="submit" class="btn btn-primary mt-4">Aggiorna Conferenza</button>
            </form>
        </div>
    </div>



    <!-- Sponsor della Conferenza -->
    <div class="card m-4">
        <div class="card-header">
            Sponsor della Conferenza
        </div>
        <div class="card-body">
            <div class="col-12 mb-4">

                <ul class="list-group">
                    <?php
                    /*
                    $sql = 'CALL AggiungiSponsor(?, ?, ?, ?)';

                    */


                    $sql = 'SELECT * FROM Sponsorizzazione WHERE AcronimoConf = ? AND AnnoEdizione = ?';
                    try {
                        $st = $pdo->prepare($sql);
                        $st->bindParam(1, $acronimo, PDO::PARAM_STR);
                        $st->bindValue(2, $anno, PDO::PARAM_INT);
                        $st->execute();
                    } catch (PDOException $e) {
                        echo ("[ERRORE] Query SQL (Select) non riuscita. Errore: " . $e->getMessage());
                        exit();
                    }
                    if ($st->rowCount() > 0) {
                        while ($row = $st->fetch(PDO::FETCH_OBJ)) {
                            echo "<li class='list-group-item' id='$row->NomeSponsor'>$row->NomeSponsor"
                    ?>
                            <form action="/utilities/rimuoviSponsorDaConf.php" method="POST" class="float-end">
                                <button type="submit" class="btn btn-danger btn-sm float-end">Rimuovi</button>
                                <input type="text" name="AnnoEdizione" value="<?php echo $_GET["Anno"] ?>" readonly hidden>
                                <input type="text" name="Acronimo" value="<?php echo $_GET["Acronimo"] ?>" readonly hidden>
                                <input type="text" name="NomeSponsor" value="<?php echo $row->NomeSponsor ?>" readonly hidden>
                            </form>
                            </li>
                    <?php
                        }
                    } ?>
                </ul>
            </div>
            <!-- AGGIUNTA SPONSOR SECTION -->
            <form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="POST" enctype="multipart/form-data">

                <div class="form-group">
                    <input type="text" class="form-control" name="AnnoEdizione" value="<?php echo $_GET["Anno"] ?>" readonly hidden>
                    <input type="text" class="form-control" name="Acronimo" value="<?php echo $_GET["Acronimo"] ?>" readonly hidden>
                </div>
                <div class="input-group">
                    <select class="form-select col-6" name="NomeSponsor" aria-label="Sponsor Selector" required>
                        <?php
                        $exist = TRUE;

                        /* $sql = "CALL SponsorMancanti(?, ?)"; // c'è un bug per cui non funziona */
                        $sql = "SELECT * FROM Sponsor WHERE Nome NOT IN 
                        (SELECT NomeSponsor FROM Sponsorizzazione WHERE AcronimoConf = ? AND AnnoEdizione = ?)";
                        try {
                            $st = $pdo->prepare($sql);
                            $st->bindParam(1, $acronimo, PDO::PARAM_STR);
                            $st->bindValue(2, $anno, PDO::PARAM_INT);
                            $st->execute();

                            if ($st->rowCount() == 0) {
                                $exist = FALSE;
                            } else {
                                while ($row = $st->fetch(PDO::FETCH_OBJ)) {
                                    echo "<option value='$row->Nome'>$row->Nome</option>";
                                }
                            }
                        } catch (PDOException $e) {
                            echo ("[ERRORE] Query SQL (Select) non riuscita. Errore: " . $e->getMessage());
                            exit();
                        }
                        ?>
                    </select>
                    <input type="number" class="form-control ml-4" class="col-6" name="ImportoSponsor" id="" placeholder="Importo Sponsor" min=100 <?php if (!$exist) echo "disabled" ?> required>
                </div>
                <input type="text" name="modificaTipo" value="aggiungiSponsor" hidden>
                <button type="submit" class="btn btn-primary mt-4" <?php if (!$exist) echo "disabled" ?>>Aggiungi Sponsor</button>
            </form>
        </div>
    </div>

</body>

</html>