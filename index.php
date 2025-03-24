<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

$conn = new SQLite3('personagens.sql');

if (isset($_POST['create'])) {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $sexo = $_POST['sexo'];
    $especie = $_POST['especie'];
    $biografia = $_POST['biografia'];
    $poderes = $_POST['poderes'];

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

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $sexo = $_POST['sexo'];
    $especie = $_POST['especie'];
    $biografia = $_POST['biografia'];
    $poderes = $_POST['poderes'];

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

if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $stmt = $conn->prepare("DELETE FROM personagens WHERE id = :id");
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $stmt->execute();
}

$result = $conn->query("SELECT * FROM personagens");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Personagens</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=MedievalSharp&display=swap');
        
        body {
            font-family: 'MedievalSharp', cursive;
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px;
            background-color: #1a0f0f;
            color: #d4c4a1;
            line-height: 1.6;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%238b4513' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E"),
                linear-gradient(45deg, #1a0f0f 25%, #2a1f1f 25%, #2a1f1f 50%, #1a0f0f 50%, #1a0f0f 75%, #2a1f1f 75%, #2a1f1f);
            background-size: 60px 60px, 60px 60px;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Cpath fill='%23ffd700' fill-opacity='0.05' d='M15 15h70l-35 70z'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: -1;
        }

        h1 {
            color: #ffd700;
            text-align: center;
            margin-bottom: 40px;
            font-size: 3em;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            letter-spacing: 2px;
        }

        .form-container {
            background-color: rgba(42, 31, 31, 0.95);
            padding: 30px;
            margin-bottom: 40px;
            border: 4px solid #8b4513;
            position: relative;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='50' height='50' viewBox='0 0 50 50'%3E%3Cpath fill='%238b4513' fill-opacity='0.1' d='M25 0L0 25l25 25 25-25z'/%3E%3C/svg%3E");
        }

        .form-container::before,
        .form-container::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 2px solid #ffd700;
        }

        .form-container::before {
            top: -2px;
            left: -2px;
            border-right: none;
            border-bottom: none;
        }

        .form-container::after {
            bottom: -2px;
            right: -2px;
            border-left: none;
            border-top: none;
        }

        input[type="text"], textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #333;
            border-radius: 6px;
            box-sizing: border-box;
            background-color: #2a2a2a;
            color: #fff;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus, textarea:focus {
            border-color: #1976d2;
            outline: none;
            box-shadow: 0 0 0 2px rgba(25, 118, 210, 0.2);
        }

        textarea {
            height: 120px;
            resize: vertical;
        }

        button {
            background-color: #8b4513;
            color: #ffd700;
            padding: 12px 24px;
            border: 2px solid #654321;
            border-radius: 0;
            cursor: pointer;
            margin-right: 12px;
            font-weight: bold;
            font-size: 16px;
            font-family: 'MedievalSharp', cursive;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 2px;
            position: relative;
        }

        button[name="delete"] {
            background-color: #800000;
            border-color: #600000;
        }

        button:hover {
            transform: translateY(-2px);
            background-color: #654321;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background-color: #2a1f1f;
            box-shadow: 0 8px 32px rgba(0,0,0,0.7);
            border: 2px solid #8b4513;
            margin-top: 20px;
            position: relative;
        }

        th, td {
            padding: 18px;
            text-align: left;
            border-bottom: 1px solid #333;
            color: #fff;
            font-size: 15px;
            letter-spacing: 0.3px;
        }

        th {
            background-color: #8b4513;
            color: #ffd700;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 16px;
            border-bottom: 2px solid #654321;
        }

        tr:hover {
            background-color: #3a2f2f;
            transition: background-color 0.3s ease;
        }

        img {
            max-width: 120px;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            transition: transform 0.3s ease;
        }

        img:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body>
    <h1>Cadastro de Personagens</h1>

    <div class="form-container">
        <h2>Novo Personagem</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="nome" placeholder="Nome" required>
            <textarea name="descricao" placeholder="Descrição" required></textarea>
            <input type="text" name="sexo" placeholder="Sexo" required>
            <input type="text" name="especie" placeholder="Espécie" required>
            <textarea name="biografia" placeholder="Biografia"></textarea>
            <input type="file" name="foto">
            <textarea name="poderes" placeholder="Poderes/Habilidades"></textarea>
            <button type="submit" name="create">Cadastrar</button>
        </form>
    </div>

    <table>
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
                        <img src="<?= htmlspecialchars($row['foto']) ?>" alt="Foto">
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['poderes']) ?></td>
                <td>
                    <form method="POST" style="display:inline-block;" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
                        <!-- Mantendo os valores originais em campos ocultos -->
                        <input type="hidden" name="nome" value="<?= htmlspecialchars($row['nome']) ?>">
                        <input type="hidden" name="descricao" value="<?= htmlspecialchars($row['descricao']) ?>">
                        <input type="hidden" name="sexo" value="<?= htmlspecialchars($row['sexo']) ?>">
                        <input type="hidden" name="especie" value="<?= htmlspecialchars($row['especie']) ?>">
                        <input type="hidden" name="biografia" value="<?= htmlspecialchars($row['biografia']) ?>">
                        <input type="hidden" name="poderes" value="<?= htmlspecialchars($row['poderes']) ?>">
                        <input type="hidden" name="foto_atual" value="<?= htmlspecialchars($row['foto']) ?>">
                        <input type="file" name="foto" style="max-width: 200px;">
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