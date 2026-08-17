<?php
require_once __DIR__ . '/../includes/auth.php';
protegerPage();

require_once __DIR__ . '/../config/database.php';

$database = new Database();
$db = $database->getConnection();

$succes = "";
$erreur = "";

// Sauvegarde des modifications
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom        = trim($_POST['nom'] ?? '');
    $telephone  = trim($_POST['telephone'] ?? '');
    $niveau     = trim($_POST['niveau'] ?? '');
    $universite = trim($_POST['universite'] ?? '');
    $age        = trim($_POST['age'] ?? '');
    $ville      = trim($_POST['ville'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $disponible = isset($_POST['disponible']) ? 1 : 0;
    $a_propos   = trim($_POST['a_propos'] ?? '');

    if ($nom === '' || $email === '') {
        $erreur = "Le nom et l'email sont obligatoires.";
    } else {
        $stmt = $db->prepare(
            "UPDATE profile_content SET
                nom = :nom, telephone = :telephone, niveau = :niveau,
                universite = :universite, age = :age, ville = :ville,
                email = :email, disponible = :disponible, a_propos = :a_propos
             WHERE id = 1"
        );
        $stmt->execute([
            ':nom' => $nom, ':telephone' => $telephone, ':niveau' => $niveau,
            ':universite' => $universite, ':age' => $age, ':ville' => $ville,
            ':email' => $email, ':disponible' => $disponible, ':a_propos' => $a_propos,
        ]);
        $succes = "Modifications enregistrées avec succès.";
    }
}

// Récupère les valeurs actuelles pour pré-remplir le formulaire
$stmt = $db->query("SELECT * FROM profile_content WHERE id = 1");
$profil = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le profil</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy-950: #050B2E; --navy-900: #080E3A;
            --ink: #F2F4FA; --muted: #9AA3C7;
            --red: #E4392E; --cyan: #33D9E8; --green: #2ECC71;
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
        .topbar nav a {
            font-family: var(--font-mono); font-size: 0.8rem; color: var(--muted);
            text-decoration: none; border: 1px solid rgba(255,255,255,0.15);
            padding: 0.4rem 0.9rem; border-radius: 999px; margin-left: 0.5rem;
        }
        .topbar nav a:hover { border-color: var(--cyan); color: var(--cyan); }
        .container { max-width: 640px; margin: 0 auto; padding: 2rem; }
        label {
            display: block; font-family: var(--font-mono); font-size: 0.75rem;
            color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em;
            margin-bottom: 0.4rem; margin-top: 1.1rem;
        }
        input, textarea {
            width: 100%; background-color: rgba(255,255,255,0.03);
            border: 1px solid rgba(51,217,232,0.25); color: var(--ink);
            border-radius: 6px; padding: 0.6rem 0.85rem; font-family: var(--font-body);
        }
        input:focus, textarea:focus {
            outline: none; border-color: var(--cyan);
            box-shadow: 0 0 0 3px rgba(51,217,232,0.15);
        }
        textarea { resize: vertical; }
        .checkbox-row { display: flex; align-items: center; gap: 0.5rem; margin-top: 1.1rem; }
        .checkbox-row input { width: auto; }
        .checkbox-row label { margin: 0; }
        button {
            margin-top: 1.75rem; font-family: var(--font-mono); font-size: 0.85rem;
            letter-spacing: 0.06em; background-color: var(--red); border: 1px solid var(--red);
            color: white; padding: 0.7rem 1.6rem; border-radius: 999px; cursor: pointer;
        }
        button:hover { background-color: transparent; color: var(--red); }
        .msg {
            padding: 0.7rem 1rem; border-radius: 6px; font-size: 0.9rem; margin-bottom: 1rem;
        }
        .succes { background: rgba(46,204,113,0.1); border: 1px solid var(--green); color: #b7f5cf; }
        .erreur { background: rgba(228,57,46,0.1); border: 1px solid var(--red); color: #ffb3ae; }
    </style>
</head>
<body>
    <div class="topbar">
        <h1>Modifier le profil</h1>
        <nav>
            <a href="index.php">Messages</a>
            <a href="../logout.php">Déconnexion</a>
        </nav>
    </div>

    <div class="container">
        <?php if ($succes): ?><div class="msg succes"><?= htmlspecialchars($succes) ?></div><?php endif; ?>
        <?php if ($erreur): ?><div class="msg erreur"><?= htmlspecialchars($erreur) ?></div><?php endif; ?>

        <form method="POST" action="edit_profile.php">
            <label for="nom">Nom complet</label>
            <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($profil['nom']) ?>" required>

            <label for="telephone">Téléphone</label>
            <input type="text" id="telephone" name="telephone" value="<?= htmlspecialchars($profil['telephone']) ?>">

            <label for="niveau">Niveau d'étude</label>
            <input type="text" id="niveau" name="niveau" value="<?= htmlspecialchars($profil['niveau']) ?>">

            <label for="universite">Université</label>
            <input type="text" id="universite" name="universite" value="<?= htmlspecialchars($profil['universite']) ?>">

            <label for="age">Âge</label>
            <input type="text" id="age" name="age" value="<?= htmlspecialchars($profil['age']) ?>">

            <label for="ville">Ville</label>
            <input type="text" id="ville" name="ville" value="<?= htmlspecialchars($profil['ville']) ?>">

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($profil['email']) ?>" required>

            <div class="checkbox-row">
                <input type="checkbox" id="disponible" name="disponible" <?= $profil['disponible'] ? 'checked' : '' ?>>
                <label for="disponible">Disponible</label>
            </div>

            <label for="a_propos">À propos de moi</label>
            <textarea id="a_propos" name="a_propos" rows="5"><?= htmlspecialchars($profil['a_propos']) ?></textarea>

            <button type="submit">Enregistrer</button>
        </form>
    </div>
</body>
</html>
