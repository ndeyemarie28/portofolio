<?php
require_once __DIR__ . '/../includes/auth.php';
protegerPage(); // bloque l'accès si non connecté

require_once __DIR__ . '/../config/database.php';

$database = new Database();
$db = $database->getConnection();

$stmt = $db->query("SELECT id, nom, email, sujet, message, lu, created_at FROM messages ORDER BY created_at DESC");
$messages = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration — Messages</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy-950: #050B2E; --navy-900: #080E3A;
            --ink: #F2F4FA; --muted: #9AA3C7;
            --red: #E4392E; --cyan: #33D9E8;
            --font-display: 'Space Grotesk', sans-serif;
            --font-body: 'Inter', sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
        }
        * { box-sizing: border-box; }
        body { background: var(--navy-950); color: var(--ink); font-family: var(--font-body); margin: 0; }
        .topbar {
            display: flex; justify-content: space-between; align-items: center;
            padding: 1.25rem 2rem; border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        .topbar h1 { font-family: var(--font-display); font-size: 1.2rem; margin: 0; }
        .topbar a {
            font-family: var(--font-mono); font-size: 0.8rem; color: var(--muted);
            text-decoration: none; border: 1px solid rgba(255,255,255,0.15);
            padding: 0.4rem 0.9rem; border-radius: 999px;
        }
        .topbar a:hover { border-color: var(--red); color: var(--red); }
        .container { max-width: 900px; margin: 0 auto; padding: 2rem; }
        .msg-card {
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(51,217,232,0.2);
            border-radius: 8px; padding: 1.25rem 1.5rem; margin-bottom: 1rem;
        }
        .msg-head { display: flex; justify-content: space-between; font-family: var(--font-mono); font-size: 0.78rem; color: var(--cyan); }
        .msg-card h3 { margin: 0.5rem 0 0.2rem; font-family: var(--font-display); font-size: 1.05rem; }
        .msg-card .meta { color: var(--muted); font-size: 0.85rem; margin-bottom: 0.6rem; }
        .msg-card p { margin: 0; line-height: 1.6; }
        .empty { color: var(--muted); text-align: center; padding: 3rem 0; font-family: var(--font-mono); }
    </style>
</head>
<body>
    <div class="topbar">
        <h1>Bonjour, <?= htmlspecialchars($_SESSION['admin_username']) ?></h1>
        <a href="../logout.php">Déconnexion</a>
    </div>

    <div class="container">
        <?php if (empty($messages)): ?>
            <p class="empty">Aucun message pour le moment.</p>
        <?php else: ?>
            <?php foreach ($messages as $m): ?>
                <div class="msg-card">
                    <div class="msg-head">
                        <span>#<?= (int)$m['id'] ?></span>
                        <span><?= htmlspecialchars($m['created_at']) ?></span>
                    </div>
                    <h3><?= htmlspecialchars($m['sujet'] ?: '(sans sujet)') ?></h3>
                    <div class="meta"><?= htmlspecialchars($m['nom']) ?> — <?= htmlspecialchars($m['email']) ?></div>
                    <p><?= nl2br(htmlspecialchars($m['message'])) ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
