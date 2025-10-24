<?php
include '../conexao/conexao.php';

$sql = "SELECT a.id, p.nome AS professor, a.turno, a.dia_semana, a.descricao, a.cor 
        FROM aulas a
        INNER JOIN professores p ON a.professor_id = p.id
        ORDER BY p.nome, 
                 FIELD(a.turno, 'Manhã','Tarde','Noite'),
                 FIELD(a.dia_semana, 'Seg','Ter','Qua','Qui','Sex','Sab','Dom')";
$stmt = $conexao->prepare($sql);
$stmt->execute();

$aulas = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($aulas);
?>
