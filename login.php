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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BANK OF LORE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=MedievalSharp&display=swap');
        
        :root {
            --gold: #ffd700;
            --dark-brown: #2a1f1f;
            --light-brown: #8b4513;
            --darker-brown: #654321;
            --parchment: #d4c4a1;
            --background: #1a0f0f;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'MedievalSharp', cursive;
            background-color: var(--background);
            color: var(--parchment);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Cpath fill='%23ffd700' fill-opacity='0.1' d='M50 0C22.4 0 0 22.4 0 50s22.4 50 50 50 50-22.4 50-50S77.6 0 50 0zm0 90c-22.1 0-40-17.9-40-40s17.9-40 40-40 40 17.9 40 40-17.9 40-40 40z'/%3E%3C/svg%3E");
            background-attachment: fixed;
        }

        .container {
            background-color: rgba(42, 31, 31, 0.95);
            padding: 40px;
            border: 2px solid rgba(255, 215, 0, 0.3);
            border-radius: 8px;
            max-width: 500px;
            width: 90%;
            position: relative;
            min-height: 600px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5), 0 0 30px rgba(255, 215, 0, 0.2);
            backdrop-filter: blur(5px);
        }

        .container::before,
        .container::after,
        .corner-top-right,
        .corner-bottom-left {
            content: '';
            position: absolute;
            width: 30px;
            height: 30px;
            border: 2px solid var(--gold);
            z-index: 1;
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

        .corner-top-right {
            top: -2px;
            right: -2px;
            border-left: none;
            border-bottom: none;
        }

        .corner-bottom-left {
            bottom: -2px;
            left: -2px;
            border-right: none;
            border-top: none;
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
            
        }

        .logo i {
            font-size: 48px;
            color: var(--gold);
            text-shadow: 0 0 10px rgba(255, 215, 0, 0.5);
        }

        h1 {
            color: var(--gold);
            text-align: center;
            margin-bottom: 30px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            font-size: 2.2rem;
            letter-spacing: 1px;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group i {
            position: absolute;
            left: 7%;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gold);
            font-size: 18px;
        }

        input {
            width: 90%;
            padding: 12px 12px 12px 40px;
            margin: 10px auto;
            background-color: rgba(42, 31, 31, 0.8);
            border: 1px solid rgba(255, 215, 0, 0.3);
            border-radius: 4px;
            color: var(--gold);
            font-family: 'MedievalSharp', cursive;
            font-size: 18px;
            text-align: left;
            display: block;
            transition: all 0.3s ease;
        }

        input:focus {
            outline: none;
            border-color: var(--gold);
            box-shadow: 0 0 10px rgba(255, 215, 0, 0.3);
        }

        input::placeholder {
            color: rgba(255, 215, 0, 0.5);
        }

        button {
            width: 90%;
            margin: 0 auto;
            padding: 14px;
            background-color: var(--light-brown);
            color: var(--gold);
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-family: 'MedievalSharp', cursive;
            font-size: 18px;
            margin-top: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
            transition: all 0.3s ease;
            display: block;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 215, 0, 0.2), transparent);
            transition: all 0.6s ease;
            z-index: -1;
        }

        button:hover::before {
            left: 100%;
        }

        button:hover {
            background-color: var(--darker-brown);
            box-shadow: 0 0 15px rgba(255, 215, 0, 0.4);
            transform: translateY(-2px);
        }

        .message {
            text-align: center;
            color: var(--gold);
            margin-top: 20px;
            padding: 10px;
            border-radius: 4px;
            background-color: rgba(0, 0, 0, 0.2);
            font-size: 16px;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .tabs {
            display: flex;
            margin-bottom: 30px;
            border-bottom: 1px solid rgba(255, 215, 0, 0.3);
        }

        .tab {
            flex: 1;
            text-align: center;
            padding: 15px 10px;
            cursor: pointer;
            color: var(--light-brown);
            transition: all 0.3s ease;
            font-size: 18px;
            position: relative;
        }

        .tab:hover {
            color: rgba(255, 215, 0, 0.8);
        }

        .tab.active {
            color: var(--gold);
        }

        .tab.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: var(--gold);
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from { transform: scaleX(0); }
            to { transform: scaleX(1); }
        }

        .form-wrapper {
            position: relative;
            min-height: 200px;
        }

        #loginForm, #cadastroForm {
            transition: all 0.5s ease;
            position: absolute;
            width: 100%;
            opacity: 0;
            transform: translateX(-20px);
            pointer-events: none;
        }

        #loginForm.active, #cadastroForm.active {
            opacity: 1;
            transform: translateX(0);
            pointer-events: all;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: rgba(212, 196, 161, 0.7);
        }

        @media (max-width: 500px) {
            .container {
                padding: 30px 15px;
                min-height: 500px;
            }
            
            h1 {
                font-size: 1.8rem;
            }
            
            input, button {
                width: 95%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="corner-top-right"></div>
        <div class="corner-bottom-left"></div>
        
        <div class="logo">
            <i class="fas fa-dragon"></i>
        </div>
        
        <h1>RPG Characters</h1>
        
        <div class="tabs">
            <div class="tab active" onclick="showForm('login')">Login</div>
            <div class="tab" onclick="showForm('cadastro')">Cadastro</div>
        </div>

        <div class="form-wrapper">
            <form id="loginForm" method="POST" class="active">
                <div class="form-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="usuario" placeholder="Usuário" required>
                </div>
                <div class="form-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="senha" placeholder="Senha" required>
                </div>
                <button type="submit" name="login">
                    Entrar
                </button>
            </form>

            <form id="cadastroForm" method="POST">
                <div class="form-group">
                    <i class="fas fa-user-plus"></i>
                    <input type="text" name="usuario" placeholder="Usuário" required>
                </div>
                <div class="form-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="senha" placeholder="Senha" required>
                </div>
                <button type="submit" name="cadastrar">
                    Cadastrar
                </button>
            </form>
        </div>

        <?php if (isset($mensagem)): ?>
            <div class="message"><?= htmlspecialchars($mensagem) ?></div>
        <?php endif; ?>
        
        <div class="footer">
            Bem-vindo ao mundo de aventuras
        </div>
    </div>

    <script>
        function showForm(formType) {
            // Remove active class from all forms
            document.getElementById('loginForm').classList.remove('active');
            document.getElementById('cadastroForm').classList.remove('active');
            
            // Add active class to selected form
            document.getElementById(formType + 'Form').classList.add('active');
            
            // Update tabs
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => tab.classList.remove('active'));
            event.target.classList.add('active');
        }
    </script>
</body>
</html>