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
    <!-- modifica da ADMIN -->

    <div class="position-relative">
        <div class="position-absolute top-0 end-0 mt-5 translate-middle">
            <?php

            if (isset($_SESSION["authorized"])) {
                // Aggiungi ai preferiti
                // TODO
                /*
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
                // aggiungere like
                echo "<a href='/TODOCOMPLETE.php?Anno=$tutorial->Codice' class='btn btn-primary'>Iscriviti</a>";
            }
            */
            }
            ?>
        </div>
    </div>


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
            <h6 class="text-center text-secondary "><i class="bi bi-heart fs-3 emptyheart" onclick="likePresentazione(1, this)"></i></h6>
        <?php
        } else {
        ?>
            <h6 class="text-center"><i class="bi bi-heart-fill fs-3 text-danger fullheart" onclick="likePresentazione(0, this)"></i></h6>
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

    <?php
    }
    ?>
    <script src="/js/admin/modificaTutorial.js"></script>
    <script>
        // Aggiungere ai Preferiti
        function likePresentazione(value, element) {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    if (this.responseText != "") {
                        console.log(this.responseText);
                        document.getElementById("result").textContent = this.responseText;
                    }
                }
            };
            xmlhttp.open("POST", "/api/PrefPresentazione.php", true);
            xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xmlhttp.withCredentials = true;
            xmlhttp.send("Codice=<?php echo $_GET["Codice"] ?>&add=" + value);
            if (value) {
                // cambiamo in cuore pieno
                element.className = "bi bi-heart-fill fs-3 text-danger fullheart";
                element.setAttribute("onclick", "likePresentazione(0, this)");
            } else {
                // da cuore pieno a cuore vuoto
                element.className = "bi bi-heart fs-3 emptyheart";
                element.setAttribute("onclick", "likePresentazione(1, this)");
            }
        }
    </script>
</body>

</html>