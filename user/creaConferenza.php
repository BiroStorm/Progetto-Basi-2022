<?php
session_start();
// l'Utente deve essere loggato e DEVE essere un admin
if (!isset($_SESSION['authorized']) || !strcmp("Admin", $_SESSION["role"]) == 0) {
    //utente non autorizzato. Pagina Non autorizzato!
    header("Location: /login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
};
// è un admin...

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Creazione Conferenza</title>
</head>
<body>
    
</body>
</html>