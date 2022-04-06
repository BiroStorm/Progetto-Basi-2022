<?php
session_start();
if (!isset($_SESSION['authorized'])) {
    header('Location: login.php');
    exit();
};
// utente loggato

//inserimento nel db.
$anno = $_GET["Anno"];
$acronimo = $_GET["Acronimo"];
$username = $_SESSION["username"];

include './utilities/databaseSetup.php';
//...