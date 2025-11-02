<?php
include '../conexao/conexao.php';

$tipo_aula_id = $_GET['tipo_aula_id'] ?? 0;

try {
    $sql = "SELECT id, nome FROM unidades_curriculares WHERE tipo_aula_id = :tipo ORDER BY nome";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':tipo', $tipo_aula_id);
    $stmt->execute();
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    echo json_encode(['erro' => $e->getMessage()]);
}
?>
