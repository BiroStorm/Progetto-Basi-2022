<?php
//CONNESSIONE AL DB
include './utilities/databaseSetup.php';
session_start();
$username = null;
if (isset($_SESSION["username"])) {
    $username = $_SESSION["username"];
}
?>
<html>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

</head>

<body>
    <!-- START Navigation Bar -->
    <?php
    $currentPage = __FILE__;
    include "./utilities/navigationBar.php";
    ?>
    <!-- END Navigation Bar -->

    <h1>Conferenze disponibili</h1>
    <?php
    $sql = "CALL VisualizzaConferenze(1)";
    $st = $pdo->query($sql);

    if ($st->rowCount() == 0) {
        echo "Nessuna conferenza disponibile";
    } else {
        $conferenze = $st->fetchAll(PDO::FETCH_OBJ);


        foreach ($conferenze as $record) {
    ?>      
            <div class="row no-gutters bg-light position-relative">
                <div class="col-md-6 mb-md-0 p-md-4">
                    <img src="<?php echo $record->Logo ?>" class="img-thumbnail" alt="..." style="width:800px; height: 200px;">
                </div>
            <div class="col-md-6 position-static p-4 pl-md-0">
                <h5 class="mt-0"><?php echo $record->Nome ?></h5>
                <h6 class="card-subtitle mb-2 text-muted"><?php echo $record->Acronimo ?> - <?php echo $record->AnnoEdizione ?></h6>
                <p>Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin. Cras purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce condimentum nunc ac nisi vulputate fringilla. Donec lacinia congue felis in faucibus.</p>
                <a href="<?php echo "/conferenze/dettagli.php?Anno=$record->AnnoEdizione&Acronimo=$record->Acronimo" ?>" class="stretched-link">Dettagli</a>
            </div>
            </div>
            <?php echo "<br>" ?>
    <?php
        }
    }
    ?>

    <script>
        function iscriviUtente(Anno, Acronimo, Username) {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("result").innerHTML = this.responseText;
                }
            };
            xmlhttp.open("GET", "/user/checkUsername.php?anno=" + Anno + "&acronimo=" + Acronimo + "&Username", true);
            xmlhttp.send();
        }
    </script>
</body>
</html>