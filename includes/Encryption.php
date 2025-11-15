<?php
namespace MailMan;

class Encryption {
    private const CIPHER = 'AES-256-CBC';
    private string $key;

    public function __construct(string $key = null) {
        // Use environment key or default (should be changed in production)
        $this->key = $key ?? hash('sha256', 'mailman_secret_key_change_this');
    }

    public function encrypt(string $data): string {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(self::CIPHER));
        $encrypted = openssl_encrypt($data, self::CIPHER, $this->key, 0, $iv);
        return base64_encode($encrypted . '::' . $iv);
    }

    public function decrypt(string $data): string {
        $decoded = base64_decode($data);
        list($encrypted, $iv) = explode('::', $decoded, 2);
        return openssl_decrypt($encrypted, self::CIPHER, $this->key, 0, $iv);
    }

    public function encryptArray(array $data): string {
        return $this->encrypt(json_encode($data));
    }

    public function decryptArray(string $data): array {
        $decrypted = $this->decrypt($data);
        return json_decode($decrypted, true) ?? [];
    }
}
