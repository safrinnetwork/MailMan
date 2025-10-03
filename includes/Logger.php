<?php
namespace MailMan;

class Logger {
    private string $logFile;

    public function __construct() {
        $logDir = __DIR__ . '/../data/logs/';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        $this->logFile = $logDir . 'email_log.json';
    }

    public function log(array $entry): void {
        $logs = $this->getAll();
        $logs[] = $entry;

        // Keep only last 1000 entries
        if (count($logs) > 1000) {
            $logs = array_slice($logs, -1000);
        }

        file_put_contents($this->logFile, json_encode($logs, JSON_PRETTY_PRINT));
    }

    public function getAll(): array {
        if (!file_exists($this->logFile)) {
            return [];
        }

        $content = file_get_contents($this->logFile);
        return json_decode($content, true) ?? [];
    }

    public function getRecent(int $limit = 50): array {
        $logs = $this->getAll();
        return array_slice(array_reverse($logs), 0, $limit);
    }
}
