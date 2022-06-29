<?php
session_start();
// IF THE USER IS NOT LOGIN
if (!isset($_SESSION['authorized'])) {
    header("Location: /login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
};
$username = $_SESSION['username'];
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
    <!-- END Navigation Bar -->
    <h2 class="text-center">Benvenuto <?php echo $username ?></h2>
    <h4 class="text-center">Modifica il tuo Account</h4>
    <?php

    include '../utilities/databaseSetup.php';

    $sql = 'SELECT * FROM Utente WHERE Username = :usr1';
    $res = $pdo->prepare($sql);
    $res->bindValue(":usr1", $username);
    $res->execute();
    $row = $res->fetch();
    $nome = $row["Nome"];
    $cognome = $row["Cognome"];
    $dataNascita = $row["DataNascita"];
    $luogoNascita = $row["LuogoNascita"];
    ?>
    <div class="card m-3">
        <div class="card-header">
            Informazioni Personali
        </div>
        <div class="card-body">
            <form>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" class="form-control" id="" value="<?php echo $username ?>" readonly>
                    <small id="" class="form-text text-muted">L'username non può essere modificato.</small>
                </div>
                <div class="form-group">
                    <label>Nome</label>
                    <input type="text" class="form-control" id="" value="<?php echo $nome ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Cognome</label>
                    <input type="text" class="form-control" id="" value="<?php echo $cognome ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Data di Nascita</label>
                    <input type="date" class="form-control" id="" value="<?php echo $dataNascita ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Luogo di Nascita</label>
                    <input type="text" class="form-control" id="" value="<?php echo $luogoNascita ?>" readonly>
                </div>
            </form>
        </div>
    </div>

    <?php


    if ((strcmp($_SESSION["role"], "Speaker") == 0) || (strcmp($_SESSION["role"], "Presenter") == 0)) {

        $sql = 'SELECT Curriculum, NomeUni, Dipartimento FROM ' . $_SESSION["role"] . ' WHERE Username = :usr1';
        $res = $pdo->prepare($sql);
        $res->bindValue(":usr1", $username);
        $res->execute();
        $row = $res->fetch();
        $nomeUni = $row["NomeUni"];
        $curriculum = $row["Curriculum"];
        $dipartimento = $row["Dipartimento"];
    ?>
        <!-- SEZIONE MODIFICA DATI PRESENTER E SPEAKER -->

        <div class="card m-3">
            <div class="card-header">
                Dati Presenter e Speaker
            </div>
            <div class="card-body">
                <div class="card m-3">
                    <div class="card-header">
                        Curriculum Vitae
                    </div>
                    <div class="card-body">
                        <form action="/api/aggiornaCurriculum.php" method="post">
                            <div class="form-group">
                                <div class="mb-3">
                                    <input type="text" name="curriculum" class="form-control" maxlength="30" value="<?php echo $curriculum ?>" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Aggiorna il CV</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card m-3">
                    <div class="card-header">
                        Affiliazione Universitaria
                    </div>
                    <div class="card-body">
                        <form action="/api/aggiornaAffiliazione.php" method="post">
                            <div class="mb-3">
                                <label class="form-label">Nome Università</label>
                                <input type="text" name="nomeUni" class="form-control" value="<?php echo $nomeUni ?>" maxlength=50 required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nome Dipartimento</label>
                                <input type="text" name="dipartimento" class="form-control" value="<?php echo $dipartimento ?>" maxlength=50 required>
                            </div>
                            <button type="submit" class="btn btn-primary">Aggiorna Affiliazione</button>
                        </form>
                    </div>
                </div>
                <div class="card m-3">
                    <div class="card-header">
                        Foto
                    </div>
                    <div class="card-body">
                        <form action="/api/aggiornaFoto.php" method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <input type="file" name="fotoProfilo" class="form-control" accept="image/png, image/jpeg, image/jpg" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Carica Foto</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }
    ?>
</body>

</html>