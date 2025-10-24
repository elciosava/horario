<?php
include '../conexao/conexao.php';
$professor_id = $_GET['professor_id'] ?? 0;

$sql = "SELECT id, sigla, descricao, cor 
        FROM tipos_aula 
        WHERE professor_id = :professor_id 
        ORDER BY sigla";
$stmt = $conexao->prepare($sql);
$stmt->execute([':professor_id' => $professor_id]);

$aulas = $stmt->fetchAll(PDO::FETCH_ASSOC);
header('Content-Type: application/json');
echo json_encode($aulas);
?>
