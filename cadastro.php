    <?php
    header('Content-Type: text/html; charset=UTF-8');
    mb_internal_encoding('UTF-8');
    mb_http_output('UTF-8');

    session_start();
    require_once 'config/db.php';

    mb_internal_encoding('UTF-8');
    mb_http_output('UTF-8');

    $erro = '';
    $sucesso = '';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $senha = $_POST['senha'];
        $confirmar_senha = $_POST['confirmar_senha'];
        
        // Valida��es
        if (empty($username) || empty($email) || empty($senha) || empty($confirmar_senha)) {
            $erro = 'Todos os campos são obrigatórios.';
        } elseif ($senha !== $confirmar_senha) {
            $erro = 'As senhas não coincidem.';
        } elseif (strlen($senha) < 6) {
            $erro = 'A senha deve ter pelo menos 6 caracteres.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erro = 'Email inválido.';
        } else {
            try {
                // Verificar se o email j� existe
                $stmt = $pdo->prepare("SELECT id FROM usuario WHERE email = ?");
                $stmt->execute([$email]);
                
                if ($stmt->rowCount() > 0) {
                    $erro = 'Este email já está cadastrado.';
                } else {
                    // ALTERA��O: Gerar hash da senha
                    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                    
                    // Inserir novo usuário com senha hasheada
                    $stmt = $pdo->prepare("INSERT INTO usuario (nome, email, senha) VALUES (?, ?, ?)");
                    $stmt->execute([$username, $email, $senha_hash]);
                    
                    // Obter o ID do usuário rec�m-cadastrado
                    $usuario_id = $pdo->lastInsertId();
                    
                    // Fazer login autom�tico
                    $_SESSION['usuario_id'] = $usuario_id;
                    $_SESSION['usuario_nome'] = $username;
                    $_SESSION['usuario_email'] = $email;
                    
                    // Redirecionar para a p�gina de ofertas
                    header('Location: ofertas.php');
                    exit;
                }
            } catch (PDOException $e) {
                $erro = 'Erro ao cadastrar usuário. Tente novamente.';
            }
        }
    }
    ?>

    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cadastro - AirFrete</title>
        <link rel="stylesheet" href="./styles/cadastro.css">
        
    </head>
    <body>
        <div class="form-container">
            <h1>Cadastro - AirFrete</h1>
            
            <?php if ($erro): ?>
                <div class="error">
                    <?php echo htmlspecialchars($erro); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($sucesso): ?>
                <div class="success">
                    <?php echo htmlspecialchars($sucesso); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Nome de usuário:</label>
                    <input type="text" id="username" name="username" required 
                        value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                </div>
                
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
                    <label for="confirmar_senha">Confirmar senha:</label>
                    <input type="password" id="confirmar_senha" name="confirmar_senha" required>
                </div>
                
                <div class="form-group">
                    <button type="submit">Cadastrar</button>
                </div>
            </form>
            
            <div class="form-link">
                <p>Já tem conta? <a href="login.php">Fazer login</a></p>
            </div>
        </div>
    </body>
    </html>