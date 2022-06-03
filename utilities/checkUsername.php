<?php
$username = $_GET["username"];
if (isset($_GET["tipologia"])) {
    $tipo = $_GET["tipologia"];
}
$hint = "";
if ($username !== "") {
    include 'databaseSetup.php';
    try {
        if (isset($tipo)) {
            switch ($tipo) {
                case "Speaker":
                    $sql = 'SELECT 1 FROM Speaker WHERE Username=:lab1';
                    break;
                case "Presenter":
                    $sql = 'SELECT 1 FROM Presenter WHERE Username=:lab1';
                    break;
                default:
                    $sql = 'SELECT 1 FROM Utente WHERE Username=:lab1';
                    break;
            }
        } else {
            $sql = 'SELECT 1 FROM Utente WHERE Username=:lab1';
        }
        $res = $pdo->prepare($sql);
        $res->bindValue(":lab1", $username);
        $res->execute();
    } catch (PDOException $e) {
        echo ("[ERRORE] Query SQL (Select) non riuscita. Errore: " . $e->getMessage());
        exit();
    }
    echo $res->rowCount() == 1 ? "Username Occupato!" : "";
} else {
    echo "";
}
