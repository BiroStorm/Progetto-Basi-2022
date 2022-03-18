<?php
$q = $_REQUEST["q"];

$hint = "";
if ($q !== "") {
    try {
        include 'credentials.php';
        $pdo = new PDO('mysql:host=' . $dbAdress . ';dbname=' . $dbName, $dbUser, $dbPass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo ("[ERRORE] Connessione al DB non riuscita. Errore: " . $e->getMessage());
        exit();
    }
    try {
        $sql = 'SELECT 1 FROM Utente WHERE Username=:lab1';
        $res = $pdo->prepare($sql);
        $res->bindValue(":lab1", $q);
        $res->execute();
    } catch (PDOException $e) {
        echo ("[ERRORE] Query SQL (Select) non riuscita. Errore: " . $e->getMessage());
        exit();
    }
    echo $res->rowCount() == 1 ? "Username Occupato!" : "Username Libero!";
}else{
    echo "";
}
?>