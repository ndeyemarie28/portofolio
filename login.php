<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

// Si déjà connecté, on va directement au dashboard
if (estConnecte()) {
    header("Location: admin/index.php");
    exit();
}

$erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (tropDeTentatives()) {
        $erreur = "Trop de tentatives. Réessaie dans quelques minutes.";
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $erreur = "Veuillez remplir tous les champs.";
        } else {
            $database = new Database();
            $db = $database->getConnection();

            $stmt = $db->prepare("SELECT id, username, password_hash FROM admin_users WHERE username = :username LIMIT 1");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                reinitialiserTentatives();
                session_regenerate_id(true); // évite la fixation de session
                $_SESSION['admin_id']       = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                header("Location: admin/index.php");
                exit();
            } else {
                enregistrerTentativeEchouee();
                $erreur = "Identifiant ou mot de passe incorrect.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — Administration</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy-950: #050B2E;
            --navy-900: #080E3A;
            --ink: #F2F4FA;
            --muted: #9AA3C7;
            --red: #E4392E;
            --cyan: #33D9E8;
            --font-display: 'Space Grotesk', sans-serif;
            --font-body: 'Inter', sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
        }
        * { box-sizing: border-box; }
        body {
            background-color: var(--navy-950);
            color: var(--ink);
            font-family: var(--font-body);
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .login-card {
            width: 100%;
            max-width: 380px;
            background: linear-gradient(180deg, rgba(51,217,232,0.05), transparent);
            border: 1px solid rgba(51,217,232,0.3);
            border-radius: 10px;
            padding: 2.25rem 2rem;
        }
        .eyebrow {
            font-family: var(--font-mono);
            font-size: 0.75rem;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: var(--cyan);
            margin-bottom: 0.4rem;
        }
        h1 {
            font-family: var(--font-display);
            font-size: 1.5rem;
            margin: 0 0 1.5rem;
        }
        label {
            display: block;
            font-family: var(--font-mono);
            font-size: 0.75rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.4rem;
        }
        input {
            width: 100%;
            background-color: rgba(255,255,255,0.03);
            border: 1px solid rgba(51,217,232,0.25);
            color: var(--ink);
            border-radius: 6px;
            padding: 0.65rem 0.85rem;
            margin-bottom: 1.1rem;
            font-family: var(--font-body);
        }
        input:focus {
            outline: none;
            border-color: var(--cyan);
            box-shadow: 0 0 0 3px rgba(51,217,232,0.15);
        }
        button {
            width: 100%;
            font-family: var(--font-mono);
            font-size: 0.85rem;
            letter-spacing: 0.06em;
            background-color: var(--red);
            border: 1px solid var(--red);
            color: white;
            padding: 0.7rem;
            border-radius: 999px;
            cursor: pointer;
        }
        button:hover { background-color: transparent; color: var(--red); }
        .erreur {
            background: rgba(228,57,46,0.1);
            border: 1px solid var(--red);
            color: #ffb3ae;
            font-size: 0.85rem;
            padding: 0.6rem 0.85rem;
            border-radius: 6px;
            margin-bottom: 1.1rem;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <p class="eyebrow">ACCÈS RÉSERVÉ</p>
        <h1>Connexion administrateur</h1>

        <?php if ($erreur): ?>
            <div class="erreur"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <label for="username">Identifiant</label>
            <input type="text" id="username" name="username" required autofocus>

            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Se connecter</button>
        </form>
    </div>
</body>
</html>
