<?php
header('Content-Type: text/html; charset=UTF-8');
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

session_start();

require_once 'config/db.php';

// Consulta para preencher os filtros
$estados = $pdo->query("SELECT DISTINCT origem FROM ofertas UNION SELECT DISTINCT destino FROM ofertas")->fetchAll(PDO::FETCH_COLUMN);
$valores = $pdo->query("SELECT MIN(preco) as min, MAX(preco) as max FROM ofertas WHERE status = 'ativa'")->fetch(PDO::FETCH_ASSOC);

// Valores padrão
$minValor = $valores['min'] ?? 0;
$maxValor = $valores['max'] ?? 9999;

// Filtros aplicados (GET)
$origem = $_GET['origem'] ?? '';
$destino = $_GET['destino'] ?? '';
$precoMax = $_GET['valor'] ?? $maxValor;

// Consulta com filtros
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

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$ofertas = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="AirFrete - Encontre o melhor frete aéreo com facilidade e praticidade"/>
    <meta name="keywords" content="frete aéreo, transporte, logística, AirFrete" />
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

        rangeInput.addEventListener('input', function() {
            labels[1].textContent = 'R$ ' + this.value;
        });

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
