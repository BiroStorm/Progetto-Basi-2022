<?php
if (
    isset($_POST['username']) && isset($_POST["password"]) && isset($_POST["nome"])
    && isset($_POST["cognome"]) && isset($_POST["luogo"]) && isset($_POST["data"])
) {
    include '../utilities/databaseSetup.php';
    try {
        // Controlliamo che l'Username scelto sia Univoco:
        $sql = 'SELECT 1 FROM Utente WHERE Username=:x';
        $res = $pdo->prepare($sql);
        $res->bindValue(":x", $_POST["username"]);
        $res->execute();
        if ($res->rowCount() == 1) {
            // Username Già Presente!
            header('Location: /errorPage.php?error="Username già registrato!"');
            exit();
        } else {

            $sql = 'INSERT INTO Utente(Username, Nome, Cognome, Password, DataNascita, LuogoNascita) VALUES(:x1, :x2, :x3, :x4, :x5, :x6)';
            $res = $pdo->prepare($sql);
            $res->bindValue(":x1", strtolower($_POST["username"]));
            $res->bindValue(":x2", strtolower($_POST["nome"]));
            $res->bindValue(":x3", strtolower($_POST["cognome"]));
            $password = password_hash($_POST["password"], PASSWORD_BCRYPT);
            $res->bindValue(":x4", $password);
            $res->bindValue(":x5", $_POST["data"]);
            $res->bindValue(":x6", strtolower($_POST["luogo"]));
            $res->execute();

            echo "Registrazione Completata! <br> Redirect in corso...";
            header("Refresh: 1; URL=login.php");
        }
    } catch (PDOException $e) {
        echo ("[ERRORE] Query SQL (Select) non riuscita. Errore: " . $e->getMessage());
        exit();
    }
};
?>