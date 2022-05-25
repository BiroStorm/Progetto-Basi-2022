<?php
//CONNESSIONE AL DB
include '../utilities/databaseSetup.php';
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Dettagli Conferenze</title>
    <style>
        .confLogo {
            max-width: 400px;
            max-height: 400px;
        }

        .imgcarosello {
            filter: blur(2px);
        }
    </style>

</head>

<body>
    <!-- START Navigation Bar -->
    <?php
    $currentPage = __FILE__;
    include "../utilities/navigationBar.php";
    ?>
    <!-- END Navigation Bar -->
    <?php
    if (!isset($_GET["Anno"], $_GET["Acronimo"])) {
        header("Location: /conferenze.php");
        exit();
    }
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

    if ($st->rowCount() == 0) {
        //Conferenza non Esiste:
        header("Location: /404.php");
        exit();
    }
    //Controllo del numero di righe ritornate
    $row = $st->fetch(); //Ricava la prima riga del risultato e poi la salva nella variabile $row
    ?>
    <div class="position-relative">
        <!-- Tasto Iscriviti/ testo "Sei già iscritto" -->
        <div class="position-absolute top-0 end-0 mt-4 translate-middle">
            <?php
            // se è loggato e lo svolgimento è attivo, mostrare pulsante o scritta d'iscrizione.
            // + se è admin tasto di "Modifica"
            if (isset($_SESSION["authorized"]) && strcmp("Attiva", $row["Svolgimento"]) == 0) {
                //controlla se è già iscritto
                $sql = "SELECT 1 FROM Registrazione WHERE UsernameUtente = ? AND AcronimoConf = ? AND AnnoEdizione = ?";
                $res = $pdo->prepare($sql);
                $res->bindValue(1, $_SESSION["username"]);
                $res->bindValue(2, $acronimo);
                $res->bindValue(3, $anno);
                $res->execute();
                if ($res->rowCount() > 0) {
                    //utente già registrato
                    echo '<h5 class="text-center text-success">Registrato</h5>';
                } else {
                    //utente NON registrato alla conferenzza
                    echo "<a href='/utilities/iscrizioneConferenza.php?Anno=$anno&Acronimo=$acronimo' class='btn btn-primary'>Iscriviti</a>";
                }

                // Tasto Modifica per gli Admin
                if (isset($_SESSION["role"]) && strcmp("Admin", $_SESSION["role"]) == 0) {
                    // è un admin
                    echo "<a href='/admin/modificaConferenza.php?Anno=$anno&Acronimo=$acronimo' class='btn btn-outline-secondary'>Modifica Conferenza</a>";
                }
            }
            ?>
        </div>
        <h2 class="text-center mt-4"><?php echo $row["Nome"] ?></h2>
        <h5 class="text-center text-secondary"><?php echo ($anno . "-" . $acronimo) ?></h5>
        <h6 class="text-center text-secondary"><?php echo ("Dal " . $row["DataInizio"] . " al " . $row["DataFine"]) ?></h6>
        <h6 class="text-center text-secondary"><?php echo ("Le sponsorizzazioni per questa conferenza sono: " . $row["Totale_Sponsorizzazioni"]) ?></h6>
        <img class="confLogo rounded mx-auto d-block" src="<?php echo $row["Logo"]; ?>" />

        <h5 class="text-center mt-2">Status:
            <?php
            if (strcmp("Attiva", $row["Svolgimento"]) == 0) {
                echo "<p class='text-success text-center'>Attiva</p>";
            } else {
                echo "<p class='text-danger text-center'>Completata</p>";
            }
            ?>
        </h5>
    </div>

    <?php
    /*
        # VARI BUG, DA RIGUARDARCI SE C'è TEMPO #
        if ($row["Totale_Sponsorizzazioni"] > 0) {
            // carosello degli sponsor
            // BUG TODO: Risolvere il Bug della Procedure GetSponsorConf
            #$sql = 'CALL GetSponsorConf(?,?)';
            $sql = "SELECT Nome, Logo, Importo FROM Sponsor JOIN Sponsorizzazione ON Nome = NomeSponsor WHERE  AcronimoConf = ? AND AnnoEdizione = ?;";
            try {
                $st = $pdo->prepare($sql);
                $st->bindParam(1, $acronimo, PDO::PARAM_STR);
                $st->bindValue(2, $anno, PDO::PARAM_INT);
                $st->execute();
            } catch (PDOException $e) {
                echo ("[ERRORE] Query SQL (Select) non riuscita. Errore: " . $e->getMessage());
                exit();
            }

        ?>

    </div>
    <div class="card text-center mx-auto" style="width: 40rem;">
        <div class="card-header">
            Sponsors
        </div>
        <div class="card-body">
            <div id="carouselExampleIndicators" class="carousel-dark slide" data-bs-ride="true">
                <div class="carousel-inner">
                    <?php
                    $primo = True;
                    while ($sponsor = $st->fetch()) {
                        // NOME LOGO IMPORTO
                        $nome = $sponsor["Nome"];
                        $logo = $sponsor["Logo"];
                        $importo = $sponsor["Importo"];
                    ?>
                        <div class="carousel-item <?php if ($primo) {
                                                        echo "active";
                                                    } ?>">

                            <img src="<?php echo $logo ?>" class="d-block imgcarosello">
                            <div class="carousel-caption d-none d-md-block">
                                <h5><?php echo $nome ?></h5>
                                <p>Importo Sponsorizzazione: <?php echo $importo ?></p>
                            </div>
                        </div>
                    <?php $primo = False;
                    } ?>

                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        <?php

        } 
         ?>
    </div>
    </div>

    */ ?>


</body>

</html>