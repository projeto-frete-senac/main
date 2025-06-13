<?php
// Configura��es de conex�o com o banco de dados
$host = 'localhost';
$dbname = 'cadastroFrete';
$username = 'root';
$password = '';

try {
    // Criar conex�o PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Configurar PDO para lançar exce��es em caso de erro
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Configurar para retornar arrays associativos por padr�o
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}
?>