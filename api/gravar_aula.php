<?php
include '../conexao/conexao.php';

// Lê os dados enviados pelo fetch (JSON)
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Dados inválidos']);
    exit;
}

$uc_id = $data['uc_id'] ?? null;
$tipo_aula_id = $data['tipo_aula_id'] ?? null;
$dia = $data['dia_semana'] ?? null;
$turno = $data['turno'] ?? null;
$cor = $data['cor'] ?? '#1a2041';
$semana_inicio = $data['semana_inicio'] ?? date('Y-m-d', strtotime('monday this week')); // ✅ agora vem do front

// Validação simples
if (!$tipo_aula_id || !$dia || !$turno) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Campos obrigatórios faltando.']);
    exit;
}

try {
    // Verifica se já existe uma aula agendada para essa semana, dia e turno
    $sql = "SELECT id FROM aulas_agendadas 
            WHERE tipo_aula_id = :tipo 
              AND dia_semana = :dia 
              AND turno = :turno 
              AND semana_inicio = :semana";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([
        ':tipo' => $tipo_aula_id,
        ':dia' => $dia,
        ':turno' => $turno,
        ':semana' => $semana_inicio
    ]);
    $existe = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existe) {
        // Atualiza cor se já existir
        $sql = "UPDATE aulas_agendadas 
                SET cor = :cor 
                WHERE id = :id";
        $stmt = $conexao->prepare($sql);
        $stmt->execute([':cor' => $cor, ':id' => $existe['id']]);
    } else {
        // Busca cor padrão do tipo de aula
        $sqlCor = "SELECT cor FROM tipos_aula WHERE id = :id";
        $stmtCor = $conexao->prepare($sqlCor);
        $stmtCor->execute([':id' => $tipo_aula_id]);
        $cor = $stmtCor->fetchColumn() ?: '#1a2041';

        // Insere novo registro
        $sql = "INSERT INTO aulas_agendadas (tipo_aula_id, uc_id, dia_semana, turno, cor, semana_inicio)
        VALUES (:tipo, :uc, :dia, :turno, :cor, :semana)";
        $stmt = $conexao->prepare($sql);
        $stmt->execute([
            ':tipo' => $tipo_aula_id,
            ':uc' => $uc_id,
            ':dia' => $dia,
            ':turno' => $turno,
            ':cor' => $cor,
            ':semana' => $semana_inicio
        ]);
    }

    echo json_encode(['status' => 'ok']);
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'erro',
        'mensagem' => 'Erro no banco: ' . $e->getMessage()
    ]);
}
?>