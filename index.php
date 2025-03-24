<?php
// Ativar exibição de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar se o arquivo SQLite é válido ou recriar
if (!file_exists('personagens.sql') || !is_readable('personagens.sql')) {
    $db = new SQLite3('personagens.sql');
    $db->exec("CREATE TABLE IF NOT EXISTS personagens (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nome TEXT NOT NULL,
        descricao TEXT NOT NULL,
        sexo TEXT NOT NULL,
        especie TEXT NOT NULL,
        biografia TEXT,
        foto TEXT,
        poderes TEXT
    )");
    $db->close();
}

// Conexão com o banco de dados SQLite
$conn = new SQLite3('personagens.sql');

// Criar registro
if (isset($_POST['create'])) {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $sexo = $_POST['sexo'];
    $especie = $_POST['especie'];
    $biografia = $_POST['biografia'];
    $poderes = $_POST['poderes'];

    // Upload de foto
    $foto = '';
    if (!empty($_FILES['foto']['name'])) {
        $foto = 'uploads/' . basename($_FILES['foto']['name']);
        move_uploaded_file($_FILES['foto']['tmp_name'], $foto);
    }

    $stmt = $conn->prepare("INSERT INTO personagens (nome, descricao, sexo, especie, biografia, foto, poderes) VALUES (:nome, :descricao, :sexo, :especie, :biografia, :foto, :poderes)");
    $stmt->bindValue(':nome', $nome, SQLITE3_TEXT);
    $stmt->bindValue(':descricao', $descricao, SQLITE3_TEXT);
    $stmt->bindValue(':sexo', $sexo, SQLITE3_TEXT);
    $stmt->bindValue(':especie', $especie, SQLITE3_TEXT);
    $stmt->bindValue(':biografia', $biografia, SQLITE3_TEXT);
    $stmt->bindValue(':foto', $foto, SQLITE3_TEXT);
    $stmt->bindValue(':poderes', $poderes, SQLITE3_TEXT);
    $stmt->execute();
}

// Atualizar registro
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $sexo = $_POST['sexo'];
    $especie = $_POST['especie'];
    $biografia = $_POST['biografia'];
    $poderes = $_POST['poderes'];

    // Upload de foto
    $foto = $_POST['foto_atual'];
    if (!empty($_FILES['foto']['name'])) {
        $foto = 'uploads/' . basename($_FILES['foto']['name']);
        move_uploaded_file($_FILES['foto']['tmp_name'], $foto);
    }

    $stmt = $conn->prepare("UPDATE personagens SET nome = :nome, descricao = :descricao, sexo = :sexo, especie = :especie, biografia = :biografia, foto = :foto, poderes = :poderes WHERE id = :id");
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $stmt->bindValue(':nome', $nome, SQLITE3_TEXT);
    $stmt->bindValue(':descricao', $descricao, SQLITE3_TEXT);
    $stmt->bindValue(':sexo', $sexo, SQLITE3_TEXT);
    $stmt->bindValue(':especie', $especie, SQLITE3_TEXT);
    $stmt->bindValue(':biografia', $biografia, SQLITE3_TEXT);
    $stmt->bindValue(':foto', $foto, SQLITE3_TEXT);
    $stmt->bindValue(':poderes', $poderes, SQLITE3_TEXT);
    $stmt->execute();
}

// Excluir registro
if (isset($_POST['delete'])) {
    $id = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM personagens WHERE id = :id");
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $stmt->execute();
}

// Ler registros
$result = $conn->query("SELECT * FROM personagens");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Personagens</title>
</head>
<body>
    <h1>Cadastrar Personagem</h1>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="nome" placeholder="Nome" required><br><br>
        <textarea name="descricao" placeholder="Descrição" required></textarea><br><br>
        <input type="text" name="sexo" placeholder="Sexo" required><br><br>
        <input type="text" name="especie" placeholder="Espécie" required><br><br>
        <textarea name="biografia" placeholder="Biografia"></textarea><br><br>
        <input type="file" name="foto"><br><br>
        <textarea name="poderes" placeholder="Poderes/Habilidades"></textarea><br><br>
        <button type="submit" name="create">Cadastrar</button>
    </form>

    <h1>Lista de Personagens</h1>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Descrição</th>
            <th>Sexo</th>
            <th>Espécie</th>
            <th>Biografia</th>
            <th>Foto</th>
            <th>Poderes</th>
            <th>Ações</th>
        </tr>
        <?php while ($row = $result->fetchArray(SQLITE3_ASSOC)): ?>
            <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['nome']) ?></td>
                <td><?= htmlspecialchars($row['descricao']) ?></td>
                <td><?= htmlspecialchars($row['sexo']) ?></td>
                <td><?= htmlspecialchars($row['especie']) ?></td>
                <td><?= htmlspecialchars($row['biografia']) ?></td>
                <td>
                    <?php if (!empty($row['foto'])): ?>
                        <img src="<?= htmlspecialchars($row['foto']) ?>" alt="Foto" width="100">
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['poderes']) ?></td>
                <td>
                    <form method="POST" style="display:inline-block;" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
                        <input type="text" name="nome" value="<?= htmlspecialchars($row['nome']) ?>">
                        <input type="text" name="descricao" value="<?= htmlspecialchars($row['descricao']) ?>">
                        <input type="text" name="sexo" value="<?= htmlspecialchars($row['sexo']) ?>">
                        <input type="text" name="especie" value="<?= htmlspecialchars($row['especie']) ?>">
                        <textarea name="biografia"><?= htmlspecialchars($row['biografia']) ?></textarea>
                        <input type="file" name="foto">
                        <input type="hidden" name="foto_atual" value="<?= htmlspecialchars($row['foto']) ?>">
                        <textarea name="poderes"><?= htmlspecialchars($row['poderes']) ?></textarea>
                        <button type="submit" name="update">Atualizar</button>
                    </form>
                    <form method="POST" style="display:inline-block;">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
                        <button type="submit" name="delete">Deletar</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
