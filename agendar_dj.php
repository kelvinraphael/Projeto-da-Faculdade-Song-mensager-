<?php
session_start();
if ($_SESSION["tipo"] != "cliente") { 
    header("Location: login.php"); 
    exit; 
}

require "conexao.php";

$id_dj = isset($_GET['id']) ? intval($_GET['id']) : 0;
$cliente_id = $_SESSION['id'];

$dj_sql = $con->prepare("SELECT u.nome, p.estilo_musical, p.preco, p.foto FROM usuarios u JOIN perfis_dj p ON u.id_usuario = p.id_usuario WHERE u.id_usuario = ?");
$dj_sql->bind_param("i", $id_dj);
$dj_sql->execute();
$dj = $dj_sql->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data_evento = $_POST['data_evento'];
    $horario     = $_POST['horario'];
    $local_evento= $_POST['local_evento'];
    $tipo_evento = $_POST['tipo_evento'];
    $status      = 'pendente';
    
    $insert = $con->prepare("INSERT INTO reservas (id_cliente, id_dj, data_evento, horario, status, data_reserva, local_evento, tipo_evento) VALUES (?, ?, ?, ?, ?, NOW(), ?, ?)");
    $insert->bind_param("iisssss", $cliente_id, $id_dj, $data_evento, $horario, $status, $local_evento, $tipo_evento);
    $msg = $insert->execute() ? "Reserva realizada com sucesso!" : "Erro ao agendar: ".$con->error;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Agendar DJ - <?php echo htmlspecialchars($dj['nome']); ?></title>
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:Arial, Helvetica, sans-serif;background:#f5f5f5;color:#111;}
header{width:100%;background:#fff;padding:15px 5%;display:flex;justify-content:space-between;align-items:center;box-shadow:0 2px 8px #0002;}
header h2{font-size:26px;}
nav a{ text-decoration:none;color:black;font-size:16px;padding:8px 16px;background:#28a745;border-radius:8px;border:1px solid #1e7e34;margin-left:10px;}
nav a:hover{background:#1e7e34;color:#fff;}

.container{max-width:900px;margin:40px auto;padding:20px;}
.card-dj{display:flex;gap:30px;background:#fff;border-radius:15px;box-shadow:0 0 20px rgba(0,0,0,0.1);padding:25px;align-items:center;flex-wrap:wrap;margin-bottom:30px;}
.card-dj img{width:250px;height:250px;border-radius:12px;object-fit:cover;box-shadow:0 0 12px #0002;}
.card-dj .info{flex:1;}
.card-dj .info h2{margin-bottom:10px;}
.card-dj .info p{margin-bottom:8px;font-size:16px;}

form{background:#fff;padding:25px;border-radius:15px;box-shadow:0 0 20px rgba(0,0,0,0.1);}
form h3{margin-bottom:20px;}
form label{display:block;margin-bottom:5px;font-weight:bold;}
form input, form textarea, form select{width:100%;padding:12px;margin-bottom:15px;border-radius:8px;border:1px solid #ccc;font-size:15px;}
form button{padding:12px 25px;background:#28a745;color:#fff;border:none;border-radius:8px;font-size:16px;cursor:pointer;transition:0.3s;}
form button:hover{background:#1e7e34;}
.msg{margin-bottom:15px;padding:12px;background:#d4edda;color:#155724;border-radius:8px;font-weight:bold;text-align:center;}
@media(max-width:768px){.card-dj{flex-direction:column;align-items:center;}.card-dj img{width:100%;max-width:300px;}}
</style>
</head>
<body>

<header>
    <h2>Song Manager</h2>
    <nav>
        <a href="home_cliente.php">Voltar</a>
        <a href="logout.php">Sair</a>
    </nav>
</header>

<div class="container">

    <div class="card-dj">
        <?php if(!empty($dj['foto'])): ?>
            <img src="uploads/<?php echo $dj['foto']; ?>" alt="Foto DJ">
        <?php endif; ?>
        <div class="info">
            <h2><?php echo htmlspecialchars($dj['nome']); ?></h2>
            <p><strong>Estilo:</strong> <?php echo htmlspecialchars($dj['estilo_musical']); ?></p>
            <p><strong>Preço:</strong> R$ <?php echo number_format($dj['preco'],2,',','.'); ?></p>
        </div>
    </div>

    <?php if(isset($msg)) echo "<div class='msg'>$msg</div>"; ?>

    <form method="POST">
        <h3>Agendar DJ</h3>
        <label>Data do Evento</label>
        <input type="date" name="data_evento" required>

        <label>Horário</label>
        <input type="time" name="horario" required>

        <label>Local do Evento</label>
        <input type="text" name="local_evento" placeholder="Ex: Clube XYZ" required>

        <label>Tipo de Evento</label>
        <select name="tipo_evento" required>
            <option value="">Selecione...</option>
            <option value="Festa">Festa</option>
            <option value="Casamento">Casamento</option>
            <option value="Aniversário">Aniversário</option>
            <option value="Outro">Outro</option>
        </select>

        <button type="submit">Agendar DJ</button>
    </form>

</div>

</body>
</html>
