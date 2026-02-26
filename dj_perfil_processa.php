<?php
session_start();

if ($_SESSION["tipo"] != "dj") { 
    header("Location: login.php");
    exit;
}

require "conexao.php";

// ID do DJ logado
$id_usuario = $_POST["id_usuario"];
$estilo     = $_POST["estilo_musical"];
$preco      = $_POST["preco"];
$bio        = $_POST["bio"];
$foto       = null;

// Upload da foto
if (!empty($_FILES["foto"]["name"])) {
    $foto = time() . "_" . $_FILES["foto"]["name"];
    move_uploaded_file($_FILES["foto"]["tmp_name"], "uploads/" . $foto);
}

// Verifica se o perfil já existe
$sql = $con->prepare("SELECT id_dj, foto FROM perfis_dj WHERE id_usuario = ?");
$sql->bind_param("i", $id_usuario);
$sql->execute();
$result = $sql->get_result();
$perfilExiste = $result->fetch_assoc();

if ($perfilExiste) {

    // Se o usuário não enviou foto nova → mantém a antiga
    if (!$foto) {
        $foto = $perfilExiste["foto"];
    }

    // Atualiza o perfil
    $update = $con->prepare("
        UPDATE perfis_dj 
        SET estilo_musical = ?, preco = ?, bio = ?, foto = ?
        WHERE id_usuario = ?
    ");
    $update->bind_param("sdssi", $estilo, $preco, $bio, $foto, $id_usuario);
    $update->execute();

} else {

    // Cria um novo perfil
    $insert = $con->prepare("
        INSERT INTO perfis_dj (id_usuario, estilo_musical, preco, bio, foto)
        VALUES (?, ?, ?, ?, ?)
    ");
    $insert->bind_param("isdss", $id_usuario, $estilo, $preco, $bio, $foto);
    $insert->execute();
}

header("Location: home_dj.php");
exit;
?>
