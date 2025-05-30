<?php
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
            
            if ($usuario && $usuario['senha'] === $senha) {
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
</head>
<body>
    <div>
        <h1>Login - AirFrete</h1>
        
        <?php if ($erro): ?>
            <div style="color: red; margin-bottom: 15px;">
                <?php echo htmlspecialchars($erro); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div style="margin-bottom: 15px;">
                <label for="email">Email:</label><br>
                <input type="email" id="email" name="email" required 
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label for="senha">Senha:</label><br>
                <input type="password" id="senha" name="senha" required>
            </div>
            
            <div style="margin-bottom: 15px;">
                <button type="submit">Entrar</button>
            </div>
        </form>
        
        <div>
            <p>Não tem conta? <a href="cadastro.php">Cadastre-se</a></p>
        </div>
    </div>
</body>
</html>