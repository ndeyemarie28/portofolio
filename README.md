# Mise en place — Base de données + Page admin

## 1. Créer la base de données

Importe le fichier `database/schema.sql` dans phpMyAdmin (ou en ligne de commande) :

```bash
mysql -u root -p < database/schema.sql
```

Cela crée la base `portfolio_db` avec les tables `admin_users` et `messages`.

## 2. Configurer la connexion

Ouvre `config/database.php` et mets à jour `$username` / `$password` selon ton serveur MySQL local ou ton hébergeur.

## 3. Copier les fichiers dans ton projet

Place ces fichiers/dossiers à la racine de ton site (là où se trouve `essai.html`) :

```
config/
includes/
admin/
database/          (tu peux le supprimer après l'import du .sql)
login.php
logout.php
traiter_contact.php
generate_admin.php
```

## 4. Créer ton compte admin

1. Dans `generate_admin.php`, remplace `$username` et `$password` par TES identifiants.
2. Ouvre `http://localhost/ton-projet/generate_admin.php` dans le navigateur une seule fois.
3. **Supprime immédiatement `generate_admin.php` du serveur** — c'est un script à usage unique, il ne doit jamais rester en ligne.

## 5. Relier ton formulaire de contact

Dans `essai.html`, modifie le formulaire de contact ainsi (ajoute `action`, `method`, et les `name` sur chaque champ) :

```html
<form class="contact-form" action="traiter_contact.php" method="POST">
    <div class="row g-3">
        <div class="col-md-6">
            <input type="text" name="nom" class="form-control" placeholder="Your Name" required>
        </div>
        <div class="col-md-6">
            <input type="email" name="email" class="form-control" placeholder="Your Email" required>
        </div>
        <div class="col-12">
            <input type="text" name="sujet" class="form-control" placeholder="Subject">
        </div>
        <div class="col-12">
            <textarea name="message" class="form-control" rows="5" placeholder="Your Message" required></textarea>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-cta">Send Message</button>
        </div>
    </div>
</form>
```

## 6. Accéder à l'admin

- Connexion : `login.php`
- Tableau de bord (protégé) : `admin/index.php`
- Déconnexion : `logout.php`

Personne d'autre ne peut voir `admin/index.php` sans être connecté : `protegerPage()` redirige automatiquement vers `login.php` si la session n'existe pas.

## Sécurité — ce qui est déjà fait

- Mots de passe **jamais stockés en clair** (`password_hash` / `password_verify`).
- Requêtes SQL préparées (PDO) → protection contre les injections SQL.
- Régénération de l'ID de session à la connexion → protection contre la fixation de session.
- Cookie de session en `HttpOnly` → inaccessible en JavaScript.
- Limitation simple des tentatives de connexion (5 essais / 15 minutes).
- `.htaccess` bloquant l'accès direct au dossier `config/`.
- Échappement systématique (`htmlspecialchars`) de tout ce qui est affiché → protection contre le XSS.

## À faire toi-même avant la mise en production

- Passer le site en **HTTPS**, puis décommenter `'secure' => true` dans `includes/auth.php`.
- Ne jamais commiter `config/database.php` avec de vrais identifiants sur un dépôt public (ajoute-le à `.gitignore`).
- Supprimer `generate_admin.php` et le dossier `database/` du serveur de production une fois utilisés.
