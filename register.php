<?php
session_start();
if (isset($_SESSION['authorized'])) {
    header('Location: index.php');
    exit();
};

if (isset($_POST['username']) && isset($_POST["password"]) && isset($_POST["nome"]) 
    && isset($_POST["cognome"]) && isset($_POST["luogo"]) && isset($_POST["data"])) {
    try {
        include 'credentials.php';
        $pdo = new PDO('mysql:host='.$dbAdress.';dbname='.$dbName, $dbUser, $dbPass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo ("[ERRORE] Connessione al DB non riuscita. Errore: " . $e->getMessage());
        exit();
    }
    try {
        // Controlliamo che l'Username scelto sia Univoco:
        $sql = 'SELECT 1 FROM Utente WHERE Username=:x';
        $res = $pdo->prepare($sql);
        $res->bindValue(":x", $_POST["username"]);
        $res->execute();
        if ($res->rowCount() == 1){
            // Username Già Presente!
            // TODO 
        }else{
            
            $sql = 'INSERT INTO Utente(Username, Nome, Cognome, Password, DataNascita, LuogoNascita) VALUES(:x1, :x2, :x3, :x4, :x5, :x6)';
            $res = $pdo->prepare($sql);
            $res->bindValue(":x1",$_POST["username"]);
            $res->bindValue(":x2",$_POST["nome"]);
            $res->bindValue(":x3",$_POST["cognome"]);
            $password = password_hash($_POST["password"], PASSWORD_BCRYPT);
            $res->bindValue(":x4",$password);
            $res->bindValue(":x5",$_POST["data"]);
            $res->bindValue(":x6",$_POST["luogo"]);
            $res->execute();
            
            echo "Registrazione Completata! <br> Redirect in corso...";
            header("Refresh: 1; URL=login.php");
        }
    } catch (PDOException $e) {
        echo ("[ERRORE] Query SQL (Select) non riuscita. Errore: " . $e->getMessage());
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrazione</title>
</head>
<body>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
        Username: <input type="text" name="username" autocomplete="off" onkeyup="checkUsername(this.value)">
        <span id="result"></span>
        <br>
        Nome: <input type="text" name="nome" id="" autocomplete="off" required>
        <br>
        Cognome: <input type="text" name="cognome" autocomplete="off" equired>
        <br>
        Data di Nascita: <input type="date" name="data" id="" min="1920-01-01" max="2022-12-31" required>
        <br>
        Luogo di Nascita: <input type="text" name="luogo" required>
        <br>
        Password: <input type="password" name="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Deve contenere almeno 1 numero, 1 lettera maiuscola e minuscola, deve essere lungo almeno 8 caratteri!" required>
        <br>
        <!-- Ci starebbe anche un bel input password per la validazione... -->
        <!-- TODO: Validazione Form -->
        <input type="submit" value="Registrati">
        <input type="reset" value="Clear">
    </form>
</body>

<script src="js/register.js"></script>
</html>