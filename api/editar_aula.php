<?php
include '../conexao/conexao.php';

// Lê o JSON recebido
$data = json_decode(file_get_contents('php://input'), true);

$id = $data['id'] ?? null;
$tipo_aula_id = $data['tipo_aula_id'] ?? null;
$uc_id = $data['uc_id'] ?? null;
$dia = $data['dia_semana'] ?? null;
$turno = $data['turno'] ?? null;
$semana_inicio = $data['semana_inicio'] ?? null;

if (!$id || !$tipo_aula_id || !$dia || !$turno || !$semana_inicio) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Dados incompletos.']);
    exit;
}

try {
    // Atualiza todos os campos, incluindo a UC
    $sql = "UPDATE aulas_agendadas
            SET tipo_aula_id = :tipo,
                uc_id = :uc,
                dia_semana = :dia,
                turno = :turno,
                semana_inicio = :semana
            WHERE id = :id";

    $stmt = $conexao->prepare($sql);
    $stmt->execute([
        ':tipo' => $tipo_aula_id,
        ':uc' => $uc_id,
        ':dia' => $dia,
        ':turno' => $turno,
        ':semana' => $semana_inicio,
        ':id' => $id
    ]);

    echo json_encode(['status' => 'ok', 'mensagem' => 'Aula atualizada com sucesso!']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro no banco: ' . $e->getMessage()]);
}
?>
