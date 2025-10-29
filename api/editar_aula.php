<?php
include '../conexao/conexao.php';

$data = json_decode(file_get_contents('php://input'), true);

$id = $data['id'] ?? null;
$tipo_aula_id = $data['tipo_aula_id'] ?? null;
$dia = $data['dia_semana'] ?? null;
$turno = $data['turno'] ?? null;
$semana_inicio = $data['semana_inicio'] ?? null;

if (!$id || !$tipo_aula_id || !$dia || !$turno || !$semana_inicio) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Dados incompletos.']);
    exit;
}

try {
    $sql = "UPDATE aulas_agendadas
            SET tipo_aula_id = :tipo, dia_semana = :dia, turno = :turno, semana_inicio = :semana
            WHERE id = :id";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([
        ':tipo' => $tipo_aula_id,
        ':dia' => $dia,
        ':turno' => $turno,
        ':semana' => $semana_inicio,
        ':id' => $id
    ]);

    echo json_encode(['status' => 'ok']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'erro', 'mensagem' => $e->getMessage()]);
}
?>
