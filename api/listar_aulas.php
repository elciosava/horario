<?php
include '../conexao/conexao.php';

$semana = $_GET['semana_inicio'] ?? date('Y-m-d', strtotime('monday this week'));

try {
    $sql = "SELECT
                a.id,
                a.dia_semana,
                a.turno,
                a.semana_inicio,
                a.cor,
                t.id            AS tipo_aula_id,
                t.sigla,
                t.descricao,
                t.professor_id,
                p.nome          AS professor,
                u.id            AS uc_id,
                u.nome          AS uc_nome
            FROM aulas_agendadas a
            LEFT JOIN tipos_aula t           ON t.id = a.tipo_aula_id
            LEFT JOIN professores p          ON p.id = t.professor_id
            LEFT JOIN unidades_curriculares u ON u.id = a.uc_id
            WHERE a.semana_inicio = :semana
            ORDER BY p.nome,
                     FIELD(a.turno,'Manhã','Tarde','Noite'),
                     FIELD(a.dia_semana,'Seg','Ter','Qua','Qui','Sex','Sab','Dom')";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([':semana' => $semana]);

    $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($dados);
} catch (PDOException $e) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro ao listar aulas: '.$e->getMessage()]);
}
