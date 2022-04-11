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
    <style>
        h2,
        h4 {
            text-align: center;
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
    <h2>Benvenuto <?php echo $username ?></h2>
    <h4>Modifica il tuo Account</h4>
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
    <div class="card">
        <div class="card-header">
            Informazioni Personali
        </div>
        <div class="card-body">
            <form>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" class="form-control" id="" placeholder="<?php echo $username ?>" readonly>
                    <small id="" class="form-text text-muted">L'username non può essere modificato.</small>
                </div>
                <div class="form-group">
                    <label>Nome</label>
                    <input type="text" class="form-control" id="" value="<?php echo $nome ?>">
                </div>
                <div class="form-group">
                    <label>Cognome</label>
                    <input type="text" class="form-control" id="" value="<?php echo $cognome ?>">
                </div>
                <div class="form-group">
                    <label>Data di Nascita</label>
                    <input type="date" class="form-control" id="" value="<?php echo $dataNascita ?>">
                </div>
                <div class="form-group">
                    <label>Luogo di Nascita</label>
                    <input type="text" class="form-control" id="" value="<?php echo $luogoNascita ?>">
                </div>
                <button type="submit" class="btn btn-primary">Aggiorna i dati</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            Dati Presenter e Speaker
        </div>
        <div class="card-body">
            <form>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" class="form-control" id="" placeholder="<?php echo $username ?>" readonly>
                    <small id="" class="form-text text-muted">L'username non può essere modificato.</small>
                </div>
                <div class="form-group">
                    <label>Nome</label>
                    <input type="text" class="form-control" id="" value="<?php echo $nome ?>">
                </div>
                <div class="form-group">
                    <label>Cognome</label>
                    <input type="text" class="form-control" id="" value="<?php echo $cognome ?>">
                </div>
                <div class="form-group">
                    <label>Data di Nascita</label>
                    <input type="date" class="form-control" id="" value="<?php echo $dataNascita ?>">
                </div>
                <div class="form-group">
                    <label>Luogo di Nascita</label>
                    <input type="text" class="form-control" id="" value="<?php echo $luogoNascita ?>">
                </div>
                <button type="submit" class="btn btn-primary">Aggiorna i dati</button>
            </form>
        </div>
    </div>
</body>

</html>