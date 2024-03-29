<?php
include '../utilities/databaseSetup.php';
session_start();
if (!isset($_GET["Codice"])) {
    header("Location: /conferenze.php");
    exit();
}

$sql = "CALL dettagliPresentazione(?)";
try {
    $st = $pdo->prepare($sql);
    $st->bindParam(1, $_GET["Codice"], PDO::PARAM_INT);
    $st->execute();
} catch (PDOException $e) {
    echo ("[ERRORE] Stored Procedure (dettagliPresentazione) non riuscita. Errore: " . $e->getMessage());
    exit();
}
if ($st->rowCount() == 0) {
    header("Location: /404.php");
    exit();
}
$tutorial = $st->fetch(PDO::FETCH_OBJ);
$st->closeCursor();
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>[Tutorial] <?php echo $tutorial->Titolo ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.3/font/bootstrap-icons.css">
    <style>
        .emptyheart:hover {
            color: red;
        }
    </style>
</head>

<body>
    <!-- P.Codice, Titolo, OraInizio, OraFine, CodSessione, NumeroSequenza, Abstract -->
    <?php
    $currentPage = __FILE__;
    include "../utilities/navigationBar.php";
    ?>

    <!-- Main Box -->
    <h2 class="text-center mt-4"><?php echo $tutorial->Titolo ?></h2>
    <h5 class="text-center text-secondary"><?php echo ("Orario: " . $tutorial->OraInizio . " - " . $tutorial->OraFine) ?></h5>
    <h6 class="text-center text-secondary"><?php echo ("Posizione N. " . $tutorial->NumeroSequenza) ?></h6>
    <?php
    if (isset($_SESSION["authorized"], $_SESSION["username"])) {
        // Bisogna controllare se lo ha tra i preferiti:
        $isAlreadyFav = "SELECT 1 FROM Preferiti WHERE CodPresentazione = ? AND Username = ?";
        try {
            $stmt = $pdo->prepare($isAlreadyFav);
            $stmt->bindParam(1, $_GET["Codice"], PDO::PARAM_INT);
            $stmt->bindParam(2, $_SESSION["username"], PDO::PARAM_STR);
            $stmt->execute();
        } catch (PDOException $e) {
            echo ("[ERRORE] Stored Procedure (Select) non riuscita. Errore: " . $e->getMessage());
            exit();
        }
        if ($stmt->rowCount() == 0) {
            // non ha messo ancora Like!
    ?>
            <h6 class="text-center text-secondary "><i class="bi bi-heart fs-3 emptyheart" onclick="likePresentazione(1, <?php echo $_GET['Codice'] ?>, this)"></i></h6>
        <?php
        } else {
        ?>
            <h6 class="text-center"><i class="bi bi-heart-fill fs-3 text-danger fullheart" onclick="likePresentazione(0, <?php echo $_GET['Codice'] ?>, this)"></i></h6>
        <?php
        }
    } else {
        ?>
        <h6 class="text-center text-danger"><a href="/login.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>"><i class="bi bi-heart emptyheart"></i></a></h6>
    <?php
    }


    ?>

    <p id="result" class="text-center text-danger"></p>
    <div class="m-4">
        <div class="card">
            <div class="card-body">
                <label class="fs-5 text">Abstract:</label>
                <p class="lh-base pr-4"><?php echo $tutorial->Abstract ?></p>
            </div>
        </div>
    </div>


    <div class="card m-4">
        <div class="card-header">
            Speaker Associati
        </div>
        <div class="card-body">
            <?php
            // cercasi tutti quelli che presentano il tutorial
            $sql = "CALL getSpeakersAssegnati(?)";
            try {
                $st = $pdo->prepare($sql);
                $st->bindParam(1, $_GET["Codice"], PDO::PARAM_INT);
                $st->execute();
            } catch (PDOException $e) {
                echo ("[ERRORE] Stored Procedure (dettagliPresentazione) non riuscita. Errore: " . $e->getMessage());
                exit();
            }
            if ($st->rowCount() == 0) {
                echo "Nessun Speaker assegnato.";
            } else {
            ?>
                <!-- Tabella degli Speaker che presentano il tutorial -->
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Username</th>
                                <th scope="col">Cognome</th>
                                <th scope="col">Nome</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($speaker = $st->fetch(PDO::FETCH_OBJ)) {
                                // per ogni speaker presente:
                                // Nome, Cognome, Username
                            ?>
                                <tr>
                                    <th scope="row"><?php echo $speaker->Username ?></th>
                                    <td><?php echo $speaker->Cognome ?></td>
                                    <td><?php echo $speaker->Nome ?></td>
                                </tr>
                        <?php
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                </div>

        </div>
    </div>
    <!-- SEZIONE [ADMIN] aggiunta di uno Speaker -->
    <?php
    if (isset($_SESSION["role"]) && strcmp("Admin", $_SESSION["role"]) == 0) {
    ?>
        <div class="card m-4">
            <div class="card-header">
                Assegnazione Ruolo
            </div>
            <div class="card-body">
                <form action="/api/admin/aggiungiSpeaker.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Username Utente</label>
                        <input type="text" name="username" class="form-control" maxlength="24" onchange="checkUsername(this.value);" required>
                        <small id="resultUsername" class="form-text"></small>
                    </div>

                    <input type="number" name="codice" value="<?php echo $_GET["Codice"] ?>" hidden readonly>
                    <button type="submit" id="btnAggiungiPresenter" class="btn btn-primary" disabled>Aggiungi Speaker</button>
                </form>
            </div>
        </div>
        
        <!-- VALUTAZIONE DI ALTRI ADMIN SU QUESTA PRESENTAZIONE -->
        <div class="card m-4">
            <div class="card-header">
                Valutazione della Presentazione
            </div>
            <div class="card-body">
                <?php
                $st->closeCursor();
                $sql = "SELECT * FROM Valutazione WHERE CodPresentazione = ?";
                try {
                    $st = $pdo->prepare($sql);
                    $st->bindValue(1, $_GET["Codice"], PDO::PARAM_INT);
                    $st->execute();
                } catch (PDOException $e) {
                    echo ("[ERRORE] Query SQL (get Valutazione) non riuscita. Errore: " . $e->getMessage());
                    exit;
                }
                // SCORRERE IL RESULT E STAMPARE LA TABELLA!
                if($st->rowCount() == 0){
                    ?>
                    <p>Non ci sono valutazioni per questa Presentazione!</p>
                    <?php
                }else{
                    echo '<div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                          <tr>
                            <th scope="col">Username</th>
                            <th scope="col">Voto</th>
                            <th scope="col">Note</th>
                          </tr>
                        </thead>
                        <tbody>';
                    while($valutazione = $st->fetch(PDO::FETCH_OBJ)){
                        echo "<tr>
                        <th scope='row'>$valutazione->UsernameAdmin</th>
                        <td>$valutazione->Voto</td>
                        <td>$valutazione->Note</td>
                      </tr>";
                    }
                    echo '</tbody>
                    </table></div>';
                }
                ?>
            </div>
        </div>

        <!-- Valutazione della Presentazione -->
        <div class="card m-4">
            <div class="card-header">
                Valutazione della Presentazione
            </div>
            <div class="card-body">
                <?php
                $sql = "SELECT 1 FROM Valutazione WHERE UsernameAdmin = ? AND CodPresentazione = ?";
                try {
                    $st = $pdo->prepare($sql);
                    $st->bindValue(1, $_SESSION["username"], PDO::PARAM_STR);
                    $st->bindValue(2, $_GET["Codice"], PDO::PARAM_INT);
                    $st->execute();
                } catch (PDOException $e) {
                    echo ("[ERRORE] Query SQL (get Valutazione) non riuscita. Errore: " . $e->getMessage());
                    exit;
                }
                if ($st->rowCount() == 1) {
                    echo "<p>Hai già valutato questa Presentazione!</p>";
                } else {
                ?>
                    <form action="/api/admin/valutaPresentazione.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Voto</label>
                            <select name="voto" class="form-control" id="" required>
                                <option value="0">0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                                <option value="9">9</option>
                                <option value="10">10</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Note</label>
                            <input type="text" name="note" class="form-control" maxlength="50">
                        </div>

                        <input type="number" name="Codice" value="<?php echo $_GET["Codice"] ?>" hidden readonly>
                        <button type="submit" class="btn btn-primary">Valuta Presentazione</button>
                    </form>
                <?php } ?>
            </div>
        </div>

    <?php
    }
    ?>

    <!-- Sezione Risorse del Tutorial -->
    <div class="card m-4">
        <div class="card-header">
            Risorse Aggiuntive
        </div>
        <div class="card-body">
            <!-- Visualizzazione Risorse -->

            <?php

            $sql = "SELECT Link, Descrizione, UsernameSpeaker FROM Risorsa WHERE CodTutorial = ?";
            try {
                $st = $pdo->prepare($sql);
                $st->bindValue(1, $_GET["Codice"], PDO::PARAM_INT);
                $st->execute();
            } catch (PDOException $e) {
                echo ("[ERRORE] Query SQL (get Risorsa) non riuscita. Errore: " . $e->getMessage());
                exit;
            }
            if ($st->rowCount() == 0) {
                echo "<p>Non sono state aggiunte risorse.</p>";
            } else {
            ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Link</th>
                                <th scope="col">Descrizione</th>
                                <th scope="col">Speaker</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($risorsa = $st->fetch(PDO::FETCH_OBJ)) {
                                $str = '<td><a class="btn btn-primary" href="' . htmlspecialchars($risorsa->Link) . '">Link</a></td>';
                                $str .= "<td>" . $risorsa->Descrizione . "</td>";
                                $str .= "<td>" . $risorsa->UsernameSpeaker . "</td>";
                                // se è lo Speaker della risorsa, crea tasto elimina.
                                if (isset($_SESSION["username"]) && strcmp($risorsa->UsernameSpeaker, $_SESSION["username"]) == 0) {
                                    $str .= '<td><a class="btn btn-danger" href="/api/speaker/eliminaRisorsa.php?Codice=' . $_GET["Codice"] . "&Link=" . htmlspecialchars($risorsa->Link) . '">Rimuovi</a></td>';
                                }
                                $str .= "</tr>";
                                echo $str;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- SEZIONE [Speaker] Modifica Risorse Aggiuntive -->
                <?php
            }
            if (isset($_SESSION["authorized"])) {
                $sql = "SELECT 1 FROM Insegnamento WHERE Username = ? AND CodiceTutorial = ?";
                try {
                    $st = $pdo->prepare($sql);
                    $st->bindValue(1, $_SESSION["username"]);
                    $st->bindValue(2, $_GET["Codice"]);
                    $st->execute();
                    if ($st->rowCount() > 0) {
                ?>
                        <form action="/api/speaker/aggiungiRisorsa.php" method="POST">
                            <div class="mb-3">
                                <div class="form-group">
                                    <label class="form-label">Link</label>
                                    <input type="url" name="Link" class="form-control" placeholder="http://https://www.google.it/" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Descrizione</label>
                                    <input type="textarea" name="Descrizione" class="form-control" maxlength="255">
                                </div>
                            </div>

                            <input type="number" name="Codice" value="<?php echo $_GET["Codice"] ?>" hidden readonly>
                            <button type="submit" class="btn btn-primary">Aggiungi Risorsa</button>
                        </form>
            <?php
                    }
                } catch (PDOException $e) {
                    echo ("[ERRORE] Query SQL (Select from Insegnamento) non riuscita. Errore: " . $e->getMessage());
                    exit;
                }
            }
            ?>
        </div>
    </div>

    <script src="/js/admin/modificaTutorial.js"></script>
    <script src="/js/likePresentazione.js"></script>
</body>

</html>