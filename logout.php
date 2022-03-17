<?php
   session_start();
   session_destroy();
   echo "Logout completato! Redirect in corso...";
   header("Refresh: 2; URL=index.php");
?>