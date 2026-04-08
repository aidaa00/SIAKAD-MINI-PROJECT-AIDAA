<?php
require_once '../storage.php';
require_once '../layout.php';
require_once '../classes/Mahasiswa.php';
initStorage();

$msg = '';
$action = $_GET['action'] ?? '';
$editData = null;

// Proses Form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act = $_POST['act'] ?? '';

    if ($act === 'add' || $act === 'edit') {
        $nim   = trim($_POST['nim']   ?? '');
        $nama  = trim($_POST['nama']  ?? '');
        $email = trim($_POST['email'] ?? '');
        $prodi = trim($_POST['prodi'] ?? '');
        if ($nim && $nama && $email && $prodi) {
            saveMahasiswa(['nim'=>$nim,'nama'=>$nama,'email'=>$email,'prodi'=>$prodi]);
            $msg = "<div class='alert alert-success'>✅ Data mahasiswa berhasil " . ($act==='add'?'ditambahkan':'diperbarui') . "!</div>";
            $action = '';
        } else {
            $msg = "<div class='alert alert-danger'>❌ Semua field wajib diisi!</div>";
        }
    }
    if ($act === 'delete') {
        $nim = $_POST['nim'] ?? '';
        if ($nim) { deleteMahasiswa($nim); $msg = "<div class='alert alert-info'>🗑️ Mahasiswa berhasil dihapus.</div>"; }
    }
}

if ($action === 'edit') {
    $nim = $_GET['nim'] ?? '';
    $list = getMahasiswaList();
    $editData = $list[$nim] ?? null;
}

$list = getMahasiswaList();

ob_start(); ?>

<?= $msg ?>

<?php if ($action === 'add' || $action === 'edit'): ?>
<div class="card" style="max-width:640px">
  <div class="card-header">
    <h3><?= $action==='add' ? '➕ Tambah Mahasiswa Baru' : '✏️ Edit Mahasiswa' ?></h3>
    <a href="mahasiswa.php" class="btn btn-sm btn-outline">← Kembali</a>
  </div>
  <div class="card-body">
    <form method="POST">
      <input type="hidden" name="act" value="<?= $action ?>">
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label">NIM *</label>
          <input class="form-control" name="nim" placeholder="Contoh: 2024001"
            value="<?= htmlspecialchars($editData['nim'] ?? '') ?>"
            <?= $action==='edit' ? 'readonly style="background:#eef2ff"' : '' ?> required>
        </div>
        <div class="form-group">
          <label class="form-label">Nama Lengkap *</label>
          <input class="form-control" name="nama" placeholder="Nama Mahasiswa"
            value="<?= htmlspecialchars($editData['nama'] ?? '') ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label">Email *</label>
          <input class="form-control" type="email" name="email" placeholder="email@univ.ac.id"
            value="<?= htmlspecialchars($editData['email'] ?? '') ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label">Program Studi *</label>
          <select class="form-control" name="prodi" required>
            <option value="">-- Pilih Prodi --</option>
            <?php foreach(['Teknik Informatika','Sistem Informasi','Manajemen Informatika','Ilmu Komputer'] as $p): ?>
              <option value="<?= $p ?>" <?= (($editData['prodi']??'') === $p)?'selected':'' ?>><?= $p ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div style="margin-top:20px;display:flex;gap:10px">
        <button class="btn btn-primary" type="submit">💾 Simpan</button>
        <a href="mahasiswa.php" class="btn btn-outline">Batal</a>
      </div>
    </form>
  </div>
</div>

<?php else: ?>

<div class="card">
  <div class="card-header">
    <h3>👨‍🎓 Daftar Mahasiswa <span style="font-family:inherit;color:var(--text-muted);font-weight:400;font-size:13px">(<?= count($list) ?> orang)</span></h3>
    <a href="mahasiswa.php?action=add" class="btn btn-primary">➕ Tambah Mahasiswa</a>
  </div>
  <?php if (empty($list)): ?>
    <div class="empty-state"><div class="empty-icon">👨‍🎓</div><p>Belum ada data mahasiswa. Tambah sekarang!</p></div>
  <?php else: ?>
  <div class="table-wrapper">
    <table class="data-table">
      <thead><tr><th>#</th><th>NIM</th><th>Nama</th><th>Email</th><th>Program Studi</th><th>Aksi</th></tr></thead>
      <tbody>
      <?php $no=1; foreach($list as $m): ?>
        <tr>
          <td style="color:var(--text-muted)"><?= $no++ ?></td>
          <td><code style="background:rgba(26,86,219,.08);padding:2px 8px;border-radius:6px;font-size:12px"><?= $m['nim'] ?></code></td>
          <td style="font-weight:600"><?= htmlspecialchars($m['nama']) ?></td>
          <td style="color:var(--text-muted);font-size:13px"><?= htmlspecialchars($m['email']) ?></td>
          <td>
            <span style="background:rgba(26,86,219,.08);color:var(--primary);padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600">
              <?= htmlspecialchars($m['prodi']) ?>
            </span>
          </td>
          <td style="display:flex;gap:6px;flex-wrap:wrap">
            <a href="mahasiswa.php?action=edit&nim=<?= urlencode($m['nim']) ?>" class="btn btn-warning btn-sm">✏️ Edit</a>
            <form method="POST" style="display:inline" onsubmit="return confirm('Hapus mahasiswa ini?')">
              <input type="hidden" name="act" value="delete">
              <input type="hidden" name="nim" value="<?= $m['nim'] ?>">
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
renderLayout('Manajemen Mahasiswa', 'mahasiswa', $content);
?>
