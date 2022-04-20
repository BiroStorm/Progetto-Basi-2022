<?php
//CONNESSIONE AL DB
include './utilities/databaseSetup.php';
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
    <title>Conferenze</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

</head>

<body>

    <!-- START Navigation Bar -->
    <?php
    $currentPage = __FILE__;
    include "./utilities/navigationBar.php";
    ?>
    <!-- END Navigation Bar -->
    <h1 class="text-center m-4">Conferenze disponibili</h1>
    <?php
    $sql = "CALL VisualizzaConferenze(1)";
    $st = $pdo->query($sql);

    if ($st->rowCount() == 0) {
        echo "Nessuna conferenza disponibile";
    } else {
        $conferenze = $st->fetchAll(PDO::FETCH_OBJ);


        foreach ($conferenze as $record) {
    ?>
            <div class="card mb-3">
                <div class="row g-0">
                    <div class="col-md-4">
                        <img src="<?php echo $record->Logo ?>" class="img-fluid  w-100 h-100 rounded-start" alt="...">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $record->Nome ?></h5>

                            <?php if (strcmp("Attiva", $record->Svolgimento) == 0) {
                                echo "Status: <p class='text-success'>Attiva</p>";
                            } else {
                                echo "Status: <p class='text-danger'>Completata</p>";
                            }
                            ?>

                            <p class="card-text"><small class="text-muted"><?php echo "Creatore: $record->Creatore"; ?></small></p>

                            <a href="<?php echo "/conferenze/dettagli.php?Anno=$record->AnnoEdizione&Acronimo=$record->Acronimo" ?>" class="stretched-link">Dettagli</a>
                        </div>
                    </div>
                </div>
            </div>
    <?php
        }
    }
    ?>
</body>

</html>