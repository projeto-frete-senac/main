<?php
require_once 'config/db.php';
$erro = '';
$sucesso = '';
$email_enviado = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $etapa = $_POST['etapa'];

    if ($etapa === 'verificar_email') {
        $email = trim($_POST['email']);
        $stmt = $pdo->prepare("SELECT id FROM usuario WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if ($usuario) {
            $email_enviado = true;
        } else {
            $erro = "Email não encontrado.";
        }

    } elseif ($etapa === 'alterar_senha') {
        $email = $_POST['email'];
        $nova = $_POST['nova_senha'];
        $confirma = $_POST['confirmar_senha'];

        if ($nova !== $confirma) {
            $erro = "As senhas não coincidem.";
            $email_enviado = true;
        } elseif (strlen($nova) < 6) {
            $erro = "A senha deve ter pelo menos 6 caracteres.";
            $email_enviado = true;
        } else {
            $nova_hash = password_hash($nova, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE usuario SET senha = ? WHERE email = ?");
            $stmt->execute([$nova_hash, $email]);
            $sucesso = "Senha redefinida com sucesso. <a href='login.php'>Fazer login</a>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Esqueceu a Senha</title>
    <link rel="stylesheet" href="./styles/login.css">
</head>
<body>
<div class="form-container">
    <h1>Recuperar Senha</h1>

    <?php if ($erro): ?>
        <div class="error"><?php echo htmlspecialchars($erro); ?></div>
    <?php endif; ?>

    <?php if ($sucesso): ?>
        <div class="success"><?php echo $sucesso; ?></div>
    <?php endif; ?>

    <?php if (!$sucesso && !$email_enviado): ?>
        <!-- Etapa 1: solicitar email -->
        <form method="POST">
            <input type="hidden" name="etapa" value="verificar_email">
            <div class="form-group">
                <label for="email">Digite seu email cadastrado:</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div class="form-group">
                <button type="submit">Continuar</button>
            </div>
        </form>

    <?php elseif (!$sucesso && $email_enviado): ?>
        <!-- Etapa 2: alterar senha -->
        <form method="POST">
            <input type="hidden" name="etapa" value="alterar_senha">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($_POST['email']); ?>">
            <div class="form-group">
                <label>Nova senha:</label>
                <input type="password" name="nova_senha" required>
            </div>
            <div class="form-group">
                <label>Confirmar nova senha:</label>
                <input type="password" name="confirmar_senha" required>
            </div>
            <div class="form-group">
                <button type="submit">Redefinir Senha</button>
            </div>
        </form>
    <?php endif; ?>

    <div class="form-link">
        <p><a href="login.php">Voltar ao login</a></p>
    </div>
</div>
</body>
</html>
