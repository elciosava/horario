<?php
include '../conexao/conexao.php';

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'] ?? null;

if (!$id) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'ID não informado.']);
    exit;
}

try {
    $sql = "DELETE FROM aulas_agendadas WHERE id = :id";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([':id' => $id]);

    echo json_encode(['status' => 'ok']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'erro', 'mensagem' => $e->getMessage()]);
}
?>
