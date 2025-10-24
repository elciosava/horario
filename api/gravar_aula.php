<?php
include 'conexao.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Dados inválidos']);
    exit;
}

$professorNome = $data['professor'];
$dia = $data['dia_semana'];
$turno = $data['turno'];
$descricao = $data['descricao'];
$cor = $data['cor'];

// busca o ID do professor
$sql = "SELECT id FROM professores WHERE nome = :nome";
$stmt = $conexao->prepare($sql);
$stmt->bindParam(':nome', $professorNome);
$stmt->execute();
$professor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$professor) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Professor não encontrado']);
    exit;
}

$professorId = $professor['id'];

// verifica se já existe aula no mesmo horário
$sql = "SELECT id FROM aulas WHERE professor_id = :prof AND dia_semana = :dia AND turno = :turno";
$stmt = $conexao->prepare($sql);
$stmt->execute([':prof' => $professorId, ':dia' => $dia, ':turno' => $turno]);
$existe = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existe) {
    // atualizar
    $sql = "UPDATE aulas SET descricao = :descricao, cor = :cor 
            WHERE id = :id";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([
        ':descricao' => $descricao,
        ':cor' => $cor,
        ':id' => $existe['id']
    ]);
} else {
    // inserir
    $sql = "INSERT INTO aulas (professor_id, dia_semana, turno, descricao, cor)
            VALUES (:prof, :dia, :turno, :descricao, :cor)";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([
        ':prof' => $professorId,
        ':dia' => $dia,
        ':turno' => $turno,
        ':descricao' => $descricao,
        ':cor' => $cor
    ]);
}

echo json_encode(['status' => 'ok']);
?>
