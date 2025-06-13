<?php
header('Content-Type: text/html; charset=UTF-8');
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

session_start();

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
?>

<!-- P�gina inicial -->
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="AirFrete - Encontre o melhor frete a�reo com facilidade e praticidade"/>
    <meta name="keywords" content="frete aéreo, transporte, logística, AirFrete" />
    <title>AirFrete - Encontre seu frete ideal</title>
    <link rel="stylesheet" href="styles/index.css">
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

    <main class="container">
        <aside class="menu">
            <div class="cardFilter">
                <div class="field">
                    <label for="from">De:</label>
                    <select id="from">
                        <option value="">Selecione</option>
                        <option value="SP">São Paulo</option>
                        <option value="RJ">Rio de Janeiro</option>
                    </select>
                </div>
                <div class="field">
                    <label for="to">Para:</label>
                    <select id="to">
                        <option value="">Selecione</option>
                        <option value="BA">Bahia</option>
                        <option value="MG">Minas Gerais</option>
                    </select>
                </div>
                <div class="price">
                    <label for="range">Valor:</label>
                    <input type="range" id="range" min="0" max="9999" step="50" />
                    <div class="range-labels">
                        <span>R$ 0</span>
                        <span>R$ 9999</span>
                    </div>
                </div>
            </div>
        </aside>

        <section class="right">
            <div class="carrossel-wrapper">
                <button id="prev-btn" class="carrossel-btn">&#10094;</button>
                <div class="carrossel" id="carrossel-ofertas">
                    <?php
                    // PHP para listar somente ofertas ativas
                    require_once 'config/db.php';
                    $sql = "SELECT * FROM ofertas WHERE status = 'ativa'";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute();
                    $ofertas = $stmt->fetchAll();
                    // Adiciona link para a página da oferta
                    // Supondo que existe uma página oferta.php?id=ID
                    // O card vira um link clicável

                    foreach ($ofertas as $oferta) {
                        echo "<a href='oferta.php?id={$oferta['id']}' class='oferta-card-link'>";
                        echo "<div class='oferta-card'>";
                        echo "<h3>De: {$oferta['origem']}</h3>";
                        echo "<p>Para: {$oferta['destino']}</p>";
                        echo "<p>Preço: R$ {$oferta['preco']}</p>";
                        echo "</div>";
                        echo "</a>";
                    }
                    ?>
                </div>
                <button id="next-btn" class="carrossel-btn">&#10095;</button>
            </div>
        </section>
    </main>

    <script>
        document.getElementById('range').addEventListener('input', function(e) {
            const value = e.target.value;
            const labels = document.querySelectorAll('.range-labels span');
            labels[1].textContent = 'R$ ' + value;
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

