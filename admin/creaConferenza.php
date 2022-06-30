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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Creazione Conferenza</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

</head>

<body>
    <?php
    $currentPage = __FILE__;
    include "../utilities/navigationBar.php";
    ?>
    <div class="card text-center mx-auto mt-4" style="max-width: 18rem;">
        <div class="card-header">
            Crea una Conferenza
        </div>
        <div class="card-body">
            <form action="/api/admin/aggiungiConferenza.php" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Acronimo</label>
                    <input type="text" name="acronimo" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Logo Conferenza</label>
                    <input type="file" name="logo" class="form-control form-control-sm" accept="image/png, image/jpeg, image/jpg" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Anno Edizione</label>
                    <input type="text" name="annoEdizione" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nome</label>
                    <input type="text" name="nome" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Data di inizio Conferenza</label>
                    <input type="date" name="inizio" min="1920-01-01" max="2022-12-31" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Data di fine Conferenza</label>
                    <input type="date" name="fine" min="1920-01-01" max="2022-12-31" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Crea</button>
            </form>
        </div>
    </div>
</body>

</html>