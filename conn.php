<?php
$servername = "fdb1030.awardspace.net";
$username = "4581386_gameboxmarket";
$password = "38-%cjbp4US9MI-o";
$dbname = "4581386_gameboxmarket";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>