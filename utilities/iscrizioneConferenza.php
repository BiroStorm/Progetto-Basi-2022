<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php
    session_start();
    if (!isset($_SESSION['authorized'])) {
        header('Location: /login.php');
        exit();
    };
    // utente loggato

    //inserimento nel db.
    $anno = $_GET["Anno"];
    $acronimo = $_GET["Acronimo"];
    $username = $_SESSION["username"];

    include '../utilities/databaseSetup.php';
    //...

    echo "test";
    ?>
</body>

</html>