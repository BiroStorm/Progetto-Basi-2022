<?php
session_start();
// IF THE USER IS NOT LOGIN
if (!isset($_SESSION['authorized'])) {
    header("Location: /login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
};
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presentazioni Preferite</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
</head>

<body>
    <?php
    $currentPage = __FILE__;
    include "../utilities/navigationBar.php";
    ?>

    <h2 class="text-center mt-4">Benvenuto <?php echo $username ?></h2>
    <h4 class="text-center">Presentazioni Preferite</h4>
    <div class="card m-4">
        <h6 class="card-header">Presentazioni Salvate</h6>
        <div class="card-body">

            <?php
            include '../utilities/databaseSetup.php';
            $sql = "CALL PresentazioniPreferite(?)";
            // S.Giorno, S.Titolo AS "TitoloSessione", S.Link, PREF.CodPresentazione, 
            // P.Titolo AS "TitoloPresentazione", P.OraInizio, P.OraFine
            try {
                $st = $pdo->prepare($sql);
                $st->bindParam(1, $username);
                $st->execute();
                if ($st->rowCount() == 0) {
                    echo "Non ci sono Presentazioni Salvate";
                    return;
                } else {
            ?>
                    <table class="table">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Giorno</th>
                                <th scope="col">Conferenza</th>
                                <th scope="col">Sessione</th>
                                <th scope="col">Link Sessione</th>
                                <th scope="col">Titolo</th>
                                <th scope="col">Inizio</th>
                                <th scope="col">Fine</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $index = 0;
                            while ($presentazione = $st->fetch(PDO::FETCH_OBJ)) {
                                $index++;
                            ?>
                            <tr>
                                <td scope="row"><?php echo $index?></td>
                                <td scope="row"><?php echo $presentazione->Giorno?></td>
                                <td scope="row"><a href="/conferenze/dettagli.php?Anno=<?php echo $presentazione->AnnoEdizione?>&Acronimo=<?php echo $presentazione->AcronimoConf?>" >Conferenza</a></td>
                                <td scope="row"><?php echo $presentazione->TitoloSessione?></td>
                                <td scope="row"><a href="<?php echo $presentazione->Link?>" >Link</a></td>
                                <td scope="row"><a href="/utilities/redirectPresentazione.php?Codice=<?php echo $presentazione->CodPresentazione?>" ><?php echo $presentazione->TitoloPresentazione?></a></td>
                                <td scope="row"><?php echo $presentazione->OraInizio?></td>
                                <td scope="row"><?php echo $presentazione->OraFine?></td>
                            </tr>
                            <?php
                            }
                            ?>

                        </tbody>
                    </table>
            <?php
                }
            } catch (PDOException $e) {
                echo ("Risulta già tra i preferiti!");
                exit;
            }
            ?>
        </div>
    </div>
</body>

</html>