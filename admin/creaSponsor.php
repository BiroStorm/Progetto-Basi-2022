<?php
session_start();
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

// if $_POST is set:
if (isset($_POST["nome"])) {
    
    $target_dir = __DIR__."/../assets/imgs/sponsor/";
    $targetfinale = $target_dir . basename($_FILES["logo"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetfinale, PATHINFO_EXTENSION));

    if (UPLOAD_ERR_OK !== $_FILES["logo"]['error']) {
        //errore nell'upload
    } else {
        $check = getimagesize($_FILES["logo"]["tmp_name"]);
        if ($check == false || ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg")) {
            // non è un img
            $uploadOk = 0;
        } else {
            if ($_FILES["logo"]["size"] > 400000) {
                // file troppo grande!
            } else {
                if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target_dir . $_POST["nome"] .".". $imageFileType)) {
                    echo "uploaded";
                } else {
                    //errore con l'uploading del file
                    echo "error";
                }
            }
        }
    }
    exit();
}


?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Creazione Sponsor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

</head>

<body>
    <?php
    $currentPage = __FILE__;
    include "../utilities/navigationBar.php";
    ?>
    <div class="card text-center mx-auto mt-4" style="max-width: 18rem;">
        <div class="card-header">
            Crea uno Sponsor
        </div>
        <div class="card-body">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Nome Sponsor</label>
                    <input type="text" name="nome" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Logo Sponsor</label>
                    <input type="file" name="logo" class="form-control form-control-sm" accept="image/png, image/jpeg, image/jpg" required>
                </div>
                <button type="submit" class="btn btn-primary">Crea</button>
            </form>
        </div>
    </div>
</body>

</html>