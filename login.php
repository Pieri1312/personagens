
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new SQLite3('personagens.sql');

$conn->exec("CREATE TABLE IF NOT EXISTS usuarios (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    usuario TEXT NOT NULL UNIQUE,
    senha TEXT NOT NULL
)");

if (isset($_POST['cadastrar'])) {
    $usuario = $_POST['usuario'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    
    try {
        $stmt = $conn->prepare("INSERT INTO usuarios (usuario, senha) VALUES (:usuario, :senha)");
        $stmt->bindValue(':usuario', $usuario, SQLITE3_TEXT);
        $stmt->bindValue(':senha', $senha, SQLITE3_TEXT);
        $stmt->execute();
        $mensagem = "Cadastro realizado com sucesso!";
    } catch (Exception $e) {
        $mensagem = "Erro: Usuário já existe";
    }
}

if (isset($_POST['login'])) {
    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];
    
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE usuario = :usuario");
    $stmt->bindValue(':usuario', $usuario, SQLITE3_TEXT);
    $result = $stmt->execute();
    
    if ($user = $result->fetchArray()) {
        if (password_verify($senha, $user['senha'])) {
            session_start();
            $_SESSION['usuario'] = $usuario;
            header("Location: index.php");
            exit();
        }
    }
    $mensagem = "Usuário ou senha incorretos";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login - BANK OF LORE</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=MedievalSharp&display=swap');
        
        body {
            font-family: 'MedievalSharp', cursive;
            background-color: #1a0f0f;
            color: #d4c4a1;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Cpath fill='%23ffd700' fill-opacity='0.1' d='M50 0C22.4 0 0 22.4 0 50s22.4 50 50 50 50-22.4 50-50S77.6 0 50 0zm0 90c-22.1 0-40-17.9-40-40s17.9-40 40-40 40 17.9 40 40-17.9 40-40 40z'/%3E%3C/svg%3E");
        }

        .container {
            background-color: rgba(42, 31, 31, 0.95);
            padding: 40px;
            border: none;
            max-width: 500px;
            width: 100%;
            position: relative;
            background: url("attached_assets/download (1).png") no-repeat center;
            background-size: 100% 100%;
            min-height: 600px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .container::before,
        .container::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 2px solid #ffd700;
        }

        .container::before {
            top: -2px;
            left: -2px;
            border-right: none;
            border-bottom: none;
        }

        .container::after {
            bottom: -2px;
            right: -2px;
            border-left: none;
            border-top: none;
        }

        h1 {
            color: #ffd700;
            text-align: center;
            margin-bottom: 30px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }

        .form-group {
            margin-bottom: 20px;
        }

        input {
            width: 90%;
            padding: 10px;
            margin: 5px auto;
            background-color: rgba(42, 31, 31, 0.8);
            border: none;
            color: #ffd700;
            font-family: 'MedievalSharp', cursive;
            font-size: 18px;
            text-align: center;
            display: block;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #8b4513;
            color: #ffd700;
            border: none;
            cursor: pointer;
            font-family: 'MedievalSharp', cursive;
            font-size: 16px;
            margin-top: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        button:hover {
            background-color: #654321;
        }

        .message {
            text-align: center;
            color: #ffd700;
            margin-top: 10px;
        }

        .tabs {
            display: flex;
            margin-bottom: 20px;
        }

        .tab {
            flex: 1;
            text-align: center;
            padding: 10px;
            cursor: pointer;
            color: #8b4513;
        }

        .tab.active {
            color: #ffd700;
            border-bottom: 2px solid #ffd700;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>RPG Characters</h1>
        
        <div class="tabs">
            <div class="tab active" onclick="showForm('login')">Login</div>
            <div class="tab" onclick="showForm('cadastro')">Cadastro</div>
        </div>

        <form id="loginForm" method="POST" style="display: block;">
            <div class="form-group">
                <input type="text" name="usuario" placeholder="Usuário" required>
            </div>
            <div class="form-group">
                <input type="password" name="senha" placeholder="Senha" required>
            </div>
            <button type="submit" name="login">Entrar</button>
        </form>

        <form id="cadastroForm" method="POST" style="display: none;">
            <div class="form-group">
                <input type="text" name="usuario" placeholder="Usuário" required>
            </div>
            <div class="form-group">
                <input type="password" name="senha" placeholder="Senha" required>
            </div>
            <button type="submit" name="cadastrar">Cadastrar</button>
        </form>

        <?php if (isset($mensagem)): ?>
            <div class="message"><?= htmlspecialchars($mensagem) ?></div>
        <?php endif; ?>
    </div>

    <script>
        function showForm(formType) {
            document.getElementById('loginForm').style.display = formType === 'login' ? 'block' : 'none';
            document.getElementById('cadastroForm').style.display = formType === 'cadastro' ? 'block' : 'none';
            
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => tab.classList.remove('active'));
            event.target.classList.add('active');
        }
    </script>
</body>
</html>
