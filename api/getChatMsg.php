<?php
include '../utilities/databaseSetup.php';
session_start();
if (!isset($_SESSION['authorized'])) {
  header('HTTP/1.1 403 Unauthorized');
  exit;
}

if (!isset($_POST["Offset"], $_POST["SessionID"])) {
  header('HTTP/1.1 500');
  exit;
}

$username = $_SESSION["username"];
$sessione = $_POST["SessionID"];

$sql = "SELECT OraInizio, OraFine, Giorno FROM Sessione WHERE Codice = ?";
try {
  $st = $pdo->prepare($sql);
  $st->bindParam(1, $sessione, PDO::PARAM_INT);
  $st->execute();
} catch (PDOException $e) {
  echo ("[ERRORE] Controllo Orario sessione non riuscita. Errore: " . $e->getMessage());
  exit();
}
$result = $st->fetch(PDO::FETCH_OBJ);
$today = strtotime("now");
$inizio = strtotime($result->Giorno . " " . $result->OraInizio);
$fine = strtotime($result->Giorno . " " . $result->OraFine);

if (($today < $inizio) || ($today > $fine)) {
  header('HTTP/1.1 405');
  exit;
}
$st->closeCursor();

try {
  $sql = "CALL VisualizzaMessaggi(?, ?)";
  $res = $pdo->prepare($sql);
  $res->bindValue(1, $sessione, PDO::PARAM_INT);
  $res->bindValue(2, $_POST["Offset"], PDO::PARAM_INT);
  $res->execute();
  $risposta = "";
  $offset = $_POST["Offset"];
  // se non ci sono messaggi nuovi
  if ($res->rowCount() == 0) {
    header('HTTP/1.1 204');
    echo json_encode(array($offset, $risposta));
    exit;
  }
  // risultano nuovi messaggi:

  while ($msg = $res->fetch(PDO::FETCH_OBJ)) {
    if (strcmp($msg->Mittente, $username) == 0) {
      //messaggio dell'utente:
      $risposta .= '<div class="card border-success chat-message-right mb-3">
            <div class="card-header text-success">' . $msg->Mittente . ' </div>
            <div class="card-body text-dark">
              <p class="card-text">' . $msg->Testo . '</p>
              <p class="card-text"><small class="text-muted">' . $msg->Orario . '</small></p>
            </div>
          </div>';
    } else {

      $risposta .= '<div class="card border-dark chat-message-left mb-3"">
        <div class="card-header">' . $msg->Mittente . ' </div>
        <div class="card-body text-dark">
          <p class="card-text">' . $msg->Testo . '</p>
          <p class="card-text"><small class="text-muted">' . $msg->Orario . '</small></p>
        </div>
      </div>';
    }
    $offset = $msg->NMessaggio;
  }
  echo json_encode(array($offset, $risposta));
  exit;
} catch (PDOException $e) {
  echo ("[ERRORE] VisualizzaMessaggi non riuscita. Errore: " . $e->getMessage() . "\nOffset: " . $_POST["Offset"]);
  header('HTTP/1.1 500');
  exit;
}
