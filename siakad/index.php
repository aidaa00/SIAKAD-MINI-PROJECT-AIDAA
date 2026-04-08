<?php
// WAJIB: Letakkan session_start dan pengecekan di baris paling atas
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

require_once 'storage.php';
require_once 'layout.php';
initStorage();

// Ambil data user dari session untuk menyapa di dashboard
$user = $_SESSION['user'];

$mahasiswaList  = getMahasiswaList();
$mkList         = getMataKuliahList();
$dosenList      = getDosenList();
$nilaiAll       = getNilaiAll();

$totalNilai = 0;
foreach ($nilaiAll as $n) $totalNilai += count($n);

// Hitung rata-rata IPK semua mahasiswa
require_once 'classes/Mahasiswa.php';
require_once 'classes/MataKuliah.php';
$ipkList = [];
foreach ($mahasiswaList as $mhs) {
    $obj = new Mahasiswa($mhs['nim'],$mhs['nama'],$mhs['email'],$mhs['prodi']);
    foreach (getNilaiMahasiswa($mhs['nim']) as $kode => $nilai) {
        if (isset($mkList[$kode])) {
            $mk = new MataKuliah($mkList[$kode]['kode'],$mkList[$kode]['nama'],$mkList[$kode]['sks'],$mkList[$kode]['dosen']);
            $obj->inputNilai($mk, (float)$nilai);
        }
    }
    $ipkList[] = $obj->hitungIPK();
}
$avgIPK = count($ipkList) ? round(array_sum($ipkList)/count($ipkList),2) : 0;

ob_start(); ?>

<div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 20px; background: #fff; padding: 15px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
    <div style="color: var(--primary); font-weight: 600;">
        <?php 
        // Cek apakah $user itu array. Jika bukan, kita anggap dia string biasa.
        $namaUser = is_array($user) ? $user['nama'] : $user;
        $roleUser = is_array($user) && isset($user['role']) ? $user['role'] : 'User';
        ?>
        
        👋 Halo, <?= htmlspecialchars($namaUser) ?> 
        <span style="font-weight: normal; color: #666; font-size: 0.8em;">
            (Logged as: <?= ucfirst($roleUser) ?>)
        </span>
    </div>
    <a href="logout.php" style="color: #dc3545; text-decoration: none; font-weight: bold; font-size: 0.9em;">Keluar 🚪</a>
</div>

<div class="hero-banner">
  <h1>🎓 Selamat Datang di SIAKAD Mini</h1>
  <p>Sistem Informasi Akademik berbasis PHP OOP — kelola data mahasiswa, mata kuliah, nilai, dan cetak KHS dengan mudah.</p>
</div>

<div class="stats-grid">
  <div class="stat-card blue">
    <div class="stat-icon">👨‍🎓</div>
    <div class="stat-value"><?= count($mahasiswaList) ?></div>
    <div class="stat-label">Total Mahasiswa</div>
  </div>
  <div class="stat-card cyan">
    <div class="stat-icon">📚</div>
    <div class="stat-value"><?= count($mkList) ?></div>
    <div class="stat-label">Mata Kuliah</div>
  </div>
  <div class="stat-card green">
    <div class="stat-icon">📝</div>
    <div class="stat-value"><?= $totalNilai ?></div>
    <div class="stat-label">Data Nilai Masuk</div>
  </div>
  <div class="stat-card purple">
    <div class="stat-icon">⭐</div>
    <div class="stat-value"><?= $avgIPK ?></div>
    <div class="stat-label">Rata-rata IPK</div>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">

  <div class="card">
    <div class="card-header">
      <h3>👨‍🎓 Daftar Mahasiswa</h3>
      <a href="pages/mahasiswa.php" class="btn btn-primary btn-sm">Lihat Semua</a>
    </div>
    <div class="table-wrapper">
      <table class="data-table">
        <thead><tr><th>NIM</th><th>Nama</th><th>Prodi</th></tr></thead>
        <tbody>
        <?php foreach(array_slice($mahasiswaList,0,5) as $m): ?>
          <tr>
            <td><code style="background:rgba(26,86,219,.08);padding:2px 7px;border-radius:5px;font-size:12px"><?= $m['nim'] ?></code></td>
            <td><?= htmlspecialchars($m['nama']) ?></td>
            <td style="font-size:12px;color:var(--text-muted)"><?= htmlspecialchars($m['prodi']) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <h3>📚 Mata Kuliah</h3>
      <a href="pages/matakuliah.php" class="btn btn-accent btn-sm">Lihat Semua</a>
    </div>
    <div class="table-wrapper">
      <table class="data-table">
        <thead><tr><th>Kode</th><th>Nama MK</th><th>SKS</th></tr></thead>
        <tbody>
        <?php foreach($mkList as $mk): ?>
          <tr>
            <td><code style="background:rgba(6,182,212,.08);padding:2px 7px;border-radius:5px;font-size:12px"><?= $mk['kode'] ?></code></td>
            <td><?= htmlspecialchars($mk['nama']) ?></td>
            <td><span style="font-weight:700;color:var(--primary)"><?= $mk['sks'] ?></span></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<div class="card">
  <div class="card-header"><h3>⚡ Aksi Cepat</h3></div>
  <div class="card-body" style="display:flex;flex-wrap:wrap;gap:12px;">
    <?php if ($user['role'] === 'dosen'): ?>
        <a href="pages/mahasiswa.php?action=add" class="btn btn-primary">➕ Tambah Mahasiswa</a>
        <a href="pages/matakuliah.php?action=add" class="btn btn-accent">➕ Tambah Mata Kuliah</a>
        <a href="pages/nilai.php" class="btn btn-success">📝 Input Nilai</a>
        <a href="pages/dosen.php?action=add" class="btn btn-outline">➕ Tambah Dosen</a>
    <?php endif; ?>

    <a href="pages/khs.php" class="btn btn-info">📊 Lihat KHS & IPK</a>
    <a href="pages/laporan.php" class="btn btn-warning">🖨️ Cetak Laporan</a>
  </div>
</div>

<?php
$content = ob_get_clean();
renderLayout('Dashboard', 'dashboard', $content);
?>