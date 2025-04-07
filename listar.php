<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$conn = new SQLite3('personagens.sql');
$result = $conn->query("SELECT * FROM personagens");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Lista de Personagens</title>
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
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        h1 {
            color: #ffd700;
            text-align: center;
            margin: 0;
        }

        .character-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .character-card {
            background-color: rgba(42, 31, 31, 0.95);
            border: 2px solid #8b4513;
            padding: 15px;
            border-radius: 8px;
        }

        .character-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .character-card h3 {
            color: #ffd700;
            margin: 0 0 10px 0;
        }

        button {
            background-color: #8b4513;
            color: #ffd700;
            padding: 8px 16px;
            border: none;
            cursor: pointer;
            font-family: 'MedievalSharp', cursive;
        }

        button:hover {
            background-color: #654321;
        }

        .nav-buttons {
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Lista de Personagens</h1>
        <div class="nav-buttons">
            <a href="index.php"><button>Cadastrar Novo</button></a>
            <form action="login.php" method="POST" style="margin: 0;">
                <button type="submit" name="logout" style="background-color: #800000;">Logout</button>
            </form>
        </div>
    </div>

    <div class="character-grid">
        <?php while ($row = $result->fetchArray(SQLITE3_ASSOC)): ?>
            <div class="character-card">
                <?php if (!empty($row['foto'])): ?>
                    <img src="<?= htmlspecialchars($row['foto']) ?>" alt="Foto de <?= htmlspecialchars($row['nome']) ?>">
                <?php endif; ?>
                <h3><?= htmlspecialchars($row['nome']) ?></h3>
                <p><strong>Espécie:</strong> <?= htmlspecialchars($row['especie']) ?></p>
                <p><strong>Descrição:</strong> <?= htmlspecialchars($row['descricao']) ?></p>
                <a href="editar.php?id=<?= htmlspecialchars($row['id']) ?>">
                    <button>Editar</button>
                </a>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
