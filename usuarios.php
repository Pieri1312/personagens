<?php
session_start();


if (!isset($_SESSION['usuario']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit();
}

$conn = new SQLite3('personagens.sql');


if (isset($_POST['delete']) && isset($_POST['id'])) {
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = :id AND usuario NOT IN ('admin1', 'admin2')");
    $stmt->bindValue(':id', $_POST['id'], SQLITE3_INTEGER);
    $stmt->execute();
}


if (isset($_POST['update']) && isset($_POST['id'])) {
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;
    $stmt = $conn->prepare("UPDATE usuarios SET is_admin = :is_admin WHERE id = :id AND usuario NOT IN ('admin1', 'admin2')");
    $stmt->bindValue(':id', $_POST['id'], SQLITE3_INTEGER);
    $stmt->bindValue(':is_admin', $is_admin, SQLITE3_INTEGER);
    $stmt->execute();
}

$result = $conn->query("SELECT * FROM usuarios");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Lista de Usuários - Admin</title>
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

        .user-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: rgba(42, 31, 31, 0.95);
            border: 2px solid #8b4513;
        }

        .user-table th, .user-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #8b4513;
        }

        .user-table th {
            background-color: #8b4513;
            color: #ffd700;
        }

        .user-table tr:hover {
            background-color: rgba(139, 69, 19, 0.2);
        }

        button {
            background-color: #8b4513;
            color: #ffd700;
            padding: 8px 16px;
            border: none;
            cursor: pointer;
            font-family: 'MedievalSharp', cursive;
            margin: 0 5px;
        }

        button:hover {
            background-color: #654321;
        }

        button[name="delete"] {
            background-color: #800000;
        }

        button[name="delete"]:hover {
            background-color: #600000;
        }

        .nav-buttons {
            display: flex;
            gap: 10px;
        }

        .admin-badge {
            background-color: #ffd700;
            color: #1a0f0f;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.8em;
        }

        .actions {
            display: flex;
            gap: 5px;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        input[type="checkbox"] {
            width: 20px;
            height: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Lista de Usuários</h1>
        <div class="nav-buttons">
            <a href="index.php"><button type="button">Voltar ao Início</button></a>
            <form action="login.php" method="POST" style="margin: 0;">
                <button type="submit" name="logout" style="background-color: #800000;">Logout</button>
            </form>
        </div>
    </div>

    <table class="user-table">
        <tr>
            <th>ID</th>
            <th>Usuário</th>
            <th>Tipo</th>
            <th>Ações</th>
        </tr>
        <?php while ($row = $result->fetchArray(SQLITE3_ASSOC)): ?>
            <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['usuario']) ?></td>
                <td>
                    <?php if ($row['is_admin'] == 1): ?>
                        <span class="admin-badge">Admin</span>
                    <?php else: ?>
                        Usuário
                    <?php endif; ?>
                </td>
                <td class="actions">
                    <?php if ($row['usuario'] != 'admin1' && $row['usuario'] != 'admin2'): ?>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
                            <div class="checkbox-wrapper">
                                <input type="checkbox" name="is_admin" <?= $row['is_admin'] ? 'checked' : '' ?>>
                                <label>Admin</label>
                                <button type="submit" name="update">Salvar</button>
                            </div>
                        </form>
                        <form method="POST" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
                            <button type="submit" name="delete">Excluir</button>
                        </form>
                    <?php else: ?>
                        <em>Admin principal</em>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
