<?php
namespace MailMan;

class Config {
    private string $configFile;
    private Encryption $encryption;

    public function __construct() {
        $this->configFile = __DIR__ . '/../config/settings.json';
        $this->encryption = new Encryption();
        $this->initializeConfig();
    }

    private function initializeConfig(): void {
        if (!file_exists($this->configFile)) {
            $defaultConfig = [
                'auth' => [
                    'username' => 'user1234',
                    'password' => password_hash('mostech', PASSWORD_DEFAULT)
                ],
                'smtp' => [
                    'host' => '',
                    'port' => 587,
                    'username' => '',
                    'password' => '',
                    'from_email' => '',
                    'from_name' => ''
                ]
            ];
            $this->save($defaultConfig);
        }
    }

    public function get(): array {
        if (!file_exists($this->configFile)) {
            return [];
        }
        $encrypted = file_get_contents($this->configFile);
        return $this->encryption->decryptArray($encrypted);
    }

    public function save(array $config): bool {
        $encrypted = $this->encryption->encryptArray($config);
        return file_put_contents($this->configFile, $encrypted) !== false;
    }

    public function getAuth(): array {
        return $this->get()['auth'] ?? [];
    }

    public function getSMTP(): array {
        return $this->get()['smtp'] ?? [];
    }

    public function updateAuth(string $username, string $password): bool {
        $config = $this->get();
        $config['auth'] = [
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ];
        return $this->save($config);
    }

    public function updateSMTP(array $smtp): bool {
        $config = $this->get();
        $config['smtp'] = $smtp;
        return $this->save($config);
    }
}
