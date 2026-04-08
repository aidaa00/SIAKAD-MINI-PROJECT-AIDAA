<?php
require_once '../storage.php';
require_once '../layout.php';
require_once '../classes/Dosen.php';
initStorage();

$msg    = '';
$action = $_GET['action'] ?? '';
$editData = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act = $_POST['act'] ?? '';
    if ($act === 'add' || $act === 'edit') {
        $nidn  = trim($_POST['nidn'] ?? '');
        $nama  = trim($_POST['nama'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $mk    = trim($_POST['mk']   ?? '');
        if ($nidn && $nama && $email) {
            saveDosen(['nidn'=>$nidn,'nama'=>$nama,'email'=>$email,'mk'=>$mk]);
            $msg = "<div class='alert alert-success'>✅ Data dosen berhasil " . ($act==='add'?'ditambahkan':'diperbarui') . "!</div>";
            $action = '';
        } else {
            $msg = "<div class='alert alert-danger'>❌ NIDN, Nama, dan Email wajib diisi!</div>";
        }
    }
    if ($act === 'delete') {
        $nidn = $_POST['nidn'] ?? '';
        if ($nidn) { deleteDosen($nidn); $msg = "<div class='alert alert-info'>🗑️ Dosen berhasil dihapus.</div>"; }
    }
}
if ($action === 'edit') {
    $list = getDosenList();
    $editData = $list[$_GET['nidn'] ?? ''] ?? null;
}
$list = getDosenList();

ob_start(); ?>

<?= $msg ?>

<?php if ($action === 'add' || $action === 'edit'): ?>
<div class="card" style="max-width:600px">
  <div class="card-header">
    <h3><?= $action==='add' ? '➕ Tambah Dosen' : '✏️ Edit Dosen' ?></h3>
    <a href="dosen.php" class="btn btn-sm btn-outline">← Kembali</a>
  </div>
  <div class="card-body">
    <form method="POST">
      <input type="hidden" name="act" value="<?= $action ?>">
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label">NIDN *</label>
          <input class="form-control" name="nidn" placeholder="Nomor Induk Dosen"
            value="<?= htmlspecialchars($editData['nidn'] ?? '') ?>"
            <?= $action==='edit' ? 'readonly style="background:#eef2ff"' : '' ?> required>
        </div>
        <div class="form-group">
          <label class="form-label">Nama Lengkap *</label>
          <input class="form-control" name="nama" placeholder="Nama Dosen"
            value="<?= htmlspecialchars($editData['nama'] ?? '') ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label">Email *</label>
          <input class="form-control" type="email" name="email" placeholder="email@univ.ac.id"
            value="<?= htmlspecialchars($editData['email'] ?? '') ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label">Mata Kuliah Ampu</label>
          <input class="form-control" name="mk" placeholder="Pisah dengan koma"
            value="<?= htmlspecialchars($editData['mk'] ?? '') ?>">
        </div>
      </div>
      <div style="margin-top:20px;display:flex;gap:10px">
        <button class="btn btn-info" type="submit">💾 Simpan</button>
        <a href="dosen.php" class="btn btn-outline">Batal</a>
      </div>
    </form>
  </div>
</div>

<?php else: ?>
<div class="card">
  <div class="card-header">
    <h3>👨‍🏫 Daftar Dosen <span style="font-family:inherit;color:var(--text-muted);font-weight:400;font-size:13px">(<?= count($list) ?> dosen)</span></h3>
    <a href="dosen.php?action=add" class="btn btn-info">➕ Tambah Dosen</a>
  </div>
  <?php if (empty($list)): ?>
    <div class="empty-state"><div class="empty-icon">👨‍🏫</div><p>Belum ada data dosen.</p></div>
  <?php else: ?>
  <div class="table-wrapper">
    <table class="data-table">
      <thead><tr><th>#</th><th>NIDN</th><th>Nama</th><th>Email</th><th>MK Ampu</th><th>Aksi</th></tr></thead>
      <tbody>
      <?php $no=1; foreach($list as $d): ?>
        <tr>
          <td style="color:var(--text-muted)"><?= $no++ ?></td>
          <td><code style="background:rgba(139,92,246,.08);color:#7c3aed;padding:2px 8px;border-radius:6px;font-size:12px"><?= $d['nidn'] ?></code></td>
          <td style="font-weight:600"><?= htmlspecialchars($d['nama']) ?></td>
          <td style="color:var(--text-muted);font-size:13px"><?= htmlspecialchars($d['email']) ?></td>
          <td style="font-size:13px;color:var(--text-muted)"><?= htmlspecialchars($d['mk'] ?: '—') ?></td>
          <td style="display:flex;gap:6px">
            <a href="dosen.php?action=edit&nidn=<?= urlencode($d['nidn']) ?>" class="btn btn-warning btn-sm">✏️ Edit</a>
            <form method="POST" style="display:inline" onsubmit="return confirm('Hapus dosen ini?')">
              <input type="hidden" name="act" value="delete">
              <input type="hidden" name="nidn" value="<?= $d['nidn'] ?>">
              <button class="btn btn-danger btn-sm" type="submit">🗑️ Hapus</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
<?php endif; ?>

<?php
$content = ob_get_clean();
renderLayout('Manajemen Dosen', 'dosen', $content);
?>
