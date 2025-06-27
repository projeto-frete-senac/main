<?php
header('Content-Type: text/html; charset=UTF-8');
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

session_start();

require_once 'config/db.php';

// Consulta para preencher os filtros (ordenado alfabeticamente)
$estados = $pdo->query("SELECT DISTINCT origem FROM ofertas UNION SELECT DISTINCT destino FROM ofertas ORDER BY 1")->fetchAll(PDO::FETCH_COLUMN);
$valores = $pdo->query("SELECT MIN(preco) as min, MAX(preco) as max FROM ofertas WHERE status = 'ativa'")->fetch(PDO::FETCH_ASSOC);

// Valores padrão
$minValor = $valores['min'] ?? 0;
$maxValor = $valores['max'] ?? 9999;

// Filtros aplicados (GET)
$origem = $_GET['origem'] ?? '';
$destino = $_GET['destino'] ?? '';
$precoMax = $_GET['valor'] ?? $maxValor;

// Consulta com filtros (ordenado do mais recente para o mais antigo)
$sql = "SELECT * FROM ofertas WHERE status = 'ativa'";
$params = [];

if ($origem) {
    $sql .= " AND origem = :origem";
    $params[':origem'] = $origem;
}

if ($destino) {
    $sql .= " AND destino = :destino";
    $params[':destino'] = $destino;
}

if (is_numeric($precoMax)) {
    $sql .= " AND preco <= :preco";
    $params[':preco'] = $precoMax;
}

// Adiciona ordenação por data/ID mais recente
$sql .= " ORDER BY id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$ofertas = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>AirFrete - Encontre seu frete ideal</title>
    <link rel="stylesheet" href="styles/index.css">
    <style>
        .btn {
            background-color: #667eea;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 16px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-weight: 500;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            margin-top: 10px;
        }

        .btn:hover {
            background-color: #764ba2;
        }

        /* Estilos adicionais para a barra de preço roxa */
        .price #range {
            width: 100%;
            height: 8px;
            border-radius: 10px;
            background: linear-gradient(45deg, #e5e7eb, #d1d5db);
            outline: none;
            -webkit-appearance: none;
            appearance: none;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }

        .price #range::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            background: linear-gradient(45deg, #667eea, #764ba2);
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            transition: all 0.3s ease;
        }

        .price #range::-webkit-slider-thumb:hover {
            transform: scale(1.2);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        .price #range::-moz-range-thumb {
            width: 25px;
            height: 25px;
            border-radius: 50%;
            background: linear-gradient(45deg, #667eea, #764ba2);
            cursor: pointer;
            border: none;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            transition: all 0.3s ease;
        }

        .price #range::-moz-range-track {
            height: 8px;
            border-radius: 10px;
            background: transparent;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo"><a href="index.php" class="home">AIR FRETE</a></div>
        <div class="about">
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <div class="user-welcome">
                    <span>Bem-vindo, <?= htmlspecialchars($_SESSION['usuario_nome']) ?>!</span>
                    <button><a href="ofertas.php">Minhas Ofertas</a></button>
                    <button><a href="ofertas.php?logout=1">Sair</a></button>
                </div>
            <?php else: ?>
                <button><a href="login.php">Login</a></button>
                <button><a href="cadastro.php">Cadastro</a></button>
            <?php endif; ?>
        </div>
    </header>

    <main class="container">
        <aside class="menu">
            <form method="get" class="cardFilter">
                <div class="field">
                    <label for="from">De:</label>
                    <select id="from" name="origem">
                        <option value="">Qualquer lugar</option>
                        <?php foreach ($estados as $estado): ?>
                            <option value="<?= htmlspecialchars($estado) ?>" <?= $estado == $origem ? 'selected' : '' ?>>
                                <?= htmlspecialchars($estado) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label for="to">Para:</label>
                    <select id="to" name="destino">
                        <option value="">Qualquer lugar</option>
                        <?php foreach ($estados as $estado): ?>
                            <option value="<?= htmlspecialchars($estado) ?>" <?= $estado == $destino ? 'selected' : '' ?>>
                                <?= htmlspecialchars($estado) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="price">
                    <label for="range">Valor:</label>
                    <input type="range" id="range" name="valor" min="<?= $minValor ?>" max="<?= $maxValor ?>" step="50" value="<?= htmlspecialchars($precoMax) ?>" />
                    <div class="range-labels">
                        <span>R$ <?= $minValor ?></span>
                        <span>R$ <?= htmlspecialchars($precoMax) ?></span>
                    </div>
                </div>
                <div style="margin-top: 10px; text-align: center;">
                    <button type="submit" class="btn">Buscar</button>
                </div>
            </form>
        </aside>

        <section class="right">
            <div class="carrossel-wrapper">
                <button id="prev-btn" class="carrossel-btn">&#10094;</button>
                <div class="carrossel" id="carrossel-ofertas">
                    <?php if (count($ofertas) > 0): ?>
                        <?php foreach ($ofertas as $oferta): ?>
                            <a href="oferta.php?id=<?= $oferta['id'] ?>" class="oferta-card-link">
                                <div class="oferta-card">
                                    <h3>De: <?= htmlspecialchars($oferta['origem']) ?></h3>
                                    <p>Para: <?= htmlspecialchars($oferta['destino']) ?></p>
                                    <p>Preço: R$ <?= number_format($oferta['preco'], 2, ',', '.') ?></p>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="oferta-card">
                            <p>Nenhuma oferta encontrada com os filtros selecionados.</p>
                        </div>
                    <?php endif; ?>
                </div>
                <button id="next-btn" class="carrossel-btn">&#10095;</button>
            </div>
        </section>
    </main>

    <script>
        const rangeInput = document.getElementById('range');
        const labels = document.querySelectorAll('.range-labels span');

        // Função para atualizar a barra de preço com preenchimento roxo
        function updateSlider() {
            const value = rangeInput.value;
            const min = rangeInput.min;
            const max = rangeInput.max;
            const percentage = ((value - min) / (max - min)) * 100;
            
            rangeInput.style.background = `linear-gradient(to right, 
                #667eea 0%, 
                #764ba2 ${percentage}%, 
                #e5e7eb ${percentage}%, 
                #d1d5db 100%)`;
            
            labels[1].textContent = 'R$ ' + value;
        }

        // Atualiza quando o usuário move o slider
        rangeInput.addEventListener('input', updateSlider);
        
        // Inicializa a barra com o valor atual
        updateSlider();

        // Carrossel
        const carrossel = document.getElementById("carrossel-ofertas");
        document.getElementById("prev-btn").addEventListener("click", () => {
            carrossel.scrollBy({ left: -300, behavior: "smooth" });
        });
        document.getElementById("next-btn").addEventListener("click", () => {
            carrossel.scrollBy({ left: 300, behavior: "smooth" });
        });
    </script>
</body>
</html>