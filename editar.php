
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new SQLite3('personagens.sql');

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
    
    header("Location: index.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM personagens WHERE id = :id");
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $personagem = $result->fetchArray(SQLITE3_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Personagem</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=MedievalSharp&display=swap');
        
        body {
            font-family: 'MedievalSharp', cursive;
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
            background-color: #1a0f0f;
            color: #d4c4a1;
            line-height: 1.6;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%238b4513' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .form-container {
            background-color: rgba(42, 31, 31, 0.95);
            padding: 30px;
            border: 4px solid #8b4513;
            margin-top: 20px;
        }

        h1 {
            color: #ffd700;
            text-align: center;
            margin-bottom: 40px;
        }

        input[type="text"], textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #8b4513;
            background-color: #2a1f1f;
            color: #d4c4a1;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #ffd700;
        }

        .current-image {
            max-width: 200px;
            margin: 10px 0;
        }

        button {
            background-color: #8b4513;
            color: #ffd700;
            padding: 12px 24px;
            border: none;
            cursor: pointer;
            margin-top: 20px;
        }

        button:hover {
            background-color: #654321;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #ffd700;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Editar Personagem</h1>
    
    <div class="form-container">
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= htmlspecialchars($personagem['id']) ?>">
            
            <label>Nome:</label>
            <input type="text" name="nome" value="<?= htmlspecialchars($personagem['nome']) ?>" required>
            
            <label>Descrição:</label>
            <textarea name="descricao" required><?= htmlspecialchars($personagem['descricao']) ?></textarea>
            
            <label>Sexo:</label>
            <input type="text" name="sexo" value="<?= htmlspecialchars($personagem['sexo']) ?>" required>
            
            <label>Espécie:</label>
            <input type="text" name="especie" value="<?= htmlspecialchars($personagem['especie']) ?>" required>
            
            <label>Biografia:</label>
            <textarea name="biografia"><?= htmlspecialchars($personagem['biografia']) ?></textarea>
            
            <label>Foto atual:</label>
            <?php if (!empty($personagem['foto'])): ?>
                <img src="<?= htmlspecialchars($personagem['foto']) ?>" alt="Foto atual" class="current-image">
            <?php endif; ?>
            <input type="hidden" name="foto_atual" value="<?= htmlspecialchars($personagem['foto']) ?>">
            <input type="file" name="foto">
            
            <label>Poderes/Habilidades:</label>
            <textarea name="poderes"><?= htmlspecialchars($personagem['poderes']) ?></textarea>
            
            <button type="submit" name="update">Salvar Alterações</button>
        </form>
        
        <a href="index.php" class="back-link">← Voltar para a lista</a>
    </div>
</body>
</html>
