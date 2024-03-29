<?php
include '../utilities/databaseSetup.php';
session_start();
########### [Autorizzazione] #############
if (isset($_SESSION['authorized'])) {
    // Utente loggato
    if (!strcmp("Admin", $_SESSION["role"]) == 0) {
        header('Location: /403.php');
        exit;
    }
} else {
    // Utente non loggato.
    header("Location: /login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

if (!isset($_GET["Codice"])) {
    //non è stato settato il codice
    header('Location: /404.php');
    exit;
}
$sessione = $_GET["Codice"];
$sql = 'SELECT * FROM Sessione WHERE Codice = ?';

try {
    $st = $pdo->prepare($sql);
    $st->bindParam(1, $sessione, PDO::PARAM_INT);
    $st->execute();
    if ($st->rowCount() == 0) {
        // non esiste la sessione con quel codice:
        header('Location: /404.php');
        exit;
    }
} catch (PDOException $e) {
    echo ("[ERRORE] Query SQL (Select) non riuscita. Errore: " . $e->getMessage());
    exit();
}
$sessione = $st->fetch(PDO::FETCH_OBJ);
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Sessione</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/admin/modificaSessione.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/thinline.css">
</head>

<body>
    <?php
    $currentPage = __FILE__;
    include "../utilities/navigationBar.php";
    ?>
    <h3 class="text-center mt-4">Modifica Sessione</h3>
    <h4 class="text-center mt-4"><?php echo $sessione->Titolo ?></h4>
    <h5 class="text-center card-subtitle mb-2 text-muted"><?php echo $sessione->Giorno; ?></h5>
    <p class="text-center">Orario: <?php echo $sessione->OraInizio . " - " . $sessione->OraFine ?></p>
    <div class="card m-4">
        <div class="card-header">
            Modifica Sessione
        </div>
        <div class="card-body">
            <form action="/api/admin/modificaSessione.php" method="POST" onchange="setEnabled();" oninput="setEnabled()" autocomplete="off">
                <div class="form-group mt-1">
                    <label>Titolo Sessione</label>
                    <input type="text" class="form-control" name="Titolo" id="datiSess" value="<?php echo $sessione->Titolo ?>">
                </div>
                <div class="form-group mt-1">
                    <label>Giorno della Sessione</label>
                    <input type="date" class="form-control" name="Data" id="datiSess" value="<?php echo $sessione->Giorno ?>">
                </div>
                <div class="form-group mt-1">
                    <label>Orario di Inizio</label>
                    <input type="time" class="form-control" name="Inizio" id="datiSess" value="<?php echo $sessione->OraInizio ?>">
                </div>
                <div class="form-group mt-1">
                    <label>Orario di Fine</label>
                    <input type="time" class="form-control" name="Fine" id="datiSess" value="<?php echo $sessione->OraFine ?>">
                </div>
                <div class="form-group mt-1">
                    <label>Link</label>
                    <input type="text" class="form-control" name="Link" id="datiSess" value="<?php echo $sessione->Link ?>" placeholder="Link alla Stanza Teams">
                </div>
                <input type="number" class="form-control" name="Codice" value="<?php echo $_GET["Codice"] ?>" hidden readonly>
                <button type="submit" class="btn btn-primary mt-3" id="modificaSess">Aggiorna i dati</button>
            </form>
        </div>
    </div>
    <!-- PRESENTAZIONI PRESENTI NELLA SESSIONE -->
    <div class="card m-4">
        <div class="card-header">
            Presentazioni
        </div>
        <div class="card-body">
            <!-- Visualizzazione Presentazioni già Presenti -->
            <?php
            // Lista Presentazioni: 
            $sql3 = "CALL VisualizzaPresentazioni(?)";
            $st3 = $pdo->prepare($sql3);
            $st3->bindValue(1, $sessione->Codice, PDO::PARAM_INT);
            $st3->execute();
            if ($st3->rowCount() > 0) {
                // stampa le presentazioni della sessione:
            ?>
                <table class="table table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">#Sequenza</th>
                            <th scope="col">Titolo</th>
                            <th scope="col">Inizio</th>
                            <th scope="col">Fine</th>
                            <th scope="col">Tipologia</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($presentazione = $st3->fetch(PDO::FETCH_OBJ)) {
                            $str = "<tr><th scope='row'>" . $presentazione->NumeroSequenza . "</th>";
                            $str .= "<td>" . $presentazione->Titolo . "</td>";
                            $str .= "<td>" . $presentazione->OraInizio . "</td>";
                            $str .= "<td>" . $presentazione->OraFine . "</td>";
                            $str .= "<td>" . $presentazione->Tipologia . "</td></tr>";
                            echo $str;
                        }
                        ?>
                    </tbody>
                </table>
            <?php
            } else {
                echo "Non ci sono presentazioni";
            }
            ?>

        </div>
    </div>
    <!-- Form per l'aggiunta di nuove Presentazioni -->
    <div class="card m-4">
        <div class="card-header">
            Aggiungi nuove Presentazioni
        </div>
        <div class="card-body">

            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-tutorial-tab" data-bs-toggle="tab" data-bs-target="#nav-tutorial" type="button" role="tab" aria-controls="nav-tutorial" aria-selected="true">Aggiungi Tutorial</button>
                    <button class="nav-link" id="nav-articolo-tab" data-bs-toggle="tab" data-bs-target="#nav-articolo" type="button" role="tab" aria-controls="nav-articolo" aria-selected="false">Aggiungi Articolo</button>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-tutorial" role="tabpanel" aria-labelledby="nav-tutorial-tab">
                    <!-- AGGIUNGI TUTORIAL FORM -->
                    <form action="/api/admin/aggiungiTutorial.php" method="POST" id="FormTutorial" autocomplete="off">
                        <!-- Titolo, OraInizio, OraFine, Abstract -->

                        <div class="form-group">
                            <label for="" class="form-label mt-2">Titolo</label>
                            <input type="text" class="form-control" name="Titolo" placeholder="Titolo del Tutorial" required>
                        </div>
                        <div class="form-group">
                            <label for="" class="form-label mt-2">Inizio</label>
                            <input type="time" class="form-control" name="Inizio" min="<?php echo $sessione->OraInizio ?>" max="<?php echo $sessione->OraFine ?>" value="<?php echo $sessione->OraInizio ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="" class="form-label mt-2">Fine</label>
                            <input type="time" class="form-control" name="Fine" min="<?php echo $sessione->OraInizio ?>" max="<?php echo $sessione->OraFine ?>" value="<?php echo $sessione->OraFine ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="" class="form-label mt-2">Abstract:</label>
                            <div class="form-floating">
                                <textarea class="form-control" form="FormTutorial" id="floatingTextarea" name="Abstract" maxlength="500"></textarea>
                                <label for="floatingTextarea">Descrizione (max 500 caratteri)</label>
                            </div>
                        </div>
                        <input type="text" class="form-control" name="CodSessione" value="<?php echo $sessione->Codice ?>" readonly hidden>
                        <button type="submit" class="btn btn-primary mt-4">Aggiungi Tutorial</button>
                    </form>
                </div>
                <div class="tab-pane fade" id="nav-articolo" role="tabpanel" aria-labelledby="nav-articolo-tab">
                    <form action="/api/admin/aggiungiArticolo.php" method="POST" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';" autocomplete="off">
                        <!-- ARTICOLO SECTION -->
                        <!-- Titolo, OraInizio, OraFine, File, NumeroPagine, stato_svolgimento, Presentatore -->

                        <div class="form-group">
                            <label for="" class="form-label mt-2">Titolo</label>
                            <input type="text" class="form-control" name="Titolo" placeholder="Titolo del Tutorial" required>
                        </div>
                        <div class="form-group">
                            <label for="" class="form-label mt-2">Inizio</label>
                            <input type="time" class="form-control" name="Inizio" min="<?php echo $sessione->OraInizio ?>" max="<?php echo $sessione->OraFine ?>" value="<?php echo $sessione->OraInizio ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="" class="form-label mt-2">Fine</label>
                            <input type="time" class="form-control" name="Fine" min="<?php echo $sessione->OraInizio ?>" max="<?php echo $sessione->OraFine ?>" value="<?php echo $sessione->OraFine ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="" class="form-label mt-2">File PDF</label>
                            <input type="file" id="inputfile" name="filePDF" class="form-control" accept="application/pdf" onchange="checkForm();" required>
                        </div>
                        <div class="form-group">
                            <label for="" class="form-label mt-2">Numero di Pagine</label>
                            <input type="number" name="NPagine" class="form-control" value=1 min=1 required>
                        </div>
                        <div class="form-group tagBox">
                            <label for="" class="form-label mt-2">Parole Chiave</label>
                            <ul id="listahashtag">
                                <input type="text" id="hashtaginput" class="form-control shadow-none" maxlength="20">
                            </ul>
                            <small class="form-text text-muted">Premi Invio o Spazio per aggiungere una Parola Chiave</small>
                        </div>
                        <div class="form-group" id="autoridiv">
                            <label for="" class="form-label mt-2">Autori dell'Articolo</label>
                            <div class="input-group mt-1" id="clonehere">

                                <input type="text" name="nomeAutore[]" class="form-control col-5" placeholder="Nome Autore" required>
                                <input type="text" name="cognomeAutore[]" class="form-control col-5 mr-2" placeholder="Cognome Autore" required>
                                <button type="button" class="btn btn-success ml-2" id="newAutoreBtn" onclick="aggiungiInput(this);">Nuovo Autore</button>
                            </div>
                        </div>

                        <input type="text" name="paroleChiave" id="outputHashtag" hidden>
                        <input type="text" class="form-control" name="CodSessione" value="<?php echo $sessione->Codice ?>" readonly hidden>
                        <button type="submit" class="btn btn-primary mt-4">Aggiungi Articolo</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
    <script src="/js/admin/modificaSessione.js"></script>
</body>

</html>