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
            background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAABhWlDQ1BJQ0MgcHJvZmlsZQAAKJF9kT1Iw1AUhU9TpUUqDnYQcchQnSyIijhKFYtgobQVWnUweekfNGlIUlwcBdeCgz+LVQcXZ10dXAVB8AfE0clJ0UVKvC8ptIjxwuN9nHfP4b37AKFWYprVNgZoum2mEnExk10RQ68IIYABhAXFzDIzkYY0PF/X8PH1LsqzvM/9OXqVnMkAn0g8y3TDIt4gnt60dM77xGFWkhTic+Jxgy5I/Mh12eU3zkWHBZ4ZNjKpeeIwsVhoY7mNWdFQiaeJo4qqUb6QcVnhvMVZLVdZ8578haGctrLMdZpDSGARS5AgQkENmyjDRpxWnRQLKdqPe/gHHb9ELoVcG2DkmEcFGmTXD/4Hv7u18hPjXlI4DrS/OM7HMBDaBepVx/k+dpz6CRB8Bq70pr9cA6Y/Sa82tegR0LsNXFw3NWUPuNwBBp4M2ZRdKUhLyOeB9zP6pizQfwt0r3q9NfZx+gCkqavkDXBwCIwUKHvN592Rnb39e6bR3w8d/3KFn6HzAgAAAAZiS0dEAAAAAAAA+UO7fwAAAAlwSFlzAAAuIwAALiMBeKU/dgAAAAd0SU1FB+UDGBYuLGHFlJ0AAAAZdEVYdENvbW1lbnQAQ3JlYXRlZCB3aXRoIEdJTVBXgQ4XAAAAsUlEQVRo3u3ZQQqDMBCF4T/qKbx4r573UldCEYrUkGQm5j3I1s/MvEVwzrIs2znGWFprD+89pZQXkW+2bXtYlJnJXJWZKedMKSUiIr/0CxG5df+DiP7mnPO0zhN7RLsI3TVZzYh2EWYeWVbXtfaNzHEcl/6R++IRZVDhxhX+SYU/hvxDhX/+ZNcK3RvRLkL3RpiPKIOK8PuIMqgoqChvRBlUFFQUVJT3owwqCir834U+kyLxP6tL/pUAAAAASUVORK5CYII=');
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
            background-color: #2a1f1f;
            padding: 30px;
            border-radius: 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.7);
            margin-bottom: 40px;
            border: 2px solid #8b4513;
            position: relative;
        }

        .form-container::before {
            content: '';
            position: absolute;
            top: 5px;
            left: 5px;
            right: 5px;
            bottom: 5px;
            border: 1px solid #8b4513;
            pointer-events: none;
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