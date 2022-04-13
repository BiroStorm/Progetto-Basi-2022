<?php
//CONNESSIONE AL DB
include '../utilities/databaseSetup.php';
session_start();
$username = null;
if (isset($_SESSION["username"])) {
    $username = $_SESSION["username"];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dettagli Conferenze</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<style>
    .confLogo{
        max-width: 400px;
        max-height: 400px;
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
    <?php
     if (isset($_GET["Anno"]) && isset($_GET["Acronimo"])) {
        $anno = $_GET["Anno"];
        $acronimo = $_GET["Acronimo"];
        $sql = 'SELECT Logo, Nome, Svolgimento, Totale_Sponsorizzazioni, DataInizio, DataFine, Creatore FROM Conferenza WHERE Acronimo=:x1 AND AnnoEdizione=:x2';
        // controllo eccezioni dato dal DB
        try {
            $st = $pdo->prepare($sql); //Preparazione SQL 
            $st->bindParam(":x1", $acronimo, PDO::PARAM_STR); //Inserire i valori relai nell'SQL
            $st->bindValue(":x2", $anno, PDO::PARAM_INT);
            $st->execute(); //Eseguire SQL
        } catch (PDOException $e) {
            echo ("[ERRORE] Query SQL (Select) non riuscita. Errore: " . $e->getMessage());
            exit();
        }

        if ($st->rowCount() == 1) { //Controllo del numero di righe ritornate
            $row = $st->fetch(); //Ricava la prima riga del risultato e poi la salva nella variabile $row
    ?>
    <h2 class="text-center mt-4"><?php echo $row["Nome"] ?></h2>
    <h5 class="text-center text-secondary"><?php echo ($anno."-".$acronimo) ?></h5>
    <h6 class="text-center text-secondary"><?php echo ("Dal ".$row["DataInizio"]." al ".$row["DataFine"]) ?></h6>
    
    <img class="confLogo rounded mx-auto d-block" src="<?php echo $row["Logo"];?>"/>
    
    <h5 class="text-center">Status: <?php if (strcmp("Attiva", $row["Svolgimento"])){
            echo "<p class='text-success text-center'>Attiva</p>";
        }else{
            echo "<p class='text-danger text-center'>Completata</p>";
        }
    ?>
    </h5>


    <?php
        }
    }
    ?>
</body>

</html>