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
$articolo = $st->fetch(PDO::FETCH_OBJ);
$st->closeCursor();
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>[Articolo] <?php echo $articolo->Titolo ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.3/font/bootstrap-icons.css">

</head>

<body>
    <!-- P.Codice, Titolo, OraInizio, OraFine, CodSessione, NumeroSequenza, Abstract -->
    <?php
    $currentPage = __FILE__;
    include "../utilities/navigationBar.php";
    ?>
    <!-- Main Box -->
    <h2 class="text-center mt-4"><?php echo $articolo->Titolo ?></h2>
    <h5 class="text-center text-secondary"><?php echo ("Orario: " . $articolo->OraInizio . " - " . $articolo->OraFine) ?></h5>
    <h6 class="text-center text-secondary"><?php echo ("Posizione N. " . $articolo->NumeroSequenza) ?></h6>
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

    <!-- Parole Chiave Section -->
    <div class="container">
        <div class="row">
            <div class="d-flex justify-content-center">
                <ul class="list-group list-group-horizontal-md">
                    <?php
                    // ci ricaviamo le parole chiavi:
                    $sql = "CALL getParole(?)";
                    try {
                        $st = $pdo->prepare($sql);
                        $st->bindParam(1, $_GET["Codice"], PDO::PARAM_INT);
                        $st->execute();
                    } catch (PDOException $e) {
                        echo ("[ERRORE] Stored Procedure (getParole) non riuscita. Errore: " . $e->getMessage());
                        exit();
                    }
                    while ($parola = $st->fetch(PDO::FETCH_OBJ)) {
                        echo "<li class='list-group-item list-group-item-dark'>$parola->Parola</li>";
                    }
                    $st->closeCursor();
                    ?>
                </ul>
            </div>
        </div>
    </div>


    <!-- Autori dell'Articolo -->
    <div class="container-fluid mt-4">
        <div class="row row-eq-height">
            <div class="col">
                <div class="card text-center h-100">
                    <div class="card-header">
                        Lista Autori
                    </div>
                    <table class="table table-striped ">
                        <thead>
                            <tr>
                                <th scope="col">Nome</th>
                                <th scope="col">Cognome</th>
                            </tr>
                        </thead>
                        <?php
                        $sqlAutori = "CALL getAutori(?)";
                        try {
                            $res = $pdo->prepare($sqlAutori);
                            $res->bindParam(1, $_GET["Codice"], PDO::PARAM_INT);
                            $res->execute();
                        } catch (PDOException $e) {
                            echo ("[ERRORE] Stored Procedure (ListPresenterArticolo) non riuscita. Errore: " . $e->getMessage());
                            exit();
                        }
                        while ($autore = $res->fetch(PDO::FETCH_OBJ)) {
                            echo "<tr>
                        <td>$autore->Nome
                        </td>
                        <td>$autore->Cognome
                        </td></tr>";
                        }
                        $res->closeCursor();
                        ?>

                    </table>

                </div>
            </div>
            <div class="col">
                <div class="card h-100">
                    <div class="card-header">
                        Informazioni
                    </div>
                    <div class="card-body">
                        <?php
                        if (isset($articolo->Nome)) {
                            echo "<h5 class='text-center'>Presentatore: $articolo->Nome $articolo->Cognome</h5>";
                        }
                        ?>
                        <h6 class="text-center">Stato Svolgimento: <?php echo $articolo->Stato_Svolgimento ?></h6>

                        <p>File: <a href="<?php echo $articolo->File ?>">Link al PDF</a></p>
                        <p>Numero Pagine: <?php echo $articolo->NumeroPagine ?></p>
                    </div>
                </div>
            </div>
        </div>


        <!-- SEZIONE [ADMIN] Selezione di un Presenter -->
        <?php
        if (isset($_SESSION["role"]) && strcmp("Admin", $_SESSION["role"]) == 0) {
        ?>
            <div class="card m-4">
                <div class="card-header">
                    Seleziona il Presenter dell'Articolo
                </div>
                <div class="card-body">
                    <form action="/api/admin/aggiornaPresenter.php" method="POST">
                        <div class="mb-3">
                            <div class="form-group">
                                <label class="form-label">Utenti Autori dell'Articolo</label>

                                <?php
                                $sql = "CALL ListPresenterArticolo(?)";
                                try {
                                    $res = $pdo->prepare($sql);
                                    $res->bindParam(1, $_GET["Codice"], PDO::PARAM_INT);
                                    $res->execute();
                                } catch (PDOException $e) {
                                    echo ("[ERRORE] Stored Procedure (ListPresenterArticolo) non riuscita. Errore: " . $e->getMessage());
                                    exit();
                                }
                                if ($res->rowCount() == 0) {
                                    // non ci sono Presenter che son anche Autori!
                                ?>
                                    <select class="form-select" disabled required>
                                        <option selected>Non risultano Autori che son anche Presenter!</option>
                                    </select>
                                    <small class="form-text">L'utente deve avere lo stesso Nome e Cognome di uno degli Autori e deve essere impostato come Presenter.</small>
                            </div>
                            <button class="btn btn-primary mt-4" disabled>Update Articolo</button>
                        <?php
                                } else {
                        ?>
                            <select class="form-select" name="username" aria-label="Utenti Autori dell'Articolo" required>
                                <?php

                                    while ($autore = $res->fetch(PDO::FETCH_OBJ)) {
                                ?>

                                    <option value="<?php echo $autore->Username ?>"><?php echo "$autore->Username | $autore->Nome | $autore->Cognome" ?></option>
                                <?php
                                    }
                                ?>
                            </select>
                            <small class="form-text">L'utente deve avere lo stesso Nome e Cognome di uno degli Autori e deve essere impostato come Presenter.</small>
                        </div>
                        <button type="submit" id="btnAggiungiPresenter" class="btn btn-primary mt-4">Update Articolo</button>
                    <?php
                                }
                    ?>
                </div>

                <input type="number" name="codice" value="<?php echo $_GET["Codice"] ?>" hidden readonly>

                </form>
            </div>

            <!-- VALUTAZIONE DI ALTRI ADMIN SU QUESTA PRESENTAZIONE -->
            <div class="card m-4">
                <div class="card-header">
                    Valutazione della Presentazione
                </div>
                <div class="card-body">
                    <?php
                    $res->closeCursor();
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
                    if ($st->rowCount() == 0) {
                    ?>
                        <p>Non ci sono valutazioni per questa Presentazione!</p>
                    <?php
                    } else {
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
                        while ($valutazione = $st->fetch(PDO::FETCH_OBJ)) {
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
                    $res->closeCursor();
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
    </div>

<?php
        }

?>

<script src="/js/likePresentazione.js"></script>
</body>

</html>