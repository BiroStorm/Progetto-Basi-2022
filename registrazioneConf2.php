<?php

//CONNESSIONE AL DB
try {
        include 'credentials.php';
        $pdo = new PDO('mysql:host='.$dbAdress.';dbname='.$dbName, $dbUser, $dbPass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo ("[ERRORE] Connessione al DB non riuscita. Errore: " . $e->getMessage());
        exit();
    }

?>

<html>

<head>
    <h1>Conferenze disponibili</h1>
</head>

<body>
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
    <?php
    $sql = "CALL VisualizzaConferenze(1)";
    $st = $pdo->query($sql);

    echo "<select name='selezioneConferenza'>";
    if($st->rowCount() == 0) {
        echo "Nessuna conferenza disponibile";
    } else {
        $conferenze = $st->fetchAll(PDO::FETCH_OBJ);
        

        foreach($conferenze as $record) {
            //echo "Nome: " .$record->Nome. " - Acronimo: " .$record->Acronimo. " - Anno: " .$record->AnnoEdizione. "<br>";
            echo "<option value='$record->Nome'>$record->Nome</option>";
        }
        
    }
    echo "</select>";
    
    ?>

<button type="submit">Registrazione alla conferenza</button>
</form>

<?php
     $valoreConferenza = $_POST['selezioneConferenza'];
     $sql2 = 'SELECT AnnoEdizione FROM Conferenza WHERE Nome = $valoreConferenza';
     $st2 = $pdo->query($sql2);
     $annoEdizione = $st2 -> fetchAll(PDO::FETCH_OBJ);
     echo $annoEdizione;
?>

</body>





</html>