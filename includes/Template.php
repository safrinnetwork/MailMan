<?php
namespace MailMan;

class Template {
    private string $templateDir;

    public function __construct() {
        $this->templateDir = __DIR__ . '/../data/templates/';
        if (!is_dir($this->templateDir)) {
            mkdir($this->templateDir, 0755, true);
        }
    }

    public function getAll(): array {
        $templates = [];
        $files = glob($this->templateDir . '*.json');

        foreach ($files as $file) {
            $data = json_decode(file_get_contents($file), true);
            if ($data) {
                $data['id'] = basename($file, '.json');
                $templates[] = $data;
            }
        }

        return $templates;
    }

    public function get(string $id): ?array {
        $file = $this->templateDir . $id . '.json';
        if (!file_exists($file)) {
            return null;
        }

        $data = json_decode(file_get_contents($file), true);
        if ($data) {
            $data['id'] = $id;
        }
        return $data;
    }

    public function save(string $name, string $subject, string $content): string {
        $id = uniqid('tpl_');
        $data = [
            'name' => $name,
            'subject' => $subject,
            'content' => $content,
            'created_at' => date('Y-m-d H:i:s')
        ];

        file_put_contents($this->templateDir . $id . '.json', json_encode($data, JSON_PRETTY_PRINT));
        return $id;
    }

    public function update(string $id, string $name, string $subject, string $content): bool {
        $template = $this->get($id);
        if (!$template) {
            return false;
        }

        $data = [
            'name' => $name,
            'subject' => $subject,
            'content' => $content,
            'created_at' => $template['created_at'],
            'updated_at' => date('Y-m-d H:i:s')
        ];

        return file_put_contents($this->templateDir . $id . '.json', json_encode($data, JSON_PRETTY_PRINT)) !== false;
    }

    public function delete(string $id): bool {
        $file = $this->templateDir . $id . '.json';
        if (file_exists($file)) {
            return unlink($file);
        }
        return false;
    }

    public function render(string $content, array $variables): string {
        foreach ($variables as $key => $value) {
            $content = str_replace('{{' . $key . '}}', htmlspecialchars($value), $content);
        }
        return $content;
    }
}
