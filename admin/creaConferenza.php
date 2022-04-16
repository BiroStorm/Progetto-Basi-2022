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
if (
    isset($_POST['acronimo']) && isset($_POST["annoEdizione"]) && isset($_POST["nome"])
    && isset($_POST["logo"]) && isset($_POST["svolgimento"]) && isset($_POST["totale_Sponsorizzazioni"])
) {
    include './utilities/databaseSetup.php';
    try {
        // Controlliamo che l'Acronimo scelto sia Univoco:
        $sql = 'SELECT 1 FROM Conferenza WHERE Acronimo=:x';
        $res = $pdo->prepare($sql);
        $res->bindValue(":x", $_POST["acronimo"]);
        $res->execute();
        if ($res->rowCount() == 1) {
            // Acronimo Già Presente!
            // TODO 
        } else {

            $sql = 'INSERT INTO Conferenza(Acronimo, AnnoEdizione, Nome, Logo, Svolgimento, Totale_Sponsorizzazioni) VALUES(:x1, :x2, :x3, :x4, :x5, :x6)';
            $res = $pdo->prepare($sql);
            $res->bindValue(":x1", $_POST["acronimo"]);
            $res->bindValue(":x2", $_POST["annoEdizione"]);
            $res->bindValue(":x3", $_POST["nome"]);
            $res->bindValue(":x4", $_POST["logo"]);
            $res->bindValue(":x5", $_POST["svolgimento"]);
            $res->bindValue(":x6", $_POST["totale_Sponsorizzazioni"]);
            $res->execute();

            echo "Creazione conferenza Completata! <br> Redirect in corso...";
            header("Refresh: 1; URL=Conferenze.php");
        }
    } catch (PDOException $e) {
        echo ("[ERRORE] Query SQL (Select) non riuscita. Errore: " . $e->getMessage());
        exit();
    }
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
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
        Acronimo: <input type="text" name="acronimo" autocomplete="off" onkeyup="checkUsername(this.value)">
        <span id="result"></span>
        <br>
        Anno Edizione: <input type="text" name="annoEdizione" id="" autocomplete="off" required>
        <br>
        Nome conferenza: <input type="text" name="nome" autocomplete="off" equired>
        <br>
       <!-- Data di Nascita: <input type="date" name="data" id="" min="1920-01-01" max="2022-12-31" required> !-->
        <br>
       Svolgimento: <input type="text" name="svolgimento" required>
        <br>
        Totale sponsorizzazioni: <input type="text" name="totale_Sponsorizzazioni" required>
        <br>
</body>

</html>