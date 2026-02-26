<?php
session_start();
require "conexao.php";

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'dj') {
    header("Location: login.php");
    exit;
}

if (!isset($_POST['id_reserva'], $_POST['novo_status'])) {
    $_SESSION['msg_status'] = "Requisição inválida.";
    header("Location: agendamentos_dj.php");
    exit;
}

$id_reserva = (int) $_POST['id_reserva'];
$novo_status = $_POST['novo_status'];

$allowed = ['aprovado','recusado','pendente'];
if (!in_array($novo_status, $allowed)) {
    $_SESSION['msg_status'] = "Status inválido.";
    header("Location: agendamentos_dj.php");
    exit;
}

// Atualiza no banco
$stmt = $con->prepare("UPDATE reservas SET status = ? WHERE id_reserva = ?");
if ($stmt === false) {
    $_SESSION['msg_status'] = "Erro no banco: " . $con->error;
    header("Location: agendamentos_dj.php");
    exit;
}
$stmt->bind_param("si", $novo_status, $id_reserva);
$stmt->execute();

$_SESSION['msg_status'] = ($stmt->affected_rows > 0) ? "Reserva atualizada para: $novo_status" : "Nenhuma alteração realizada.";
header("Location: agendamentos_dj.php");
exit;
?>
