<?php
require_once 'config/db.php';
header('Content-Type: text/html; charset=UTF-8');
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
session_start();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Oferta inválida.";
    exit;
}

$id = (int) $_GET['id'];

$sql = "
    SELECT o.*, u.nome AS nome_usuario, u.telefone
    FROM ofertas o
    JOIN usuario u ON o.usuario_id = u.id
    WHERE o.id = :id
";

$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);
$oferta = $stmt->fetch();

if (!$oferta) {
    echo "Oferta não encontrada.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Detalhes da Oferta - AirFrete</title>
    <link rel="stylesheet" href="styles/oferta.css" />
    <style>
        .oferta-info p {
        font-size: 16px;
        color: #4b5563;
        margin-bottom: 6px;
    }
    </style>
</head>
<body>
    <header>
        <div class="logo"><a href="index.php" class="home">AIR FRETE</a></div>
        <div class="about">
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <div class="user-welcome">
                    <span>Bem-vindo, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>!</span>
                    <button><a href="ofertas.php">Minhas Ofertas</a></button>
                    <button><a href="ofertas.php?logout=1">Sair</a></button>
                </div>
            <?php else: ?>
                <button><a href="login.php">Login</a></button>
                <button><a href="cadastro.php">Cadastro</a></button>
            <?php endif; ?>
        </div>
    </header>

    <main class="oferta-detalhes">
        <div class="oferta-card">
            <h2>Oferta de Frete</h2>
            <div class="info"><strong>Origem:</strong> <?php echo htmlspecialchars($oferta['origem']); ?></div>
            <div class="info"><strong>Destino:</strong> <?php echo htmlspecialchars($oferta['destino']); ?></div>
            <div class="info"><strong>Preço:</strong> R$ <?php echo number_format($oferta['preco'], 2, ',', '.'); ?></div>
            <div class="info"><strong>Status:</strong> <?php echo ucfirst($oferta['status']); ?></div>
            <div class="descricao">
                <strong>Descrição:</strong>
                <p><?php echo nl2br(htmlspecialchars($oferta['descricao'])); ?></p>
            </div>
            <div class="oferta-info">
                <p><strong>Responsável:</strong> <?= htmlspecialchars($oferta['nome_usuario']) ?></p>
                <p><strong>Telefone:</strong> <?= htmlspecialchars($oferta['telefone']) ?></p>
            </div>
            <div class="voltar">
                <a href="index.php" class="btn">Voltar</a>
            </div>
        </div>
    </main>
</body>
</html>
