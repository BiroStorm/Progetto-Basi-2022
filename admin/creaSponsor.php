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
    <form action="" method="post">
        Nome Sponsor: <input type="text" name="nome" id="">
        Logo Sponsor: <input type="file" name="logo" id="">
        <input type="reset" value="Cancella">
        <input type="submit" value="Conferma">
    </form>
</body>

</html>