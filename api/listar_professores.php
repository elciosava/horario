<?php
include 'conexao/conexao.php';

$sql = "SELECT nome FROM professores ORDER BY nome";
$stmt = $conexao->prepare($sql);
$stmt->execute();

$professores = $stmt->fetchAll(PDO::FETCH_COLUMN);

header('Content-Type: application/json');
echo json_encode($professores);
?>
