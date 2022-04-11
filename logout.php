<?php
   session_start();
   session_destroy();
   echo "Logout completato! Redirect in corso...";
   header("Refresh: 1; URL=index.php");
?>