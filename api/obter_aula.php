<?php
include '../conexao/conexao.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'ID não informado']);
    exit;
}

try {
    $sql = "SELECT 
                a.id,
                a.tipo_aula_id,
                a.uc_id,
                a.dia_semana,
                a.turno,
                a.semana_inicio,
                t.professor_id,
                t.sigla,
                t.descricao,
                u.nome AS uc_nome
            FROM aulas_agendadas a
            LEFT JOIN tipos_aula t ON t.id = a.tipo_aula_id
            LEFT JOIN unidades_curriculares u ON u.id = a.uc_id
            WHERE a.id = :id";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([':id' => $id]);

    $aula = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($aula) {
        echo json_encode($aula);
    } else {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Aula não encontrada']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'erro', 'mensagem' => $e->getMessage()]);
}
?>
