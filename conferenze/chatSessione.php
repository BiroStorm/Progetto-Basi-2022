<?php
include '../utilities/databaseSetup.php';
session_start();
if (!isset($_GET["Codice"])) {
    header("Location: /conferenze.php");
    exit();
}
if (!isset($_SESSION["authorized"])) {
    header("Location: /login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

$sql = "SELECT OraInizio, OraFine, Giorno, Titolo FROM Sessione WHERE Codice = ?";
try {
    $st = $pdo->prepare($sql);
    $st->bindParam(1, $_GET["Codice"], PDO::PARAM_INT);
    $st->execute();
} catch (PDOException $e) {
    echo ("[ERRORE] Controllo esistenza sessione non riuscita. Errore: " . $e->getMessage());
    exit();
}
if ($st->rowCount() == 0) {
    header("Location: /404.php");
    exit();
}

$result = $st->fetch(PDO::FETCH_OBJ);
$username = $_SESSION['username'];
// bisogna controllare se è attiva o meno la chat:

$today = strtotime("now");
$inizio = strtotime($result->Giorno . " " . $result->OraInizio);
$fine = strtotime($result->Giorno . " " . $result->OraFine);

if (($today < $inizio) || ($today > $fine)) {
    echo "Chat di Sessione Attualmente Chiusa!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat <?php echo $result->Titolo ?></title>
    <link rel="stylesheet" href="/css/chat.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.3/font/bootstrap-icons.css">
</head>

<body>
    <?php
    $currentPage = __FILE__;
    include "../utilities/navigationBar.php";
    ?>

    <div class="content">
        <div class="container">
            <div class="card mt-4">
                <div class="card-header text-center">
                    <b>Chat <?php echo $result->Titolo ?></b>
                </div>
                <div class="position-relative">
                    <div class="chat-messages p-4">
                        <div class="d-flex justify-content-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>

                    </div>
                </div>



                <div class="py-3 px-4 card-footer">
                    <form action="#" class="formInput">
                        <div class="input-group mb-1">
                            <input type="number" name="SessionID" class="Codice" value="<?php echo $_GET["Codice"]; ?>" hidden>
                            <input type="text" name="Message" class="form-control textinput rounded" placeholder="Inserisci il tuo messaggio..." maxlength=255 autocomplete="off">
                            <button class="btn btn-success inviobnt" type="button" id="InvioMsg" disabled>Invia</button>
                        </div>
                    </form>
                </div>


            </div>
        </div>





        <!-- INPUT UTENTE MESSAGGI
        <div class="p-4 viewMessageBox chat-messages overflow-auto" id="">
            <div class="card border-dark mb-3" style="max-width: 18rem;">
                <div class="card-header">' . $msg->Mittente . ' </div>
                <div class="card-body text-dark">
                    <p class="card-text">' . $msg->Testo . '</p>
                    <p class="card-text"><small class="text-muted">' . $msg->Orario . '</small></p>
                </div>
            </div>
            <div class="card border-dark mb-3" style="max-width: 18rem;">
                <div class="card-header">' . $msg->Mittente . ' </div>
                <div class="card-body text-dark">
                    <p class="card-text">' . $msg->Testo . '</p>
                    <p class="card-text"><small class="text-muted">' . $msg->Orario . '</small></p>
                </div>
            </div>
            <div class="card border-dark mb-3" style="max-width: 18rem;">
                <div class="card-header">' . $msg->Mittente . ' </div>
                <div class="card-body text-dark">
                    <p class="card-text">' . $msg->Testo . '</p>
                    <p class="card-text"><small class="text-muted">' . $msg->Orario . '</small></p>
                </div>
            </div>
            <div class="card border-dark mb-3" style="max-width: 18rem;">
                <div class="card-header">' . $msg->Mittente . ' </div>
                <div class="card-body text-dark">
                    <p class="card-text">' . $msg->Testo . '</p>
                    <p class="card-text"><small class="text-muted">' . $msg->Orario . '</small></p>
                </div>
            </div>



        </div>

    
        <div class="position-absolute bottom-0 start-50 translate-middle-x w-75" id="">
            <form action="#" class="formInput">
                <div class="input-group mb-3">
                    <input type="number" name="SessionID" class="Codice" value="<?php echo $_GET["Codice"]; ?>" hidden>
                    <input type="text" name="Message" class="form-control textinput rounded" placeholder="Inserisci il tuo messaggio..." maxlength=255 autocomplete="off">
                    <button class="btn btn-success inviobnt" type="submit" id="InvioMsg" disabled>Invia</button>
                </div>
            </form>
        </div>
 -->

    </div>
    <script src="/js/chat.js"></script>
</body>

</html>