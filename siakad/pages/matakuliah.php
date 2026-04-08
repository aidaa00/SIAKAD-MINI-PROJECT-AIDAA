<?php
require_once '../storage.php';
require_once '../layout.php';
require_once '../classes/MataKuliah.php';
initStorage();

$msg    = '';
$action = $_GET['action'] ?? '';
$editData = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act = $_POST['act'] ?? '';
    if ($act === 'add' || $act === 'edit') {
        $kode  = strtoupper(trim($_POST['kode']  ?? ''));
        $nama  = trim($_POST['nama']  ?? '');
        $sks   = (int)($_POST['sks']  ?? 0);
        $dosen = trim($_POST['dosen'] ?? '');
        if ($kode && $nama && $sks > 0) {
            saveMataKuliah(['kode'=>$kode,'nama'=>$nama,'sks'=>$sks,'dosen'=>$dosen]);
            $msg = "<div class='alert alert-success'>✅ Mata kuliah berhasil " . ($act==='add'?'ditambahkan':'diperbarui') . "!</div>";
            $action = '';
        } else {
            $msg = "<div class='alert alert-danger'>❌ Kode, Nama, dan SKS wajib diisi!</div>";
        }
    }
    if ($act === 'delete') {
        $kode = $_POST['kode'] ?? '';
        if ($kode) { deleteMataKuliah($kode); $msg = "<div class='alert alert-info'>🗑️ Mata kuliah berhasil dihapus.</div>"; }
    }
}
if ($action === 'edit') {
    $list = getMataKuliahList();
    $editData = $list[$_GET['kode'] ?? ''] ?? null;
}
$list = getMataKuliahList();

ob_start(); ?>

<?= $msg ?>

<?php if ($action === 'add' || $action === 'edit'): ?>
<div class="card" style="max-width:600px">
  <div class="card-header">
    <h3><?= $action==='add' ? '➕ Tambah Mata Kuliah' : '✏️ Edit Mata Kuliah' ?></h3>
    <a href="matakuliah.php" class="btn btn-sm btn-outline">← Kembali</a>
  </div>
  <div class="card-body">
    <form method="POST">
      <input type="hidden" name="act" value="<?= $action ?>">
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label">Kode MK *</label>
          <input class="form-control" name="kode" placeholder="Contoh: TI101" style="text-transform:uppercase"
            value="<?= htmlspecialchars($editData['kode'] ?? '') ?>"
            <?= $action==='edit' ? 'readonly style="background:#eef2ff"' : '' ?> required>
        </div>
        <div class="form-group">
          <label class="form-label">Nama Mata Kuliah *</label>
          <input class="form-control" name="nama" placeholder="Nama Mata Kuliah"
            value="<?= htmlspecialchars($editData['nama'] ?? '') ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label">SKS *</label>
          <select class="form-control" name="sks" required>
            <?php for($i=1;$i<=6;$i++): ?>
              <option value="<?= $i ?>" <?= (($editData['sks']??0)==$i)?'selected':'' ?>><?= $i ?> SKS</option>
            <?php endfor; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Nama Dosen</label>
          <input class="form-control" name="dosen" placeholder="Nama Pengampu"
            value="<?= htmlspecialchars($editData['dosen'] ?? '') ?>">
        </div>
      </div>
      <div style="margin-top:20px;display:flex;gap:10px">
        <button class="btn btn-accent" type="submit">💾 Simpan</button>
        <a href="matakuliah.php" class="btn btn-outline">Batal</a>
      </div>
    </form>
  </div>
</div>

<?php else: ?>
<div class="card">
  <div class="card-header">
    <h3>📚 Daftar Mata Kuliah <span style="font-family:inherit;color:var(--text-muted);font-weight:400;font-size:13px">(<?= count($list) ?> MK)</span></h3>
    <a href="matakuliah.php?action=add" class="btn btn-accent">➕ Tambah MK</a>
  </div>
  <?php if (empty($list)): ?>
    <div class="empty-state"><div class="empty-icon">📚</div><p>Belum ada mata kuliah.</p></div>
  <?php else: ?>
  <div class="table-wrapper">
    <table class="data-table">
      <thead><tr><th>#</th><th>Kode</th><th>Nama MK</th><th>SKS</th><th>Dosen</th><th>Aksi</th></tr></thead>
      <tbody>
      <?php $no=1; foreach($list as $mk): ?>
        <tr>
          <td style="color:var(--text-muted)"><?= $no++ ?></td>
          <td><code style="background:rgba(6,182,212,.08);color:var(--accent-dark);padding:2px 8px;border-radius:6px;font-size:12px;font-weight:700"><?= $mk['kode'] ?></code></td>
          <td style="font-weight:600"><?= htmlspecialchars($mk['nama']) ?></td>
          <td>
            <span style="background:linear-gradient(135deg,var(--primary),var(--primary-light));color:#fff;padding:3px 12px;border-radius:20px;font-size:12px;font-weight:700">
              <?= $mk['sks'] ?> SKS
            </span>
          </td>
          <td style="color:var(--text-muted);font-size:13px"><?= htmlspecialchars($mk['dosen'] ?: '—') ?></td>
          <td style="display:flex;gap:6px">
            <a href="matakuliah.php?action=edit&kode=<?= urlencode($mk['kode']) ?>" class="btn btn-warning btn-sm">✏️ Edit</a>
            <form method="POST" style="display:inline" onsubmit="return confirm('Hapus mata kuliah ini?')">
              <input type="hidden" name="act" value="delete">
              <input type="hidden" name="kode" value="<?= $mk['kode'] ?>">
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
renderLayout('Manajemen Mata Kuliah', 'matakuliah', $content);
?>
