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

                // INSERIMENTO LOG IN MONGO
                include_once "../utilities/mongoDBSetup.php";
                $mongodb->Conferenze->insertOne(
                    [
                        "action" => "Aggiunta Sponsor",
                        "sponsor" => $_POST["NomeSponsor"],
                        "importo" => $_POST["ImportoSponsor"],
                        "conferenza" => $_POST["Acronimo"] . " " . $_POST["AnnoEdizione"],
                        "data" => date("Y-m-d H:i:s", time())
                    ]
                );
                // END LOG IN MONGO;
                
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



    <!-- CARD Sponsor della Conferenza -->
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
                        while ($row2 = $st->fetch(PDO::FETCH_OBJ)) {
                            echo "<li class='list-group-item' id='$row2->NomeSponsor'>$row2->NomeSponsor"
                    ?>
                            <form action="/api/admin/rimuoviSponsorDaConf.php" method="POST" class="float-end">
                                <button type="submit" class="btn btn-danger btn-sm float-end">Rimuovi</button>
                                <input type="text" name="AnnoEdizione" value="<?php echo $_GET["Anno"] ?>" readonly hidden>
                                <input type="text" name="Acronimo" value="<?php echo $_GET["Acronimo"] ?>" readonly hidden>
                                <input type="text" name="NomeSponsor" value="<?php echo htmlspecialchars($row2->NomeSponsor) ?>" readonly hidden>
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

                        $sql = "CALL SponsorMancanti(?, ?)";
                        try {
                            $st = $pdo->prepare($sql);
                            $st->bindParam(1, $acronimo, PDO::PARAM_STR);
                            $st->bindValue(2, $anno, PDO::PARAM_INT);
                            $st->execute();

                            if ($st->rowCount() == 0) {
                                $exist = FALSE;
                            } else {
                                while ($row3 = $st->fetch(PDO::FETCH_OBJ)) {
                                    echo '<option value="' . htmlspecialchars($row3->Nome) . '">' . htmlspecialchars($row3->Nome) . '</option>';
                                }
                            }
                            $st->closeCursor();
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

    <!-- CARD Aggiungi Sessione -->
    <div class="card m-4">
        <div class="card-header">
            Aggiungi Sessione
        </div>

        <div class="card-body">
            <div class="accordion" id="accordionExample">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                            Sessioni Presenti
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                        <div class="accordion-body">

                            <!-- SESSIONI ESISTENTI -->
                            <?php
                            // Ci ricaviamo le sessioni della conferenza:
                            $sql2 = "CALL VisualizzazioneSessioni(?,?)";
                            try {
                                $st2 = $pdo->prepare($sql2);
                                $st2->bindValue(1, $acronimo, PDO::PARAM_STR);
                                $st2->bindValue(2, $anno, PDO::PARAM_INT);
                                $st2->execute();

                                if ($st2->rowCount() == 0) {
                                    echo "Nessuna sessione presente!";
                                } else {
                                    /* fetchAll perchè non si può avere un result "attivo" mentre
                                    // si fa un altra CALL.
                                    // Quindi si ricava tutto il risultato di uno, si chiude il cursore
                                    // per poi fare la query successiva! */
                                    $allSession = $st2->fetchAll(PDO::FETCH_OBJ);
                                    $st2->closeCursor();

                                    foreach ($allSession as $row4) {
                            ?>

                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="card-title"><?php echo $row4->Titolo; ?></h5>
                                                <h6 class="card-subtitle mb-2 text-muted"><?php echo $row4->Giorno; ?></h6>
                                                <p class="card-text">Orario: <?php echo $row4->OraInizio . " - " . $row4->OraFine ?></p>
                                                <p><a class="btn btn-outline-secondary" href="/admin/modificaSessione.php?Codice=<?php echo $row4->Codice ?>" role="button">Modifica Sessione</a></p>
                                                <?php if (empty($row4->Link)) {
                                                    echo "<small>Nessun Link Presente</small>";
                                                } else {
                                                ?><a href="<?php echo $row4->Link ?>" class="card-link">Link</a>
                                                <?php }
                                                // Lista Presentazioni: 
                                                $sql3 = "CALL VisualizzaPresentazioni(?)";
                                                $st3 = $pdo->prepare($sql3);
                                                $st3->bindValue(1, $row4->Codice, PDO::PARAM_INT);
                                                $st3->execute();
                                                if ($st3->rowCount() > 0) {
                                                    // stampa le presentazioni della sessione:
                                                ?>
                                                    <table class="table table-responsive table-striped table-hover">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th scope="col">#Seq</th>
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
                                                }
                                                ?>
                                            </div>
                                        </div>
                            <?php
                                    }
                                }
                            } catch (PDOException $e) {
                                echo ("[ERRORE] Stored Procedure (VisualizzazioneSessioni) non riuscita. Errore: " . $e->getMessage());
                                exit();
                            };
                            ?>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            Aggiungi Sessione
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <form action="/api/admin/aggiungiSessione.php" method="POST" enctype="multipart/form-data">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="AnnoEdizione" value="<?php echo $_GET["Anno"] ?>" readonly hidden>
                                    <input type="text" class="form-control" name="Acronimo" value="<?php echo $_GET["Acronimo"] ?>" readonly hidden>
                                </div>

                                <!-- Codice, Link, Titolo, OraInizio, OraFine, Data, AcronimoConf, AnnoEdizione -->
                                <div class="form-group mb-2">
                                    <label>Titolo</label>
                                    <input type="text" class="form-control" name="Titolo" placeholder="Inserisci il Titolo" required>
                                </div>
                                <div class="form-group mb-2">
                                    <label>Data</label>
                                    <input type="date" class="form-control" name="Data" min="<?php echo $row->DataInizio ?>" max="<?php echo $row->DataFine ?>" value="<?php echo $row->DataInizio ?>" required>
                                </div>
                                <div class="form-group mb-2">
                                    <label>Ora Inizio</label>
                                    <input type="time" class="form-control" name="Inizio" value="08:00" required>
                                </div>
                                <div class="form-group mb-2">
                                    <label>Ora Fine</label>
                                    <input type="time" class="form-control" name="Fine" value="09:00" required>
                                </div>
                                <div class="form-group">
                                    <label>Link</label>
                                    <input type="text" class="form-control" name="Link" value="" placeholder="Link Teams">
                                    <small>Puoi aggiungerlo anche dopo</small>
                                </div>
                                <button type="submit" class="btn btn-primary mt-4">Crea Sessione</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>

</html>