<?php
namespace MailMan;

class Auth {
    private Config $config;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->config = new Config();
    }

    public function login(string $username, string $password): bool {
        $auth = $this->config->getAuth();

        if ($auth['username'] === $username && password_verify($password, $auth['password'])) {
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['login_time'] = time();
            return true;
        }
        return false;
    }

    public function logout(): void {
        session_destroy();
        session_start();
    }

    public function isLoggedIn(): bool {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    public function requireAuth(): void {
        if (!$this->isLoggedIn()) {
            header('Location: /index.php');
            exit;
        }
    }

    public function getUsername(): string {
        return $_SESSION['username'] ?? '';
    }
}
