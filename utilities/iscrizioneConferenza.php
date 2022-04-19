<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iscrizione alla conferenza</title>
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

    $sql = "SELECT 1 From Registrazione WHERE AnnoEdizione = :an AND AcronimoConf = :ac AND UsernameUtente = :us";
    
    $res = $pdo->prepare($sql);
    $res->bindValue(":an", $anno);
    $res->bindValue(":ac", $acronimo);
    $res->bindValue(":us", $username);
    $res->execute();

    if ($res->rowCount() > 0) {
        echo "Sei già registrato a questa conferenza.";
    } else {

    //metti la stored
    $sql = 'INSERT INTO Registrazione VALUES (:an, :ac, :us)';
        $res = $pdo->prepare($sql);
        $res->bindValue(":an", $anno);
        $res->bindValue(":ac", $acronimo);
        $res->bindValue(":us", $username);

        $res->execute();
        echo "Registrato correttamente!";

    }
    
    echo " Sarai rendeirizzato automaticamente alla pagina delle conferenze...";
    header("Refresh: 2; URL=http://localhost/conferenze.php");
    ?>
</body>

</html>