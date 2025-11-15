<?php
$pageTitle = 'Konfigurasi - MailMan';
require_once __DIR__ . '/../includes/header.php';

use MailMan\Config;

$config = new Config();
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_smtp') {
        $smtp = [
            'host' => $_POST['smtp_host'] ?? '',
            'port' => (int)($_POST['smtp_port'] ?? 587),
            'username' => $_POST['smtp_username'] ?? '',
            'password' => $_POST['smtp_password'] ?? '',
            'from_email' => $_POST['from_email'] ?? '',
            'from_name' => $_POST['from_name'] ?? ''
        ];

        if ($config->updateSMTP($smtp)) {
            $message = 'Konfigurasi SMTP berhasil diperbarui';
            $messageType = 'success';
        } else {
            $message = 'Gagal memperbarui konfigurasi SMTP';
            $messageType = 'danger';
        }
    } elseif ($action === 'update_auth') {
        $username = $_POST['new_username'] ?? '';
        $password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if ($password !== $confirm_password) {
            $message = 'Password tidak cocok';
            $messageType = 'danger';
        } elseif (empty($username) || empty($password)) {
            $message = 'Username dan password tidak boleh kosong';
            $messageType = 'danger';
        } else {
            if ($config->updateAuth($username, $password)) {
                $message = 'Kredensial login berhasil diperbarui';
                $messageType = 'success';
            } else {
                $message = 'Gagal memperbarui kredensial login';
                $messageType = 'danger';
            }
        }
    }
}

$currentConfig = $config->get();
$smtp = $currentConfig['smtp'] ?? [];
?>

<?php if ($message): ?>
    <div class="alert alert-<?= $messageType ?>">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">Konfigurasi SMTP Gmail</div>

    <form method="POST">
        <input type="hidden" name="action" value="update_smtp">

        <div class="form-group">
            <label class="form-label">SMTP Host</label>
            <input type="text" name="smtp_host" class="form-control"
                   value="<?= htmlspecialchars($smtp['host'] ?? 'smtp.gmail.com') ?>"
                   placeholder="smtp.gmail.com" required>
        </div>

        <div class="form-group">
            <label class="form-label">SMTP Port</label>
            <input type="number" name="smtp_port" class="form-control"
                   value="<?= htmlspecialchars($smtp['port'] ?? '587') ?>"
                   placeholder="587" required>
        </div>

        <div class="form-group">
            <label class="form-label">Email Gmail</label>
            <input type="email" name="smtp_username" class="form-control"
                   value="<?= htmlspecialchars($smtp['username'] ?? '') ?>"
                   placeholder="your-email@gmail.com" required>
        </div>

        <div class="form-group">
            <label class="form-label">App Password Gmail</label>
            <input type="password" name="smtp_password" class="form-control"
                   value="<?= htmlspecialchars($smtp['password'] ?? '') ?>"
                   placeholder="App Password dari Google" required>
            <small style="color: var(--text-secondary); display: block; margin-top: 0.5rem;">
                Generate App Password di: <a href="https://myaccount.google.com/apppasswords" target="_blank" style="color: var(--accent);">Google Account Settings</a>
            </small>
        </div>

        <div class="form-group">
            <label class="form-label">Email Pengirim (From)</label>
            <input type="email" name="from_email" class="form-control"
                   value="<?= htmlspecialchars($smtp['from_email'] ?? '') ?>"
                   placeholder="your-email@gmail.com" required>
        </div>

        <div class="form-group">
            <label class="form-label">Nama Pengirim (From Name)</label>
            <input type="text" name="from_name" class="form-control"
                   value="<?= htmlspecialchars($smtp['from_name'] ?? '') ?>"
                   placeholder="Your Name" required>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Konfigurasi SMTP</button>
    </form>
</div>

<div class="card">
    <div class="card-header">Ubah Kredensial Login</div>

    <form method="POST">
        <input type="hidden" name="action" value="update_auth">

        <div class="form-group">
            <label class="form-label">Username Baru</label>
            <input type="text" name="new_username" class="form-control" required>
        </div>

        <div class="form-group">
            <label class="form-label">Password Baru</label>
            <input type="password" name="new_password" class="form-control" required>
        </div>

        <div class="form-group">
            <label class="form-label">Konfirmasi Password</label>
            <input type="password" name="confirm_password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success">Ubah Kredensial</button>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
