<?php
include '../conexao/conexao.php';

// 🔹 Lê o parâmetro enviado pela URL (?semana_inicio=YYYY-MM-DD)
$semana_inicio = $_GET['semana_inicio'] ?? date('Y-m-d', strtotime('monday this week'));

try {
    $sql = "SELECT a.id, t.sigla, t.descricao, t.cor, 
                   a.dia_semana, a.turno, p.nome AS professor
            FROM aulas_agendadas a
            JOIN tipos_aula t ON a.tipo_aula_id = t.id
            JOIN professores p ON t.professor_id = p.id
            WHERE a.semana_inicio = :semana
            ORDER BY p.nome, a.turno";

    $stmt = $conexao->prepare($sql);
    $stmt->execute([':semana' => $semana_inicio]);

    $aulas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($aulas);

} catch (PDOException $e) {
    echo json_encode([
        'status' => 'erro',
        'mensagem' => 'Erro ao listar aulas: ' . $e->getMessage()
    ]);
}
?>
