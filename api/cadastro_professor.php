<?php
include '../conexao/conexao.php'; // ✅ Conexão com o banco

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);

    if (!empty($nome)) {
        $sql = "INSERT INTO professores (nome)
                VALUES (:nome)";
        $stmt = $conexao->prepare($sql);
        $stmt->execute([
            ':nome' => $nome
        ]);
        $mensagem = "✅ Professor cadastrado com sucesso!";
    } else {
        $mensagem = "⚠️ O campo nome é obrigatório.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Cadastrar Professor - SENAI</title>
    <link rel="stylesheet" href="../css/style.css">
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

        input {
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
    <h1>👨‍🏫 Cadastro de Professores</h1>

    <form method="POST">
        <label>Nome do Professor:</label>
        <input type="text" name="nome" placeholder="Ex: João da Silva" required>

        <button type="submit">Cadastrar</button>

        <?php if (!empty($mensagem)): ?>
            <div class="msg"><?= $mensagem ?></div>
        <?php endif; ?>
    </form>

    <a href="../index.php" class="voltar">⬅ Voltar ao calendário</a>
</body>
</html>
