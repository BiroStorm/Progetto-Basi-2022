<?php
include '../utilities/databaseSetup.php';
session_start();
########### [Autorizzazione] #############
if (isset($_SESSION['authorized'])) {
    // Utente loggato
    if (!strcmp("Admin", $_SESSION["role"]) == 0) {
        // ma non è un admin --> Code Error 403
        header('Location: /403.php');
        exit;
    }
} else {
    // Utente non loggato.
    header("Location: /login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit;
}
$errmsg = null;
if (isset($_POST["SetRole"])) {
    // cambiare ruolo ad un utente:
    if (!isset($_POST["username"], $_POST["ruolo"])) {
        $errmsg = "Set Ruolo, però mancano i dati username o ruolo";
    } else {
        // controllare che l'utente esiste e i ruoli già presenti dell'Utente:
        // si può fare tramite un unico controllo.
        try {
            $sql1 = "CALL CheckRuolo(?);";
            $stmt = $pdo->prepare($sql1);
            $stmt->bindValue(1, $_POST["username"], PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                $errmsg = "Username non esistente!";
            } else {
                $role = $stmt->fetch(PDO::FETCH_ASSOC)["Ruolo"];
                if (strcmp($role, $_POST["ruolo"]) == 0) {
                    // ha già quel ruolo:
                    $errmsg = "L'Utente ha già quel ruolo!";
                } else {
                    switch ($_POST["ruolo"]) {
                        case "Presenter":
                            $nrole = 1;
                            break;
                        case "Speaker":
                            $nrole = 2;
                            break;
                        case "Administrator":
                            $nrole = 3;
                            break;
                        default:
                            $nrole = 0;
                            break;
                    }
                    $sql = "CALL GiveRole(?, ?);";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(1, $_POST["username"], PDO::PARAM_STR);
                    $stmt->bindValue(2, $nrole, PDO::PARAM_INT);
                    $stmt->execute();
                    $success = TRUE;
                }
            }
        } catch (PDOException $e) {
            echo ("[ERRORE] Query non riuscita. Errore: " . $e->getMessage());
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Utenti</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

</head>

<body>
    <!-- Pagina di Essegnazione Ruoli agli Utenti -->
    <?php
    $currentPage = __FILE__;
    include "../utilities/navigationBar.php";
    ?>

    <?php
    if (isset($success)) {

    ?>
        <!-- Popup SECTION -->
        <div class="toast align-items-center text-white bg-success border-0 show" role="alert" id="Toast" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    Ruolo assegnato con successo!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close" onclick="rmToast();"></button>
            </div>
        </div>
    <?php
    } elseif (isset($errmsg)) {

    ?>
        <div class="toast align-items-center text-white bg-danger border-0 show" role="alert" id="Toast" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <?php echo $errmsg ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close" onclick="rmToast();"></button>
            </div>
        </div>
    <?php
    }
    ?>


    <div class="card text-center mx-auto mt-4" style="max-width: 18rem;">
        <div class="card-header">
            Assegnazione Ruolo
        </div>
        <div class="card-body">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                <div class="mb-3">
                    <label class="form-label">Username Utente</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <select class="form-select" name="ruolo" required>
                        <option value="Presenter" selected>Presenter</option>
                        <option value="Speaker">Speaker</option>
                        <option value="Administrator">Admin</option>
                        <option value="None">Remove All Role</option>
                    </select>
                </div>

                <input type="text" name="SetRole" hidden readonly>
                <button type="submit" class="btn btn-primary">Update User</button>
            </form>
        </div>
    </div>
    <script>
        // per qualche motivo funziona solo la parte grafica di Toast e non la parte di JS
        // quindi ci creiamo da solo la parte di JS
        var element = document.getElementById("Toast");
        document.addEventListener("DOMContentLoaded", function() {
            window.setTimeout(function() {
                if (element != null) {
                    element.style.display = 'none';
                }
            }, 5000);
        });

        function rmToast() {
            if (element != null) {
                element.style.display = 'none';
            }
        };
    </script>
</body>

</html>