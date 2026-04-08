<?php
function renderLayout(string $title, string $activePage, string $content): void { ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title) ?> – SIAKAD Mini</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
  <div class="sidebar-brand">
    <a href="index.php" class="logo">
      <div class="logo-icon">🎓</div>
      <div class="logo-text">SIAKAD Mini<span>Sistem Akademik PHP OOP</span></div>
    </a>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-section-label">Menu Utama</div>
    <a href="index.php" class="<?= $activePage==='dashboard'?'active':'' ?>">
      <span class="nav-icon">🏠</span> Dashboard
    </a>

    <div class="nav-section-label">Manajemen</div>
    <a href="pages/mahasiswa.php" class="<?= $activePage==='mahasiswa'?'active':'' ?>">
      <span class="nav-icon">👨‍🎓</span> Mahasiswa
    </a>
    <a href="pages/matakuliah.php" class="<?= $activePage==='matakuliah'?'active':'' ?>">
      <span class="nav-icon">📚</span> Mata Kuliah
    </a>
    <a href="pages/dosen.php" class="<?= $activePage==='dosen'?'active':'' ?>">
      <span class="nav-icon">👨‍🏫</span> Dosen
    </a>

    <div class="nav-section-label">Akademik</div>
    <a href="pages/nilai.php" class="<?= $activePage==='nilai'?'active':'' ?>">
      <span class="nav-icon">📝</span> Input Nilai
    </a>
    <a href="pages/khs.php" class="<?= $activePage==='khs'?'active':'' ?>">
      <span class="nav-icon">📊</span> KHS & IPK
    </a>

    <div class="nav-section-label">Laporan</div>
    <a href="pages/laporan.php" class="<?= $activePage==='laporan'?'active':'' ?>">
      <span class="nav-icon">🖨️</span> Cetak Laporan
    </a>
  </nav>

  <div class="sidebar-footer">
    &copy; <?= date('Y') ?> SIAKAD Mini &mdash; PHP OOP
  </div>
</aside>

<!-- MAIN CONTENT -->
<div class="main-content">
  <header class="topbar">
    <div class="topbar-title"><?= htmlspecialchars($title) ?></div>
    <div class="topbar-right">
      <span style="font-size:13px;color:var(--text-muted);">📅 <?= date('d M Y') ?></span>
      <span class="badge-admin">👤 Admin</span>
    </div>
  </header>

  <main class="page-wrapper">
    <?= $content ?>
  </main>
</div>

</body>
</html>
<?php }
?>
