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
            <div class="card mt-2" style="width: 18rem; ">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $record->Nome ?></h5>
                    <h6 class="card-subtitle mb-2 text-muted"><?php echo $record->Acronimo ?>-<?php echo $record->AnnoEdizione ?></h6>
                    <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>

                    <?php
                    if (isset($_SESSION["username"])) {
                    ?>
                        <button class="card-link" onclick="iscriviUtente(<?php echo $record->AnnoEdizione . ',' . $record->Acronimo . ',' . $username ?>)">Card link</button>
                    <?php } ?>
                    <a href='<?php echo "/utilities/iscrizioneConferenza.php?Anno=$record->AnnoEdizione&Acronimo=$record->Acronimo" ?>' class="card-link">Iscriviti</a>
                    <a href='<?php echo "/conferenze/dettagli.php?Anno=$record->AnnoEdizione&Acronimo=$record->Acronimo" ?>' class="card-link">Dettagli</a>
                </div>
            </div>
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