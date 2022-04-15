<?php header('HTTP/1.1 404 Not Found'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forbidden Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
</head>

<body>
    <?php
    session_start();
    $currentPage = __FILE__;
    include "./utilities/navigationBar.php";
    ?>
    <div class="container d-flex justify-content-center">
        <div>
            <h1 class="text-center text-danger">404</h1>
            <h3 class="text-center">Page Not Found</h3>
            <h5 class="text-center text-secondary">Ops... Pagina non trovata!</h5>
            <h1 class="text-center"><a href="/index.php"><i class="bi bi-arrow-left-square-fill" style="color: black;"></i></a></h1>
        
        </div>
    </div>
</body>

</html>