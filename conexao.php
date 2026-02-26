<?php
$con = new mysqli("localhost", "root", "", "sistema_djs");

if ($con->connect_error) {
    die("ERRO CONEXÃO: " . $con->connect_error);
}
?>
