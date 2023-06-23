<?php

require_once 'config.php';

$id = mysqli_real_escape_string($link, $_POST["id"]);
$sql = "UPDATE songs SET likes = likes + 1 WHERE id = $id";
$link->query($sql);
echo mysqli_affected_rows($link);

$link->close();
?>
