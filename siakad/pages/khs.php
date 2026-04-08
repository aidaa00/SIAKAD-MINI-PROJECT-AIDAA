<?php
require_once '../storage.php';
require_once '../layout.php';
require_once '../classes/Mahasiswa.php';
require_once '../classes/MataKuliah.php';
initStorage();

$mahasiswaList = getMahasiswaList();
$mkList        = getMataKuliahList();
$nilaiAll      = getNilaiAll();

$selectedNim = $_GET['nim'] ?? '';

function buildMhs(array $mhs, array $mkList, array $nilaiAll): Mahasiswa {
    $obj = new Mahasiswa($mhs['nim'],$mhs['nama'],$mhs['email'],$mhs['prodi']);
    foreach ($nilaiAll[$mhs['nim']] ?? [] as $kode => $n) {
        if (isset($mkList[$kode])) {
            $mk = new MataKuliah($mkList[$kode]['kode'],$mkList[$kode]['nama'],$mkList[$kode]['sks'],$mkList[$kode]['dosen']);
            $obj->inputNilai($mk,(float)$n);
        }
    }
    return $obj;
}

ob_start(); ?>

<div style="display:grid;grid-template-columns:280px 1fr;gap:24px;align-items:start">

<!-- Sidebar daftar mahasiswa -->
<div class="card" style="position:sticky;top:90px">
  <div class="card-header"><h3>👨‍🎓 Pilih Mahasiswa</h3></div>
  <div style="padding:8px">
    <?php foreach ($mahasiswaList as $mhs):
      $obj = buildMhs($mhs, $mkList, $nilaiAll);
      $ipk = $obj->hitungIPK();
      $active = ($selectedNim === $mhs['nim']);
    ?>
    <a href="khs.php?nim=<?= urlencode($mhs['nim']) ?>"
       style="display:flex;align-items:center;justify-content:space-between;padding:12px 14px;border-radius:10px;text-decoration:none;transition:.2s;margin-bottom:2px;
              background:<?= $active ? 'linear-gradient(90deg,rgba(26,86,219,.12),rgba(59,130,246,.06))' : 'transparent' ?>;
              border-left:<?= $active ? '3px solid var(--primary)' : '3px solid transparent' ?>">
      <div>
        <div style="font-weight:600;font-size:14px;color:var(--text-main)"><?= htmlspecialchars($mhs['nama']) ?></div>
        <div style="font-size:12px;color:var(--text-muted)"><?= $mhs['nim'] ?></div>
      </div>
      <span style="font-family:'Syne',sans-serif;font-weight:800;font-size:16px;color:<?= $active ? 'var(--primary)' : 'var(--text-muted)' ?>">
        <?= $ipk ?>
      </span>
    </a>
    <?php endforeach; ?>
  </div>
</div>

<!-- KHS Display -->
<div>
  <?php if ($selectedNim && isset($mahasiswaList[$selectedNim])): ?>
    <?php
    $mhs = $mahasiswaList[$selectedNim];
    $obj = buildMhs($mhs, $mkList, $nilaiAll);
    echo $obj->cetakLaporan(); // Polymorphism - CetakLaporan interface
    ?>
    <div style="margin-top:16px;display:flex;gap:10px">
      <a href="laporan.php?nim=<?= urlencode($selectedNim) ?>" class="btn btn-primary">🖨️ Cetak Laporan</a>
      <a href="nilai.php" class="btn btn-accent">📝 Input Nilai</a>
    </div>

  <?php elseif (!empty($mahasiswaList)): ?>
    <!-- Overview semua mahasiswa -->
    <div class="card">
      <div class="card-header"><h3>📊 Rekap IPK Semua Mahasiswa</h3></div>
      <div class="table-wrapper">
        <table class="data-table">
          <thead>
            <tr><th>#</th><th>NIM</th><th>Nama</th><th>Prodi</th><th>Jumlah MK</th><th>Total SKS</th><th>IPK</th><th>Predikat</th></tr>
          </thead>
          <tbody>
          <?php $no=1; foreach ($mahasiswaList as $mhs):
            $obj  = buildMhs($mhs, $mkList, $nilaiAll);
            $ipk  = $obj->hitungIPK();
            $jmlMK = count($nilaiAll[$mhs['nim']] ?? []);
            $totalSks = 0;
            foreach ($nilaiAll[$mhs['nim']] ?? [] as $kode => $n) {
                if (isset($mkList[$kode])) $totalSks += $mkList[$kode]['sks'];
            }
            if ($ipk >= 3.51)     { $pred='Cumlaude';         $color='#065f46'; $bg='rgba(16,185,129,.12)'; }
            elseif ($ipk >= 3.01) { $pred='Sangat Memuaskan'; $color='#1e3a8a'; $bg='rgba(26,86,219,.12)'; }
            elseif ($ipk >= 2.51) { $pred='Memuaskan';        $color='#0e7490'; $bg='rgba(6,182,212,.12)'; }
            elseif ($ipk >= 2.00) { $pred='Cukup';            $color='#92400e'; $bg='rgba(245,158,11,.12)';}
            else                  { $pred='Perlu Peningkatan';$color='#991b1b'; $bg='rgba(239,68,68,.12)'; }
          ?>
            <tr>
              <td style="color:var(--text-muted)"><?= $no++ ?></td>
              <td><code style="font-size:12px;background:rgba(26,86,219,.08);color:var(--primary);padding:2px 7px;border-radius:5px"><?= $mhs['nim'] ?></code></td>
              <td style="font-weight:600"><a href="khs.php?nim=<?= urlencode($mhs['nim']) ?>" style="color:var(--primary);text-decoration:none"><?= htmlspecialchars($mhs['nama']) ?></a></td>
              <td style="font-size:13px;color:var(--text-muted)"><?= htmlspecialchars($mhs['prodi']) ?></td>
              <td style="text-align:center"><?= $jmlMK ?></td>
              <td style="text-align:center"><?= $totalSks ?></td>
              <td><span style="font-family:'Syne',sans-serif;font-weight:800;font-size:20px;color:var(--primary)"><?= $ipk ?></span></td>
              <td><span style="background:<?= $bg ?>;color:<?= $color ?>;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700"><?= $pred ?></span></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php else: ?>
    <div class="empty-state card" style="padding:60px">
      <div class="empty-icon">📊</div>
      <p>Belum ada data mahasiswa.</p>
    </div>
  <?php endif; ?>
</div>

</div>

<?php
$content = ob_get_clean();
renderLayout('KHS & IPK', 'khs', $content);
?>
