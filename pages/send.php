<?php
$pageTitle = 'Kirim Email - MailMan';
require_once __DIR__ . '/../includes/header.php';

use MailMan\Template;
use MailMan\Mailer;
use MailMan\Config;

$template = new Template();
$mailer = new Mailer();
$config = new Config();

$message = '';
$messageType = '';

// Check if SMTP is configured
$smtp = $config->getSMTP();
$smtpConfigured = !empty($smtp['host']) && !empty($smtp['username']) && !empty($smtp['password']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $toEmail = $_POST['to_email'] ?? '';
    $toName = $_POST['to_name'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $fromName = $_POST['from_name'] ?? $smtp['from_name'] ?? '';
    $useTemplate = $_POST['use_template'] ?? 'custom';
    $templateId = $_POST['template_id'] ?? '';
    $customContent = $_POST['custom_content'] ?? '';
    $contentType = $_POST['content_type'] ?? 'html';
    $includeRecipientName = isset($_POST['include_recipient_name']);

    // Validate email
    if (!$mailer->validateEmail($toEmail)) {
        $message = 'Format email tidak valid';
        $messageType = 'danger';
    } elseif (!$smtpConfigured) {
        $message = 'SMTP belum dikonfigurasi. Silakan ke halaman Konfigurasi.';
        $messageType = 'danger';
    } else {
        // Prepare content
        $body = '';
        $isHtml = $contentType === 'html';

        if ($useTemplate === 'saved' && $templateId) {
            $tpl = $template->get($templateId);
            if ($tpl) {
                $subject = $tpl['subject'];
                $body = $tpl['content'];
                $isHtml = true;

                // Replace variables
                $variables = [
                    'nama' => $toName,
                    'email' => $toEmail
                ];

                // Add custom variables from form
                foreach ($_POST as $key => $value) {
                    if (strpos($key, 'var_') === 0) {
                        $varName = substr($key, 4);
                        $variables[$varName] = $value;
                    }
                }

                $body = $template->render($body, $variables);
            }
        } else {
            $body = $customContent;
        }

        // Handle attachments
        $attachments = [];
        if (!empty($_FILES['attachments']['name'][0])) {
            $uploadDir = __DIR__ . '/../uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            foreach ($_FILES['attachments']['tmp_name'] as $key => $tmpName) {
                if ($_FILES['attachments']['error'][$key] === UPLOAD_ERR_OK) {
                    $fileName = basename($_FILES['attachments']['name'][$key]);
                    $uploadPath = $uploadDir . uniqid() . '_' . $fileName;

                    if (move_uploaded_file($tmpName, $uploadPath)) {
                        $attachments[] = [
                            'path' => $uploadPath,
                            'name' => $fileName
                        ];
                    }
                }
            }
        }

        // Send email
        $result = $mailer->send([
            'to_email' => $toEmail,
            'to_name' => $includeRecipientName ? $toName : '',
            'from_name' => $fromName,
            'subject' => $subject,
            'body' => $body,
            'is_html' => $isHtml,
            'attachments' => $attachments
        ]);

        // Clean up attachments
        foreach ($attachments as $attachment) {
            if (file_exists($attachment['path'])) {
                unlink($attachment['path']);
            }
        }

        $message = $result['message'];
        $messageType = $result['success'] ? 'success' : 'danger';
    }
}

$templates = $template->getAll();
?>

<?php if (!$smtpConfigured): ?>
    <div class="alert alert-warning">
        <strong>Perhatian:</strong> SMTP belum dikonfigurasi. Silakan ke halaman
        <a href="/pages/config.php" style="color: var(--accent); text-decoration: underline;">Konfigurasi</a>
        untuk mengatur SMTP Gmail.
    </div>
<?php endif; ?>

<?php if ($message): ?>
    <div class="alert alert-<?= $messageType ?>">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">Kirim Email</div>

    <form method="POST" enctype="multipart/form-data" id="sendForm">
        <div class="form-group">
            <label class="form-label">Sumber Template</label>
            <div class="form-check">
                <input type="radio" name="use_template" value="saved" id="useSaved" class="form-check-input"
                       onchange="toggleTemplateSource()" <?= empty($templates) ? 'disabled' : '' ?>>
                <label for="useSaved" class="form-check-label">
                    Gunakan Template Tersimpan <?= empty($templates) ? '(Belum ada template)' : '' ?>
                </label>
            </div>
            <div class="form-check">
                <input type="radio" name="use_template" value="custom" id="useCustom" class="form-check-input"
                       onchange="toggleTemplateSource()" checked>
                <label for="useCustom" class="form-check-label">Template Custom / Teks Biasa</label>
            </div>
        </div>

        <div id="savedTemplateSection" style="display: none;">
            <div class="form-group">
                <label class="form-label">Pilih Template</label>
                <select name="template_id" id="templateSelect" class="form-control" onchange="loadTemplateVariables()">
                    <option value="">-- Pilih Template --</option>
                    <?php foreach ($templates as $tpl): ?>
                        <option value="<?= $tpl['id'] ?>" data-content='<?= htmlspecialchars($tpl['content']) ?>'>
                            <?= htmlspecialchars($tpl['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div id="templateVariables" style="display: none;">
                <div class="form-group">
                    <label class="form-label">Variabel Template</label>
                    <div id="variablesList"></div>
                </div>
            </div>
        </div>

        <div id="customContentSection">
            <div class="form-group">
                <label class="form-label">Tipe Konten</label>
                <div class="form-check">
                    <input type="radio" name="content_type" value="html" id="typeHtml" class="form-check-input" checked>
                    <label for="typeHtml" class="form-check-label">HTML</label>
                </div>
                <div class="form-check">
                    <input type="radio" name="content_type" value="text" id="typeText" class="form-check-input">
                    <label for="typeText" class="form-check-label">Teks Biasa</label>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Subject Email</label>
                <input type="text" name="subject" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label">Konten Email</label>
                <textarea name="custom_content" class="form-control" style="min-height: 200px;" required></textarea>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Nama Pengirim</label>
            <input type="text" name="from_name" class="form-control"
                   value="<?= htmlspecialchars($smtp['from_name'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <div class="form-check">
                <input type="checkbox" name="include_recipient_name" id="includeRecipientName"
                       class="form-check-input" onchange="toggleRecipientName()">
                <label for="includeRecipientName" class="form-check-label">
                    Masukkan nama penerima (untuk personalisasi)
                </label>
            </div>
        </div>

        <div id="recipientNameSection" style="display: none;">
            <div class="form-group">
                <label class="form-label">Nama Penerima</label>
                <input type="text" name="to_name" id="toName" class="form-control">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Email Penerima</label>
            <input type="email" name="to_email" class="form-control" required>
        </div>

        <div class="form-group">
            <label class="form-label">Lampiran (Attachment)</label>
            <label for="fileInput" class="file-upload-label">
                📎 Pilih File
            </label>
            <input type="file" name="attachments[]" id="fileInput" multiple onchange="displayFiles()">
            <div id="fileList" class="file-list"></div>
        </div>

        <button type="submit" class="btn btn-success" <?= !$smtpConfigured ? 'disabled' : '' ?>>
            Kirim Email
        </button>
    </form>
</div>

<script>
function toggleTemplateSource() {
    const useSaved = document.getElementById('useSaved').checked;
    document.getElementById('savedTemplateSection').style.display = useSaved ? 'block' : 'none';
    document.getElementById('customContentSection').style.display = useSaved ? 'none' : 'block';

    // Toggle required attribute
    document.querySelector('[name="custom_content"]').required = !useSaved;
    document.querySelector('[name="subject"]').required = !useSaved;
}

function toggleRecipientName() {
    const include = document.getElementById('includeRecipientName').checked;
    document.getElementById('recipientNameSection').style.display = include ? 'block' : 'none';
    document.getElementById('toName').required = include;
}

function loadTemplateVariables() {
    const select = document.getElementById('templateSelect');
    const selectedOption = select.options[select.selectedIndex];
    const content = selectedOption.getAttribute('data-content');

    if (!content) {
        document.getElementById('templateVariables').style.display = 'none';
        return;
    }

    // Extract variables from template ({{variable}})
    const regex = /\{\{([^}]+)\}\}/g;
    const variables = new Set();
    let match;

    while ((match = regex.exec(content)) !== null) {
        const varName = match[1].trim();
        if (varName !== 'nama' && varName !== 'email') {
            variables.add(varName);
        }
    }

    if (variables.size > 0) {
        let html = '<div style="background-color: var(--bg-light); padding: 1rem; border-radius: 5px;">';
        html += '<p style="color: var(--text-secondary); margin-bottom: 1rem;">Template ini menggunakan variabel berikut:</p>';

        variables.forEach(varName => {
            html += `
                <div class="form-group">
                    <label class="form-label">{{${varName}}}</label>
                    <input type="text" name="var_${varName}" class="form-control" placeholder="Nilai untuk ${varName}">
                </div>
            `;
        });

        html += '</div>';
        document.getElementById('variablesList').innerHTML = html;
        document.getElementById('templateVariables').style.display = 'block';
    } else {
        document.getElementById('templateVariables').style.display = 'none';
    }
}

function displayFiles() {
    const input = document.getElementById('fileInput');
    const fileList = document.getElementById('fileList');
    fileList.innerHTML = '';

    if (input.files.length > 0) {
        Array.from(input.files).forEach(file => {
            const fileItem = document.createElement('div');
            fileItem.className = 'file-item';
            fileItem.innerHTML = `
                <span>📎 ${file.name} (${formatFileSize(file.size)})</span>
            `;
            fileList.appendChild(fileItem);
        });
    }
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
