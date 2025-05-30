<?php
session_start();
require_once 'config/db.php';

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Função para logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas Ofertas - AirFrete</title>
</head>
<body>
    <div class="header">
        <div class="logo">AirFrete</div>
        <div class="user-info">
            <div>Bem-vindo, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>!</div>
            <div style="margin-top: 5px;">
                <a href="?logout=1" class="btn btn-logout">Sair</a>
            </div>
        </div>
    </div>
    
    <div class="oferta-card">
        <div class="oferta-header">Olá, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>!</div>
        
        <div class="oferta-details">
            <div class="detail-item">
                <div class="detail-label">Local Saída:</div>
                <div class="detail-value">Brasília - DF</div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label">Local Chegada:</div>
                <div class="detail-value">Manaus - AM</div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label">Valor:</div>
                <div class="detail-value">R$ 400,00</div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label">Status:</div>
                <div class="status-icon">?</div>
            </div>
        </div>
        
        <div class="buttons">
            <button class="btn btn-edit">Editar</button>
            <button class="btn btn-delete">Excluir</button>
        </div>
    </div>
    
    <div style="text-align: center; margin-top: 30px;">
        <a href="index.php" class="btn" style="background: #6b46c1; color: white;">Voltar ao Início</a>
    </div>
</body>
</html>