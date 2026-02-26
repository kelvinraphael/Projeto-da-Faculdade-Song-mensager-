<?php
require "conexao.php";

// Recebe os dados
$nome  = $_POST['nome'];
$email = $_POST['email'];
$senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); // <<< AGORA ESTÁ CRIPTOGRAFADA

// Prepara o SQL
$sql = $con->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, 'cliente')");
$sql->bind_param("sss", $nome, $email, $senha);

// Executa
if ($sql->execute()) {
    header("Location: login.php?cadastro=ok");
    exit;
} else {
    header("Location: login.php?erro=1");
    exit;
}
?>
