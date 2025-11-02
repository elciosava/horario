<?php
include '../conexao/conexao.php';

try {
    $sql = "SELECT id, nome FROM cursos ORDER BY nome";
    $stmt = $conexao->prepare($sql);
    $stmt->execute();
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    echo json_encode([]);
}
?>
