<?php
$pageTitle = 'Log Email - MailMan';
require_once __DIR__ . '/../includes/header.php';

use MailMan\Logger;

$logger = new Logger();
$logs = $logger->getRecent(100);
?>

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between">
            <span>Riwayat Pengiriman Email</span>
            <span style="color: var(--text-secondary); font-size: 0.9rem;">
                Menampilkan <?= count($logs) ?> log terakhir
            </span>
        </div>
    </div>

    <?php if (empty($logs)): ?>
        <p style="color: var(--text-secondary); text-align: center; padding: 2rem;">
            Belum ada riwayat pengiriman email.
        </p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Status</th>
                    <th>Penerima</th>
                    <th>Nama</th>
                    <th>Subject</th>
                    <th>Error</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td style="white-space: nowrap;"><?= htmlspecialchars($log['timestamp']) ?></td>
                        <td>
                            <?php if ($log['status'] === 'success'): ?>
                                <span style="color: var(--success);">✓ Berhasil</span>
                            <?php else: ?>
                                <span style="color: var(--danger);">✗ Gagal</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($log['to_email']) ?></td>
                        <td><?= htmlspecialchars($log['to_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($log['subject']) ?></td>
                        <td>
                            <?php if (isset($log['error'])): ?>
                                <span style="color: var(--danger); font-size: 0.875rem;">
                                    <?= htmlspecialchars($log['error']) ?>
                                </span>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<div style="margin-top: 1rem; padding: 1rem; background-color: var(--bg-dark); border-radius: 8px; border: 1px solid var(--border);">
    <h4 style="margin-bottom: 1rem;">Statistik</h4>
    <?php
    $totalSuccess = count(array_filter($logs, fn($log) => $log['status'] === 'success'));
    $totalFailed = count(array_filter($logs, fn($log) => $log['status'] === 'failed'));
    $successRate = count($logs) > 0 ? round(($totalSuccess / count($logs)) * 100, 1) : 0;
    ?>
    <div class="d-flex gap-2">
        <div style="flex: 1; padding: 1rem; background-color: var(--bg-light); border-radius: 5px;">
            <div style="color: var(--text-secondary); font-size: 0.875rem;">Total Email</div>
            <div style="font-size: 1.5rem; font-weight: bold; margin-top: 0.5rem;">
                <?= count($logs) ?>
            </div>
        </div>
        <div style="flex: 1; padding: 1rem; background-color: var(--bg-light); border-radius: 5px;">
            <div style="color: var(--text-secondary); font-size: 0.875rem;">Berhasil</div>
            <div style="font-size: 1.5rem; font-weight: bold; margin-top: 0.5rem; color: var(--success);">
                <?= $totalSuccess ?>
            </div>
        </div>
        <div style="flex: 1; padding: 1rem; background-color: var(--bg-light); border-radius: 5px;">
            <div style="color: var(--text-secondary); font-size: 0.875rem;">Gagal</div>
            <div style="font-size: 1.5rem; font-weight: bold; margin-top: 0.5rem; color: var(--danger);">
                <?= $totalFailed ?>
            </div>
        </div>
        <div style="flex: 1; padding: 1rem; background-color: var(--bg-light); border-radius: 5px;">
            <div style="color: var(--text-secondary); font-size: 0.875rem;">Success Rate</div>
            <div style="font-size: 1.5rem; font-weight: bold; margin-top: 0.5rem; color: var(--accent);">
                <?= $successRate ?>%
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
