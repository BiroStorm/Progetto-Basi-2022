<?php
session_start();
if (isset($_SESSION['authorized'])) {
    header('Location: index.php');
    exit();
};

if (isset($_POST['username']) && isset($_POST["password"])) {
    include './utilities/databaseSetup.php';
    try {
        $username = $_POST["username"];
        $password = $_POST["password"];
        $sql = 'SELECT Password FROM Utente WHERE Username=:lab1';
        $res = $pdo->prepare($sql);
        $res->bindValue(":lab1", $username);

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
            $_SESSION['authorized'] = true;
            $_SESSION['username'] = $username;
            echo ("<b> Benvenuto nel sistema, " . $username ."</b><br>Redirect in corso...");
                                            // anti xss--> quindi lo porta solo su link del sito.
            if (isset($_POST["redirect"]) && str_starts_with($_POST["redirect"], "/")) {
                header("Refresh: 1; URL=".$_POST["redirect"]);
            } else {
                header("Refresh: 1; URL=index.php");
            }
            exit();
        }
    }
    // GENERA LA PAGINA e scrive l'errore
    $err = 1;
}
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

</head>

<body>
    <div>
        <div class="card text-center mx-auto mt-4" style="max-width: 18rem;">
            <div class="card-header">
                Login
            </div>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">

                <div class="card-body">

                    Username: <input type="text" name="username">
                    Password: <input type="password" name="password">
                    <?php
                    if (isset($err)) {
                        echo '<p class="card-text">Credenziali Errate!</p>';
                    }
                    ?>
                </div>
                <div class="card-footer text-muted">
                    <input type="submit" value="Login" class="btn-primary">
                </div>
                <?php
                // se il login page è stato richiamato da un'altra pagina...
                if (isset($_GET["redirect"])) {
                    echo '<input type="hidden" name="redirect" value="' . htmlspecialchars($_GET["redirect"]) . '"/>';
                }
                ?>
            </form>
        </div>
    </div>

</body>

</html>