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
            $password = password_hash($_POST["password"], PASSWORD_BCRYPT);
            $sql = 'CALL Registrazione(:x1, :x2, :x3, :x4, :x5, :x6)';
            $res = $pdo->prepare($sql);
            $res->bindValue(":x1", strip_tags(strtolower($_POST["username"])));
            $res->bindValue(":x2", $password);
            $res->bindValue(":x3", strip_tags(strtolower($_POST["nome"])));
            $res->bindValue(":x4", strip_tags(strtolower($_POST["cognome"])));
            $res->bindValue(":x5", $_POST["data"]);
            $res->bindValue(":x6", strip_tags(strtolower($_POST["luogo"])));
            $res->execute();

            if ($res->rowCount() == 0) {

                // INSERIMENTO LOG IN MONGO
                include_once "../utilities/mongoDBSetup.php";
                $mongodb->Users->insertOne(
                    [
                        "action" => "New User",
                        "username" => strip_tags(strtolower($_POST["username"])),
                        "data" => date("Y-m-d H:i:s", time())
                    ]
                );
                // END LOG IN MONGO;

                echo "Registrazione Completata! <br> Redirect in corso...";
                header("Refresh: 1; URL=/login.php");
                exit;
            } else {
                header('Location: /errorPage.php?error="Errore durante la Registrazione"');
                exit;
            }
        }
    } catch (PDOException $e) {
        echo ("[ERRORE] Stored Procedure (Registrazione) non riuscita. Errore: " . $e->getMessage());
        exit();
    }
};
