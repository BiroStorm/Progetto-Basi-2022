<?php
session_start();
if (isset($_SESSION['authorized'])) {
    header('Location: index.php');
    exit();
};

if (isset($_POST['username']) && isset($_POST["password"])) {
    try {
        include 'credentials.php';
        $pdo = new PDO('mysql:host='.$dbAdress.';dbname='.$dbName, $dbUser, $dbPass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo ("[ERRORE] Connessione al DB non riuscita. Errore: " . $e->getMessage());
        exit();
    }
    try {
        $username = $_POST["username"];
        $password = $_POST["password"];
        $sql = 'SELECT Password FROM Utente WHERE Username=:lab1';
        $res = $pdo->prepare($sql);
        $res->bindValue(":lab1",$username);
        
        $res->execute();
    } catch (PDOException $e) {
        echo ("[ERRORE] Query SQL (Select) non riuscita. Errore: " . $e->getMessage());
        exit();
    }

    if ($res->rowCount() == 1) {
        $row = $res->fetch();
        // controlliamo se le password coincidono:
        if (password_verify($password, $row["Password"])) {
            //Login Avvenuto con successo!
            $_SESSION['authorized'] = TRUE;
            $_SESSION['username'] = $username;
            echo ("<b> Benvenuto nel sistema, " . $username . "</b>");
            exit();
        }
    }
    // GENERA LA PAGINA e scrive l'errore
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>

<body>
    <?php
        // continuo dell'ultimo Else statement:
        echo "Login Fallito!<br>";
    }
    ?>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
        Username: <input type="text" name="username">
        <br>
        Password: <input type="password" name="password">
        <br>
        <input type="submit" value="Login">
    </form>

</body>

</html>