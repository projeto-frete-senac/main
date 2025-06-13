    <?php
    header('Content-Type: text/html; charset=UTF-8');
    mb_internal_encoding('UTF-8');
    mb_http_output('UTF-8');

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

    $erro = '';
    $sucesso = '';
    $modo = isset($_GET['modo']) ? $_GET['modo'] : 'listar';
    $oferta_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    // CRIAR NOVA OFERTA
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && $modo == 'criar') {
        $origem = trim($_POST['origem']);
        $destino = trim($_POST['destino']);
        $preco = floatval($_POST['preco']);
        $descricao = trim($_POST['descricao']);
        $tipo_carga = trim($_POST['tipo_carga']);
        $peso_maximo = floatval($_POST['peso_maximo']);
        $data_disponivel = $_POST['data_disponivel'];
        $prazo_entrega = intval($_POST['prazo_entrega']);
        
        // Validações
        if (empty($origem) || empty($destino) || $preco <= 0) {
            $erro = 'Origem, destino e preço são obrigatórios.';
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO ofertas (usuario_id, origem, destino, preco, descricao, tipo_carga, peso_maximo, data_disponivel, prazo_entrega) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$_SESSION['usuario_id'], $origem, $destino, $preco, $descricao, $tipo_carga, $peso_maximo, $data_disponivel, $prazo_entrega]);
                
                $sucesso = 'Oferta criada com sucesso!';
                $modo = 'listar';
            } catch (PDOException $e) {
                $erro = 'Erro ao criar oferta. Tente novamente.';
            }
        }
    }

    // EDITAR OFERTA
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && $modo == 'editar') {
        $origem = trim($_POST['origem']);
        $destino = trim($_POST['destino']);
        $preco = floatval($_POST['preco']);
        $descricao = trim($_POST['descricao']);
        $tipo_carga = trim($_POST['tipo_carga']);
        $peso_maximo = floatval($_POST['peso_maximo']);
        $data_disponivel = $_POST['data_disponivel'];
        $prazo_entrega = intval($_POST['prazo_entrega']);
        $status = $_POST['status'];
        
        // Validações
        if (empty($origem) || empty($destino) || $preco <= 0) {
            $erro = 'Origem, destino e preço são obrigatórios.';
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE ofertas SET origem = ?, destino = ?, preco = ?, descricao = ?, tipo_carga = ?, peso_maximo = ?, data_disponivel = ?, prazo_entrega = ?, status = ? WHERE id = ? AND usuario_id = ?");
                $stmt->execute([$origem, $destino, $preco, $descricao, $tipo_carga, $peso_maximo, $data_disponivel, $prazo_entrega, $status, $oferta_id, $_SESSION['usuario_id']]);
                
                $sucesso = 'Oferta atualizada com sucesso!';
                $modo = 'listar';
            } catch (PDOException $e) {
                $erro = 'Erro ao atualizar oferta. Tente novamente.';
            }
        }
    }

    // EXCLUIR OFERTA
    if ($modo == 'excluir' && $oferta_id > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM ofertas WHERE id = ? AND usuario_id = ?");
            $stmt->execute([$oferta_id, $_SESSION['usuario_id']]);
            
            if ($stmt->rowCount() > 0) {
                $sucesso = 'Oferta excluída com sucesso!';
            } else {
                $erro = 'Oferta não encontrada.';
            }
        } catch (PDOException $e) {
            $erro = 'Erro ao excluir oferta. Tente novamente.';
        }
        $modo = 'listar';
    }

    // BUSCAR OFERTAS DO USUÁRIO
    $ofertas = [];
    if ($modo == 'listar') {
        try {
            $stmt = $pdo->prepare("SELECT * FROM ofertas WHERE usuario_id = ? ORDER BY created_at DESC");
            $stmt->execute([$_SESSION['usuario_id']]);
            $ofertas = $stmt->fetchAll();
        } catch (PDOException $e) {
            $erro = 'Erro ao carregar ofertas.';
        }
    }

    // BUSCAR OFERTA ESPECÍFICA PARA EDIÇÃO
    $oferta_atual = null;
    if ($modo == 'editar' && $oferta_id > 0) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM ofertas WHERE id = ? AND usuario_id = ?");
            $stmt->execute([$oferta_id, $_SESSION['usuario_id']]);
            $oferta_atual = $stmt->fetch();
            
            if (!$oferta_atual) {
                $erro = 'Oferta não encontrada.';
                $modo = 'listar';
            }
        } catch (PDOException $e) {
            $erro = 'Erro ao carregar oferta.';
            $modo = 'listar';
        }
    }
    ?>

    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Minhas Ofertas - AirFrete</title>
        <link rel="stylesheet" href="./styles/ofertas.css">
    </head>
    <body>
        <header>
            <div class="logo"><a href="index.php" class="home">AIR FRETE</a></div>
            <div class="about">
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <!-- Usuário logado -->
                    <div class="user-welcome">
                        <span>Bem-vindo, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>!</span>
                        <button><a href="ofertas.php">Minhas Ofertas</a></button>
                        <button><a href="ofertas.php?logout=1">Sair</a></button>
                    </div>
                <?php else: ?>
                    <!-- Usuário não logado -->
                    <button><a href="login.php">Login</a></button>
                    <button><a href="cadastro.php">Cadastro</a></button>
                <?php endif; ?>
            </div>
        </header>
        
        <?php if ($erro): ?>
            <div style="max-width: 800px; margin: 20px auto;">
                <div class="error"><?php echo htmlspecialchars($erro); ?></div>
            </div>
        <?php endif; ?>
        
        <?php if ($sucesso): ?>
            <div style="max-width: 800px; margin: 20px auto;">
                <div class="success"><?php echo htmlspecialchars($sucesso); ?></div>
            </div>
        <?php endif; ?>

        <?php if ($modo == 'listar'): ?>
            <div style="text-align: center; margin: 20px;">
                <a href="?modo=criar" class="btn" style="background: #10b981; color: white;">Nova Oferta</a>
            </div>
            
            <div class="ofertas-grid">
                <?php if (empty($ofertas)): ?>
                    <div class="no-ofertas">
                        <h3>Nenhuma oferta encontrada</h3>
                        <p>Crie sua primeira oferta de frete para começar!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($ofertas as $oferta): ?>
                        <div class="oferta-card">
                            <div class="oferta-header">
                                <?php echo htmlspecialchars($oferta['origem']); ?> → <?php echo htmlspecialchars($oferta['destino']); ?>
                                <span class="status-badge status-<?php echo $oferta['status']; ?>">
                                    <?php echo ucfirst($oferta['status']); ?>
                                </span>
                            </div>
                            
                            <div class="oferta-details">
                                <div class="detail-item">
                                    <div class="detail-label">Valor:</div>
                                    <div class="detail-value">R$ <?php echo number_format($oferta['preco'], 2, ',', '.'); ?></div>
                                </div>
                                
                                <div class="detail-item">
                                    <div class="detail-label">Tipo de Carga:</div>
                                    <div class="detail-value"><?php echo htmlspecialchars($oferta['tipo_carga'] ?? 'Não especificado'); ?></div>
                                </div>
                                
                                <div class="detail-item">
                                    <div class="detail-label">Peso Máximo:</div>
                                    <div class="detail-value"><?php echo $oferta['peso_maximo'] ? number_format($oferta['peso_maximo'], 1) . ' kg' : 'Não especificado'; ?></div>
                                </div>
                                
                                <div class="detail-item">
                                    <div class="detail-label">Prazo:</div>
                                    <div class="detail-value"><?php echo $oferta['prazo_entrega'] ? $oferta['prazo_entrega'] . ' dias' : 'Não especificado'; ?></div>
                                </div>
                            </div>
                            
                            <?php if ($oferta['descricao']): ?>
                                <div style="margin: 15px 0; color: #6b7280;">
                                    <?php echo htmlspecialchars($oferta['descricao']); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="buttons">
                                <a href="?modo=editar&id=<?php echo $oferta['id']; ?>" class="btn btn-edit">Editar</a>
                                <a href="?modo=excluir&id=<?php echo $oferta['id']; ?>" class="btn btn-delete" 
                                onclick="return confirm('Tem certeza que deseja excluir esta oferta?')">Excluir</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
        <?php elseif ($modo == 'criar' || $modo == 'editar'): ?>
            <div class="form-ofertas">
                <h2 style="margin-bottom: 15px;"><?php echo $modo == 'criar' ? 'Nova Oferta' : 'Editar Oferta'; ?></h2>
                
                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="origem">Origem *:</label>
                            <input type="text" id="origem" name="origem" required 
                                value="<?php echo $oferta_atual ? htmlspecialchars($oferta_atual['origem']) : ''; ?>"
                                placeholder="Ex: São Paulo - SP">
                        </div>
                        
                        <div class="form-group">
                            <label for="destino">Destino *:</label>
                            <input type="text" id="destino" name="destino" required 
                                value="<?php echo $oferta_atual ? htmlspecialchars($oferta_atual['destino']) : ''; ?>"
                                placeholder="Ex: Rio de Janeiro - RJ">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="preco">Preço (R$) *:</label>
                            <input type="number" id="preco" name="preco" step="0.01" min="0" required 
                                value="<?php echo $oferta_atual ? $oferta_atual['preco'] : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="tipo_carga">Tipo de Carga:</label>
                            <select id="tipo_carga" name="tipo_carga">
                                <option value="">Selecione</option>
                                <option value="Documentos" <?php echo ($oferta_atual && $oferta_atual['tipo_carga'] == 'Documentos') ? 'selected' : ''; ?>>Documentos</option>
                                <option value="Eletrônicos" <?php echo ($oferta_atual && $oferta_atual['tipo_carga'] == 'Eletrônicos') ? 'selected' : ''; ?>>Eletrônicos</option>
                                <option value="Móveis" <?php echo ($oferta_atual && $oferta_atual['tipo_carga'] == 'Móveis') ? 'selected' : ''; ?>>Móveis</option>
                                <option value="Roupas" <?php echo ($oferta_atual && $oferta_atual['tipo_carga'] == 'Roupas') ? 'selected' : ''; ?>>Roupas</option>
                                <option value="Alimentos" <?php echo ($oferta_atual && $oferta_atual['tipo_carga'] == 'Alimentos') ? 'selected' : ''; ?>>Alimentos</option>
                                <option value="Geral" <?php echo ($oferta_atual && $oferta_atual['tipo_carga'] == 'Geral') ? 'selected' : ''; ?>>Carga Geral</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="peso_maximo">Peso Máximo (kg):</label>
                            <input type="number" id="peso_maximo" name="peso_maximo" step="0.1" min="0" 
                                value="<?php echo $oferta_atual ? $oferta_atual['peso_maximo'] : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="prazo_entrega">Prazo de Entrega (dias):</label>
                            <input type="number" id="prazo_entrega" name="prazo_entrega" min="1" 
                                value="<?php echo $oferta_atual ? $oferta_atual['prazo_entrega'] : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="data_disponivel">Data Disponível:</label>
                            <input type="date" id="data_disponivel" name="data_disponivel" 
                                value="<?php echo $oferta_atual ? $oferta_atual['data_disponivel'] : ''; ?>">
                        </div>
                        
                        <?php if ($modo == 'editar'): ?>
                        <div class="form-group">
                            <label for="status">Status:</label>
                            <select id="status" name="status">
                                <option value="ativa" <?php echo ($oferta_atual && $oferta_atual['status'] == 'ativa') ? 'selected' : ''; ?>>Ativa</option>
                                <option value="pausada" <?php echo ($oferta_atual && $oferta_atual['status'] == 'pausada') ? 'selected' : ''; ?>>Pausada</option>
                                <option value="finalizada" <?php echo ($oferta_atual && $oferta_atual['status'] == 'finalizada') ? 'selected' : ''; ?>>Finalizada</option>
                            </select>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="descricao">Descrição:</label>
                        <textarea id="descricao" name="descricao" rows="4" 
                                placeholder="Descreva detalhes sobre o frete..."><?php echo $oferta_atual ? htmlspecialchars($oferta_atual['descricao']) : ''; ?></textarea>
                    </div>
                    
                    <div class="btn-group"> 
                        <button type="submit" class="btn" style="background: #10b981; color: white;">
                            <?php echo $modo == 'criar' ? 'Criar Oferta' : 'Salvar Alterações'; ?>
                        </button>
                        <a href="?modo=listar" class="btn" style="background: #6b7280; color: white;">Cancelar</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </body>
    </html>