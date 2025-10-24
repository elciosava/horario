<?php
// Inclui a conexão (volta um nível pois este arquivo está dentro da pasta /api)
include '../conexao/conexao.php';

try {
    // Busca todos os professores
    $sql = "SELECT id, nome FROM professores ORDER BY nome";
    $stmt = $conexao->prepare($sql);
    $stmt->execute();

    // Retorna o resultado como array associativo (id + nome)
    $professores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retorna em formato JSON
    header('Content-Type: application/json');
    echo json_encode($professores);
    
} catch (PDOException $e) {
    // Em caso de erro, retorna um JSON de erro
    http_response_code(500);
    echo json_encode([
        'erro' => true,
        'mensagem' => 'Erro ao listar professores: ' . $e->getMessage()
    ]);
}
?>
