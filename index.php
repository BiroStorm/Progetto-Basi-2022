<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progetto di Basi Di Dati 2022</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <link rel="stylesheet" href="css/index.css">

</head>

<body>
    <?php
    include './utilities/databaseSetup.php';

    session_start();
    $sql = 'SELECT Numero FROM NTotConferenze';
    $res = $pdo->prepare($sql);
    $res->execute();
    $NConf = ($res->fetch())["Numero"];
    $sql = 'SELECT Numero FROM NConferenzeAttive';
    $res = $pdo->prepare($sql);
    $res->execute();
    $ConfAttive = ($res->fetch())["Numero"];
    $sql = 'SELECT Numero FROM NUtenti';
    $res = $pdo->prepare($sql);
    $res->execute();
    $NUtenti = ($res->fetch())["Numero"];
    ?>

    <!-- START Navigation Bar -->
    <?php
    $currentPage = __FILE__;
    include "./utilities/navigationBar.php";
    ?>
    <!-- END Navigation Bar -->

    <!-- START MAIN BOX -->

    <h1>Progetto di Basi di Dati</h1>
    <div class="container">
        <div class="row">
            <div class="col">
                Conferenze Registrate
            </div>
            <div class="col">
                Conferenze Attive
            </div>
            <div class="col">
                Utenti Registrati
            </div>
        </div>
        <div class="row">
            <div class="col">
                <?php echo $NConf; ?>
            </div>
            <div class="col">
                <?php echo $ConfAttive; ?>
            </div>
            <div class="col">
                <?php echo $NUtenti; ?>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <table class="table table-striped">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Voto</th>
                            <th scope="col">Nome</th>
                            <th scope="col">Cognome</th>
                            <th scope="col">Username</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = 'SELECT * FROM ClassificaPresentazioni LIMIT 10';
                        $res = $pdo->prepare($sql);
                        $res->execute();
                        $counter = 1;
                        // TODO: Testare se funziona quando le tabelle vengono popolate.
                        while ($row = $res->fetch()) {
                            $str = "<tr><th scope='row'>" . $counter . "</th>";
                            $str .= "<td>" . $row["Media"] . "</td>";
                            $str .= "<td>" . $row["Nome"] . "</td>";
                            $str .= "<td>" . $row["Cognome"] . "</td>";
                            $str .= "<td>" . $row["Username"] . "</td></tr>";
                            echo $str;
                            $counter += 1;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>

</html>