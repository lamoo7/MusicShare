<?php

session_start();
 
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

$filename = $_FILES['file']['name'];

$location = "images/".$filename;

$temp = explode(".", $_FILES["file"]["name"]);
$newfilename = $_SESSION["username"] . '.jpg';
move_uploaded_file($_FILES["file"]["tmp_name"], "images/" . $newfilename);

header("location: index.php");