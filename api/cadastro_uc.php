<?php
include '../conexao/conexao.php'; // ✅ importante incluir a conexão

// 🔹 Busca professores antes de montar o formulário
$sql = "SELECT id, descricao FROM tipos_aula ORDER BY descricao";
$stmt = $conexao->prepare($sql);
$stmt->execute();
$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $curso_id = trim($_POST['curso_id']);
    $uc = $_POST['uc'];

    if (!empty($curso_id) && !empty($uc)) {
        $sql = "INSERT INTO unidades_curriculares (nome, tipo_aula_id)
                VALUES (:uc, :curso_id)";
        $stmt = $conexao->prepare($sql);
        $stmt->execute([
            ':curso_id' => $curso_id,
            ':uc' => $uc
        ]);
        $mensagem = "✅ Unidade Curricular cadastrada com sucesso!";
    } else {
        $mensagem = "⚠️ Preencha todos os campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Cadastrar Unidade Curricular</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background: #f4f7ff;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 50px;
        }

        h1 {
            color: #1a2041;
            margin-bottom: 20px;
        }

        form {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-top: 10px;
        }

        input,
        select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            margin-top: 15px;
            background-color: #c3002f;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        .msg {
            margin-top: 15px;
            font-weight: bold;
            color: green;
        }
                a.voltar {
            margin-top: 20px;
            color: #1a2041;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <h1>📘 Cadastrar Unidade Curricular</h1>

    <form method="POST">
        <label>Curso:</label>
        <select name="curso_id" required>
            <option value="">Selecione...</option>
            <?php foreach ($cursos as $c): ?>
                <option value="<?= $c['id'] ?>"><?= $c['descricao'] ?></option>
            <?php endforeach; ?>
        </select>

        <label>Unidade curricular:</label>
        <input type="text" name="uc" placeholder="Ex: Unidade curricular" required>

        <button type="submit">Cadastrar</button>

        <?php if (!empty($mensagem)): ?>
            <div class="msg"><?= $mensagem ?></div>
        <?php endif; ?>
    </form>
     <a href="../index.php" class="voltar">⬅ Voltar ao calendário</a>
</body>

</html>