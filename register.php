<?php
// se è già loggato rimandiamo al index.php
session_start();
if (isset($_SESSION['authorized'])) {
    header('Location: index.php');
    exit();
};
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrazione</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

</head>

<body>
    <!-- START Navigation Bar -->
    <?php
    $currentPage = __FILE__;
    include "./utilities/navigationBar.php";
    ?>
    <!-- END Navigation Bar -->

    <div>
        <div class="card text-center mx-auto mt-4" style="max-width: 18rem;">
            <div class="card-header">
                Register Page
            </div>
            <form action="/api/registerUser.php" method="post">
                <div class="card-body" onmouseout="checkForm();">
                    <label for="" class="form-label mt-2">Username:</label>
                    <div class="input-group">
                        <div class="input-group-text">@</div>
                        <input type="text" name="username" class="form-control" autocomplete="off" onkeyup="checkUsername(this.value)">
                    </div>
                    <div id="result" class="form-text"></div>
                    <label for="" class="form-label mt-2">Nome:</label>
                    <input type="text" name="nome" id="nome" class="form-control" autocomplete="off" required>

                    <label for="" class="form-label mt-2">Cognome:</label>
                    <input type="text" name="cognome" id="cognome" class="form-control" autocomplete="off" required>

                    <label for="" class="form-label mt-2">Data di Nascita:</label>
                    <input type="date" name="data" id="dataNascita" class="form-control" id="" min="1920-01-01" max="2022-12-31" required>

                    <label for="" class="form-label mt-2">Luogo di Nascita:</label>
                    <input type="text" name="luogo" id="luogoNascita" class="form-control" required>

                    <label for="" class="form-label mt-2">Password:</label>
                    <input type="password" name="password" id="pass1" class="form-control" onchange="isValidPass();" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Deve contenere almeno 1 numero, 1 lettera maiuscola e minuscola, deve essere lungo almeno 8 caratteri!" required>
                    <div id="firstPass" class="form-text"></div>

                    <label for="" class="form-label mt-2">Conferma Password:</label>
                    <input type="password" name="passwordCheck" id="pass2" class="form-control" onchange="checkPass();" required>
                    <div id="validPass" class="form-text"></div>

                </div>
                <div class="card-footer">
                    <button type="reset" class="btn btn-outline-secondary">Clear</button>
                    <button type="submit" id="registerbtn" class="btn btn-outline-success" disabled>Registrati</button>
                </div>
            </form>
        </div>
    </div>

</body>

<script src="js/register.js"></script>

</html>

<?php
# Presenter1 : Presenter@1
# admin : admin
# Utente1 : Utente!1 
# Speaker1 : @Speaker1
# maria : HardPassword!2
# mratti : Matilde!1
?>
