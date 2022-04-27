<?php
$q = $_REQUEST["q"];

$hint = "";
if ($q !== "") {
    include '../utilities/databaseSetup.php';
    try {
        $sql = 'SELECT 1 FROM Utente WHERE Username=:lab1';
        $res = $pdo->prepare($sql);
        $res->bindValue(":lab1", $q);
        $res->execute();
    } catch (PDOException $e) {
        echo ("[ERRORE] Query SQL (Select) non riuscita. Errore: " . $e->getMessage());
        exit();
    }
    echo $res->rowCount() == 1 ? "Username Occupato!" : "";
}else{
    echo "";
}
?>