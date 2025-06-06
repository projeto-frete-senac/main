<?php

header('Content-Type: text/html; charset=UTF-8');
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

session_start();
require_once 'config/db.php';

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

$erro = '';

// Se já estiver logado, redirecionar para ofertas
if (isset($_SESSION['usuario_id'])) {
    header('Location: ofertas.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    
    // Validações básicas
    if (empty($email) || empty($senha)) {
        $erro = 'Email e senha são obrigatórios.';
    } else {
        try {
            // Buscar usuário no banco
            $stmt = $pdo->prepare("SELECT id, nome, email, senha FROM usuario WHERE email = ?");
            $stmt->execute([$email]);
            $usuario = $stmt->fetch();
            
            // ALTERAÇÃO: Verificar senha usando password_verify
            if ($usuario && password_verify($senha, $usuario['senha'])) {
                // Login bem-sucedido
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_email'] = $usuario['email'];
                
                header('Location: ofertas.php');
                exit;
            } else {
                $erro = 'Email ou senha incorretos.';
            }
        } catch (PDOException $e) {
            $erro = 'Erro ao fazer login. Tente novamente.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AirFrete</title>
    <link rel="stylesheet" href="./styles/default.css">
    <style>
        /* Reset e configurações globais */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Arial', sans-serif;
    background: linear-gradient(135deg, #e5e7eb 0%, #f3f4f6 100%);
    min-height: 100vh;
    color: #333;
}

/* Header */
header {
    background: linear-gradient(135deg, #e5e7eb 0%, #f3f4f6 100%);
    padding: 20px 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.logo {
    font-size: 32px;
    font-weight: bold;
    background: linear-gradient(45deg, #8b5cf6, #3b82f6);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    text-shadow: none;
}

.about {
    display: flex;
    gap: 15px;
    align-items: center;
}

.user-welcome {
    display: flex;
    align-items: center;
    gap: 15px;
}

.user-welcome span {
    color: #4b5563;
    font-weight: 500;
}

/* Botões do header */
header button {
    background: #1d4ed8;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 25px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 500;
}

header button:hover {
    background: #1e40af;
    transform: translateY(-2px);
}

header button a {
    color: white;
    text-decoration: none;
}

/* Container principal */
.container {
    display: flex;
    padding: 40px;
    gap: 30px;
    max-width: 1200px;
    margin: 0 auto;
}

/* Menu lateral (filtros) */
.menu {
    flex: 0 0 300px;
}

.cardFilter {
    background: white;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.field {
    margin-bottom: 20px;
}

.field label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #374151;
}

.field select {
    width: 100%;
    padding: 12px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.3s ease;
}

.field select:focus {
    outline: none;
    border-color: #3b82f6;
}

.price {
    margin-top: 25px;
}

.price label {
    display: block;
    margin-bottom: 15px;
    font-weight: 600;
    color: #374151;
}

.price input[type="range"] {
    width: 100%;
    margin-bottom: 10px;
}

.range-labels {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    color: #6b7280;
}

/* Área principal (cards) */
.right {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 20px;
}

.card_Container {
    flex: 1;
}

.card {
    background: white;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    transition: transform 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
}

.card h3 {
    color: #1f2937;
    margin-bottom: 15px;
    font-size: 18px;
}

.priceContainer {
    display: flex;
    align-items: baseline;
    margin-bottom: 15px;
}

.priceContainer span {
    font-size: 16px;
    color: #6b7280;
    margin-right: 5px;
}

.priceContainer p {
    font-size: 24px;
    font-weight: bold;
    color: #1d4ed8;
    margin: 0;
}

.description {
    color: #6b7280;
    font-size: 14px;
    line-height: 1.5;
}

.next-btn {
    background: #1d4ed8;
    color: white;
    border: none;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    font-size: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.next-btn:hover {
    background: #1e40af;
    transform: scale(1.1);
}

/* Formulários (Login/Cadastro) */
.form-container {
    max-width: 400px;
    margin: 50px auto;
    background: white;
    padding: 40px;
    border-radius: 15px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
}

.form-container h1 {
    text-align: center;
    margin-bottom: 30px;
    background: linear-gradient(45deg, #8b5cf6, #3b82f6);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #374151;
}

.form-group input {
    width: 100%;
    padding: 12px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.3s ease;
}

.form-group input:focus {
    outline: none;
    border-color: #3b82f6;
}

.form-group button {
    width: 100%;
    background: #1d4ed8;
    color: white;
    border: none;
    padding: 12px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.3s ease;
}

.form-group button:hover {
    background: #1e40af;
}

.form-link {
    text-align: center;
    margin-top: 20px;
}

.form-link a {
    color: #1d4ed8;
    text-decoration: none;
}

.form-link a:hover {
    text-decoration: underline;
}

.error {
    background: #fee2e2;
    color: #dc2626;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 20px;
    border: 1px solid #fecaca;
}

.success {
    background: #d1fae5;
    color: #059669;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 20px;
    border: 1px solid #a7f3d0;
}

/* Página de Ofertas */
.header {
    background: linear-gradient(135deg, #e5e7eb 0%, #f3f4f6 100%);
    padding: 20px 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    margin-bottom: 40px;
}

.user-info {
    text-align: right;
    color: #4b5563;
}

.oferta-card {
    max-width: 900px;
    margin: 0 auto;
    background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 100%);
    border-radius: 20px;
    padding: 40px;
    color: white;
    box-shadow: 0 10px 40px rgba(29, 78, 216, 0.3);
}

.oferta-header {
    font-size: 28px;
    font-weight: bold;
    margin-bottom: 30px;
    text-align: left;
}

.oferta-details {
    background: linear-gradient(135deg, #8b5cf6 0%, #a855f7 100%);
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 30px;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    align-items: center;
}

.detail-item {
    text-align: center;
}

.detail-label {
    font-size: 14px;
    opacity: 0.9;
    margin-bottom: 8px;
}

.detail-value {
    font-size: 18px;
    font-weight: bold;
}

.status-icon {
    background: #22c55e;
    color: white;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    font-weight: bold;
}

.buttons {
    display: flex;
    gap: 15px;
    justify-content: flex-start;
}

.btn {
    padding: 12px 25px;
    border: none;
    border-radius: 25px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s ease;
}

.btn-edit {
    background: white;
    color: #1d4ed8;
}

.btn-edit:hover {
    background: #f8fafc;
    transform: translateY(-2px);
}

.btn-delete {
    background: #ef4444;
    color: white;
}

.btn-delete:hover {
    background: #dc2626;
    transform: translateY(-2px);
}

.btn-logout {
    background: #ef4444;
    color: white;
    padding: 8px 16px;
    font-size: 14px;
}

.btn-logout:hover {
    background: #dc2626;
}

/* Responsividade */
@media (max-width: 768px) {
    .container {
        flex-direction: column;
        padding: 20px;
    }
    
    .menu {
        flex: none;
    }
    
    header {
        padding: 15px 20px;
        flex-direction: column;
        gap: 15px;
    }
    
    .logo {
        font-size: 24px;
    }
    
    .oferta-details {
        grid-template-columns: 1fr;
    }
    
    .buttons {
        justify-content: center;
    }
}
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Login - AirFrete</h1>
        
        <?php if ($erro): ?>
            <div class="error">
                <?php echo htmlspecialchars($erro); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required 
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            
            <div class="form-group">
                <button type="submit">Entrar</button>
            </div>
        </form>
        
        <div class="form-link">
            <p>Não tem conta? <a href="cadastro.php">Cadastre-se</a></p>
        </div>
    </div>
</body>
</html>