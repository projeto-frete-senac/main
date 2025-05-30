<?php
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
    <meta name="keywords" content="frete a�reo, transporte, log�stica, AirFrete" />
    <title>AirFrete - Encontre seu frete ideal</title>
    <link rel="stylesheet" href="./styles/default.css">
</head>
<body>
    <header>
        <div class="logo">AIR FRETE</div>
        <div class="about">
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <!-- Usu�rio logado -->
                <div class="user-welcome">
                    <span>Bem-vindo, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>!</span>
                    <button><a href="ofertas.php">Minhas Ofertas</a></button>
                    <button><a href="ofertas.php?logout=1">Sair</a></button>
                </div>
            <?php else: ?>
                <!-- Usu�rio n�o logado -->
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
                        <option value="SP">S�o Paulo</option>
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
            <div class="card_Container">
                <div class="card">
                    <h3>Roberto Da Cruz</h3>
                    <div class="priceContainer">
                        <span>R$</span><p>500</p>
                    </div>
                    <div class="description">
                        Lorem ipsum dolor sit, amet consectetur adipisicing elit.
                    </div>
                </div>
            </div>
            <button class="next-btn">&rarr;</button>
        </section>
    </main>

</body>
</html>