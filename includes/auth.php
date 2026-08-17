<?php
/**
 * Gestion de session et protection des pages réservées à l'admin.
 */

if (session_status() === PHP_SESSION_NONE) {
    // Options de cookie de session plus sûres
    session_set_cookie_params([
        'httponly' => true,   // inaccessible en JavaScript
        'samesite' => 'Lax',
        // 'secure' => true,   // décommente une fois le site en HTTPS
    ]);
    session_start();
}

/**
 * Renvoie true si un admin est connecté.
 */
function estConnecte(): bool {
    return isset($_SESSION['admin_id']);
}

/**
 * À appeler en haut de toute page réservée à l'admin.
 * Redirige vers la page de connexion si personne n'est connecté.
 */
function protegerPage(): void {
    if (!estConnecte()) {
        header("Location: ../login.php");
        exit();
    }
}

/**
 * Petite protection anti-force-brute très simple basée sur la session.
 * (Une vraie protection se ferait plutôt en base de données ou au niveau serveur.)
 */
function tropDeTentatives(): bool {
    if (!isset($_SESSION['tentatives'])) {
        $_SESSION['tentatives'] = 0;
        $_SESSION['premiere_tentative'] = time();
    }

    // Réinitialise le compteur après 15 minutes
    if (time() - $_SESSION['premiere_tentative'] > 900) {
        $_SESSION['tentatives'] = 0;
        $_SESSION['premiere_tentative'] = time();
    }

    return $_SESSION['tentatives'] >= 5;
}

function enregistrerTentativeEchouee(): void {
    $_SESSION['tentatives'] = ($_SESSION['tentatives'] ?? 0) + 1;
}

function reinitialiserTentatives(): void {
    unset($_SESSION['tentatives'], $_SESSION['premiere_tentative']);
}
