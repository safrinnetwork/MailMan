<?php
$pageTitle = 'Template Email - MailMan';
require_once __DIR__ . '/../includes/header.php';

use MailMan\Template;

$template = new Template();
$message = '';
$messageType = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $name = $_POST['name'] ?? '';
        $subject = $_POST['subject'] ?? '';
        $content = $_POST['content'] ?? '';

        if ($name && $subject && $content) {
            $template->save($name, $subject, $content);
            $message = 'Template berhasil dibuat';
            $messageType = 'success';
        } else {
            $message = 'Semua field harus diisi';
            $messageType = 'danger';
        }
    } elseif ($action === 'update') {
        $id = $_POST['id'] ?? '';
        $name = $_POST['name'] ?? '';
        $subject = $_POST['subject'] ?? '';
        $content = $_POST['content'] ?? '';

        if ($template->update($id, $name, $subject, $content)) {
            $message = 'Template berhasil diperbarui';
            $messageType = 'success';
        } else {
            $message = 'Gagal memperbarui template';
            $messageType = 'danger';
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'] ?? '';
        if ($template->delete($id)) {
            $message = 'Template berhasil dihapus';
            $messageType = 'success';
        } else {
            $message = 'Gagal menghapus template';
            $messageType = 'danger';
        }
    }
}

$templates = $template->getAll();
?>

<?php if ($message): ?>
    <div class="alert alert-<?= $messageType ?>">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <span>Template Email</span>
        <button class="btn btn-primary btn-sm" onclick="showCreateModal()">+ Buat Template Baru</button>
    </div>

    <?php if (empty($templates)): ?>
        <p style="color: var(--text-secondary); text-align: center; padding: 2rem;">
            Belum ada template. Buat template pertama Anda!
        </p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Nama Template</th>
                    <th>Subject</th>
                    <th>Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($templates as $tpl): ?>
                    <tr>
                        <td><?= htmlspecialchars($tpl['name']) ?></td>
                        <td><?= htmlspecialchars($tpl['subject']) ?></td>
                        <td><?= htmlspecialchars($tpl['created_at']) ?></td>
                        <td>
                            <button class="btn btn-secondary btn-sm" onclick='previewTemplate(<?= json_encode($tpl) ?>)'>Preview</button>
                            <button class="btn btn-primary btn-sm" onclick='editTemplate(<?= json_encode($tpl) ?>)'>Edit</button>
                            <button class="btn btn-danger btn-sm" onclick='deleteTemplate("<?= $tpl['id'] ?>", "<?= htmlspecialchars($tpl['name']) ?>")'>Hapus</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Create/Edit Modal -->
<div id="templateModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Buat Template</h3>
            <button class="close" onclick="closeModal()">&times;</button>
        </div>
        <form method="POST" id="templateForm">
            <div class="modal-body">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="id" id="templateId">

                <div class="form-group">
                    <label class="form-label">Nama Template</label>
                    <input type="text" name="name" id="templateName" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Subject Email</label>
                    <input type="text" name="subject" id="templateSubject" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Konten HTML</label>
                    <textarea name="content" id="templateContent" class="form-control" style="min-height: 300px; font-family: 'Courier New', monospace;" required></textarea>
                    <small style="color: var(--text-secondary); display: block; margin-top: 0.5rem;">
                        Gunakan variabel seperti: {{nama}}, {{email}}, {{custom_field}}
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Preview Modal -->
<div id="previewModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Preview Template</h3>
            <button class="close" onclick="closePreviewModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div style="margin-bottom: 1rem;">
                <strong>Subject:</strong>
                <div id="previewSubject" style="color: var(--text-secondary); margin-top: 0.5rem;"></div>
            </div>
            <div style="background-color: white; padding: 1rem; border-radius: 5px;">
                <iframe id="previewFrame" style="width: 100%; min-height: 400px; border: none;"></iframe>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closePreviewModal()">Tutup</button>
        </div>
    </div>
</div>

<script>
function showCreateModal() {
    document.getElementById('modalTitle').textContent = 'Buat Template';
    document.getElementById('formAction').value = 'create';
    document.getElementById('templateForm').reset();
    document.getElementById('templateModal').classList.add('show');
}

function editTemplate(template) {
    document.getElementById('modalTitle').textContent = 'Edit Template';
    document.getElementById('formAction').value = 'update';
    document.getElementById('templateId').value = template.id;
    document.getElementById('templateName').value = template.name;
    document.getElementById('templateSubject').value = template.subject;
    document.getElementById('templateContent').value = template.content;
    document.getElementById('templateModal').classList.add('show');
}

function closeModal() {
    document.getElementById('templateModal').classList.remove('show');
}

function previewTemplate(template) {
    document.getElementById('previewSubject').textContent = template.subject;
    const iframe = document.getElementById('previewFrame');
    iframe.srcdoc = template.content;
    document.getElementById('previewModal').classList.add('show');
}

function closePreviewModal() {
    document.getElementById('previewModal').classList.remove('show');
}

function deleteTemplate(id, name) {
    if (confirm('Apakah Anda yakin ingin menghapus template "' + name + '"?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="${id}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Close modal on outside click
window.onclick = function(event) {
    const modal = document.getElementById('templateModal');
    const previewModal = document.getElementById('previewModal');
    if (event.target === modal) {
        closeModal();
    }
    if (event.target === previewModal) {
        closePreviewModal();
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
