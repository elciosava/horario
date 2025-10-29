<?php
include '../conexao/conexao.php'; // ✅ importante incluir a conexão

// 🔹 Busca professores antes de montar o formulário
$sql = "SELECT id, nome FROM professores ORDER BY nome";
$stmt = $conexao->prepare($sql);
$stmt->execute();
$professores = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sigla = strtoupper(trim($_POST['sigla']));
    $descricao = trim($_POST['descricao']);
    $professor_id = $_POST['professor_id'];
    $cor = $_POST['cor'] ?? '#1a2041';

    if (!empty($sigla) && !empty($descricao) && !empty($professor_id)) {
        $sql = "INSERT INTO tipos_aula (sigla, descricao, professor_id, cor)
                VALUES (:sigla, :descricao, :professor_id, :cor)";
        $stmt = $conexao->prepare($sql);
        $stmt->execute([
            ':sigla' => $sigla,
            ':descricao' => $descricao,
            ':professor_id' => $professor_id,
            ':cor' => $cor
        ]);
        $mensagem = "✅ Aula cadastrada com sucesso!";
    } else {
        $mensagem = "⚠️ Preencha todos os campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Cadastrar Tipos de Aula - SENAI</title>
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
    <h1>📘 Cadastro de Tipos de Aula</h1>

    <form method="POST">
        <label>Professor:</label>
        <select name="professor_id" required>
            <option value="">Selecione...</option>
            <?php foreach ($professores as $p): ?>
                <option value="<?= $p['id'] ?>"><?= $p['nome'] ?></option>
            <?php endforeach; ?>
        </select>

        <label>Sigla (3 letras):</label>
        <input type="text" name="sigla" maxlength="3" placeholder="Ex: ADM, ELE" required>

        <label>Descrição da Aula:</label>
        <input type="text" name="descricao" placeholder="Ex: Administração, Eletricista Predial" required>

        <label>Cor da Aula:</label>
        <input type="color" name="cor" value="#1a2041">

        <button type="submit">Cadastrar</button>

        <?php if (!empty($mensagem)): ?>
            <div class="msg"><?= $mensagem ?></div>
        <?php endif; ?>
    </form>
     <a href="../index.php" class="voltar">⬅ Voltar ao calendário</a>
</body>

</html>