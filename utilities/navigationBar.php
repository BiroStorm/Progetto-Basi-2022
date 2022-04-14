<?php
// IF THE USER IS NOT LOGIN
if (isset($_SESSION['authorized'])) {
    $username = $_SESSION['username'];
};

?>

<!-- START Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="/index.php">Progetto X</a>
        <!-- boostrap in caso di schermo piccolo -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse " id="navbarNavDropdown">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" id="index" href="/index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="conferenze" href="/conferenze.php">Conferenze</a>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 profile-menu">
                <li class="nav-item dropdown">
                    <?php
                    if (isset($_SESSION['authorized'])) { ?>
                        <!-- Utente Loggato -->
                        <a class="nav-link dropdown-toggle" id="navbarDropdownMenuLink" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><?php echo $username; ?></a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">
                            <li><a class="dropdown-item" id="modificaProfilo" href="/user/modificaProfilo.php">Modifica Profilo</a></li>
                            <?php
                                // Sezione Admin
                                if (strcmp($_SESSION["role"], "Admin") == 0){
                            ?>
                            <li><a class="dropdown-item" id="" href="/user/creaConferenza.php">Crea Conferenza</a></li>
                            <?php 
                                }
                            ?>
                            <li><a class="dropdown-item" id="" href="#">Da aggiungere</a></li>
                            <li><a class="dropdown-item" href="/logout.php">Logout</a></li>
                        </ul>

                    <?php } else {
                    ?>
                        <!-- Utente NON Loggato -->
                        <a class="nav-link" id="login" href="/login.php">Login</a>
                    <?php

                    } ?>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script defer>
    document.getElementById("<?php echo basename($currentPage, '.php'); ?>").className += " active";
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<!-- END Navigation Bar -->