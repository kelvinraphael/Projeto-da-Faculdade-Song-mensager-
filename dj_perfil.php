<?php
session_start();

if (!isset($_SESSION["tipo"]) || $_SESSION["tipo"] !== "dj") {
    header("Location: login.php");
    exit;
}

require "conexao.php";

$id_usuario = $_SESSION["id"];

$sql = $con->prepare("SELECT * FROM perfis_dj WHERE id_usuario = ?");
$sql->bind_param("i", $id_usuario);
$sql->execute();
$perfil = $sql->get_result()->fetch_assoc();

?>
<link rel="stylesheet" href="style.css">

<div class="container">
    <h2>Meu Perfil de DJ</h2>

    <form action="dj_perfil_processa.php" method="POST" enctype="multipart/form-data">

        <input type="hidden" name="id_usuario" value="<?php echo $id_usuario; ?>">

        <input type="text" name="estilo_musical" placeholder="Estilo Musical"
               value="<?php echo $perfil['estilo_musical'] ?? ''; ?>" required>

        <input type="number" step="0.01" name="preco" placeholder="Preço por evento"
               value="<?php echo $perfil['preco'] ?? ''; ?>" required>

        <textarea name="bio" placeholder="Sua descrição profissional" required><?php
            echo $perfil['bio'] ?? '';
        ?></textarea>

        <p>Foto do DJ:</p>
        <input type="file" name="foto" accept="image/*">

        <?php if (!empty($perfil['foto'])): ?>
            <p>Foto atual:</p>
            <img src="uploads/<?php echo $perfil['foto']; ?>" width="150">
        <?php endif; ?>

        <button type="submit">Salvar Perfil</button>
    </form>
</div>
