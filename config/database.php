<?php
/**
 * Connexion à la base de données via PDO.
 * Modifie les identifiants ci-dessous selon ton hébergement.
 */

class Database {
    private $host     = "localhost";
    private $db_name  = "portfolio_db";
    private $username = "root";      // à changer sur ton hébergeur
    private $password = "";          // à changer sur ton hébergeur
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur DB : " . $e->getMessage());
            die("Impossible de se connecter à la base de données pour le moment.");
        }
        return $this->conn;
    }
}