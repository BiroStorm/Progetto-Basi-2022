<?php
session_start();
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
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Creazione Sponsor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

</head>

<body>
    <?php
    $currentPage = __FILE__;
    include "../utilities/navigationBar.php";
    ?>
    <div class="card text-center mx-auto mt-4" style="max-width: 18rem;">
        <div class="card-header">
            Crea uno Sponsor
        </div>
        <form action="/api/admin/aggiungiSponsor.php" method="post" enctype="multipart/form-data">

            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Nome Sponsor</label>
                    <input type="text" id="nomeSponsor" name="nome" class="form-control" maxlength="50" minlength="3" onchange="checkForm();" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Logo Sponsor</label>
                    <input type="file" id="inputfile" name="logo" class="form-control form-control-sm" accept="image/png, image/jpeg, image/jpg" onchange="checkForm();" required>
                </div>
            </div>
            <div class="card-footer">
                <button type="reset" class="btn btn-primary">Reset</button>
                <button type="submit" id="creabtn" class="btn btn-primary" disabled>Crea</button>
            </div>
        </form>
    </div>
    <script src="/js/imgValidator.js"></script>
    <script src="/js/admin/creaSponsor.js"></script>
</body>

</html>