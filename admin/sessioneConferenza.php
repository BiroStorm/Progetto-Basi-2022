<?php
session_start();
// l'Utente deve essere loggato e DEVE essere un admin
if (isset($_SESSION['authorized'])) {
    // Utente loggato
    if (!strcmp("Admin", $_SESSION["role"]) == 0) {
        // ma non è un admin --> Code Error 403
        header('Location: /403.php');
        exit();
    }
} else {
    // Utente non loggato.
    header("Location: /login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}
if (
    isset($_POST["codice"]) && isset($_POST["titolo"]) && isset($_POST["numero_presentazioni"])
    && isset($_POST["oraInizio"]) && isset($_POST["oraFine"]) && isset($_POST["data"])
) {
    // controllo date inizio < fine:
    if ($_POST["oraInizio"] > $_POST["oraFine"]) {
        // errore
        // TODO:
        $uploadOk = 0;
    }


    include '../utilities/databaseSetup.php';
    try {
        // Controlliamo che l'Acronimo scelto sia Univoco:
        $sql = 'SELECT 1 FROM Sessione WHERE Codice=?';
        $res = $pdo->prepare($sql);
        $res->bindValue(1, $_POST["codice"]);
        $res->execute();
        if ($res->rowCount() == 1) {
            // Codice Già Presente!
            // TODO 
            $uploadOk = 0;
        } else {

            $sql = 'CALL NuovaSessione(?, ?, ?, ?, ?, ?, ?)';
            $res = $pdo->prepare($sql);

            $logopath = "/assets/imgs/conferenza/default.png";
            $res->bindValue(1, $_SESSION["username"]);
            $res->bindValue(2, $_POST["codice"]);
            $res->bindValue(3, $_POST["titolo"]);
            $res->bindValue(5, $_POST["numero_presentazioni"]);
            $res->bindValue(6, $_POST["oraInizio"]);
            $res->bindValue(7, $_POST["oraFine"]);

            if (isset($_GET["Anno"]) && isset($_GET["Acronimo"])) {
                $anno = $_GET["Anno"];
                $acronimo = $_GET["Acronimo"];
                $sql = 'SELECT Logo, Nome, Svolgimento, Totale_Sponsorizzazioni, DataInizio, DataFine, Creatore FROM Conferenza WHERE Acronimo=:x1 AND AnnoEdizione=:x2';
                // controllo eccezioni dato dal DB
                try {
                    $st = $pdo->prepare($sql); //Preparazione SQL 
                    $st->bindParam(":x1", $acronimo, PDO::PARAM_STR); //Inserire i valori reali nell'SQL
                    $st->bindValue(":x2", $anno, PDO::PARAM_INT);
                    $st->execute(); //Eseguire SQL
                } catch (PDOException $e) {
                    echo ("[ERRORE] Query SQL (Select) non riuscita. Errore: " . $e->getMessage());
                    exit();
                }

                if ($st->rowCount() == 1) { //Controllo del numero di righe ritornate
                    $row = $st->fetch(); //Ricava la prima riga del risultato e poi la salva nella variabile $row

                    if ($res->execute()) {
                        echo "Sessione aggiunta alla conferenza! <br> Redirect in corso...";
                        header("Refresh: 1; URL='/utilities/iscrizioneConferenza.php?Anno=$anno&Acronimo=$acronimo'.php");
                    } else {
                        // esecuzione fallita...
                    }
                }
            }
        }
    } catch (PDOException $e) {
        echo ("[ERRORE] Query SQL (Select) non riuscita. Errore: " . $e->getMessage());
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

</head>

<body>
    <!-- START Navigation Bar -->
    <?php
    $currentPage = __FILE__;
    include "../utilities/navigationBar.php";
    ?>
    <div class="card text-center mx-auto mt-4" style="max-width: 18rem;">
        <div class="card-header">
            Crea una Sessione
        </div>
        <div class="card-body">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Codice</label>
                    <input type="text" name="codice" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Titolo</label>
                    <input type="text" name="titolo" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Numero Presentazioni</label>
                    <input type="text" name="numero_Presentazioni" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Ora di inizio sessione</label>
                    <input type="text" name="oraInizio" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Ora di fine sessione</label>
                    <input type="text" name="oraFine" min="1920-01-01" max="2022-12-31" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Crea sessione</button>
            </form>
        </div>
    </div>
    <?php
    if ($uploadOk == 0) {
        //stampa qualcosa//
    }
    ?>
    <br>

</body>

</html>