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
$uploadOk = 1;
if (
    isset($_POST['acronimo']) && isset($_POST["annoEdizione"]) && isset($_POST["nome"])
    && isset($_POST["inizio"]) && isset($_POST["fine"]) && isset($_FILES["logo"])
) {
    // controllo date inizio < fine:
    if ($_POST["inizio"] > $_POST["fine"]) {
        // errore
        // TODO:
        $uploadOk = 0;
    }


    include '../utilities/databaseSetup.php';
    try {
        // Controlliamo che l'Acronimo scelto sia Univoco:
        $sql = 'SELECT 1 FROM Conferenza WHERE Acronimo=? AND AnnoEdizione = ?';
        $res = $pdo->prepare($sql);
        $res->bindValue(1, $_POST["acronimo"]);
        $res->bindValue(2, $_POST["annoEdizione"]);
        $res->execute();
        if ($res->rowCount() == 1) {
            // Acronimo Già Presente!
            // TODO 
            $uploadOk = 0;
        } else {

            $sql = 'CALL NuovaConferenza(?, ?, ?, ?, ?, ?, ?)';
            $res = $pdo->prepare($sql);

            $logopath = "/assets/imgs/conferenza/default.png";
            $res->bindValue(1, $_SESSION["username"]);
            $res->bindValue(2, $_POST["acronimo"]);
            $res->bindValue(3, $_POST["annoEdizione"]);
            $res->bindParam(4, $logopath);
            $res->bindValue(5, $_POST["nome"]);
            $res->bindValue(6, $_POST["inizio"]);
            $res->bindValue(7, $_POST["fine"]);

            // SETUP LOADING LOGO
            $target_dir = __DIR__ . "/../assets/imgs/conferenza/";
            $targetfinale = $target_dir . basename($_FILES["logo"]["name"]);

            $imageFileType = strtolower(pathinfo($targetfinale, PATHINFO_EXTENSION));

            if (UPLOAD_ERR_OK !== $_FILES["logo"]['error']) {
                //errore nell'upload
            } else {
                $check = getimagesize($_FILES["logo"]["tmp_name"]);
                if ($check == false || ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg")) {
                    // non è un img
                } else {
                    if ($_FILES["logo"]["size"] > 800000) {
                        // file troppo grande!
                        echo "file troppo grande!";
                        exit;

                    } else {
                        if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target_dir . $_POST["acronimo"] . $_POST["annoEdizione"] . "." . $imageFileType)) {
                            $logopath = "/assets/imgs/conferenza/" . $_POST["acronimo"] . $_POST["annoEdizione"] . "." . $imageFileType;
                        } else {
                            //errore con l'uploading del file
                            echo "error";
                        }
                    }
                }
            }

            if ($res->execute()) {
                echo "Creazione conferenza Completata! <br> Redirect in corso...";
                header("Refresh: 1; URL=/conferenze.php");
            } else {
                // esecuzione fallita...
            }
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
   <div class="card text-center mx-auto mt-4" style="max-width: 18rem;">
    <div class="card-header">
        Crea una Conferenza
    </div>
    <div class="card-body">
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Acronimo</label>
                <input type="text" name="acronimo" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Logo Sponsor</label>
                <input type="file" name="logo" class="form-control form-control-sm" accept="image/png, image/jpeg, image/jpg" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Anno Edizione</label>
                <input type="text" name="annoEdizione" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Nome</label>
                <input type="text" name="nome" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Data di inizio Conferenza</label>
                <input type="date" name="inizio" min="1920-01-01" max="2022-12-31" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Data di fine Conferenza</label>
                <input type="date" name="fine" min="1920-01-01" max="2022-12-31" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Crea</button>
        </form>
    </div>
</div>
        <?php
        if ($uploadOk == 0) {
            //stampa qualcosa//
        }
        ?>
        <br>
</body>

</html>