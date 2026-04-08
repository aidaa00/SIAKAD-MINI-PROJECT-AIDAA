<?php
require_once '../storage.php';
require_once '../layout.php';
require_once '../classes/Mahasiswa.php';
require_once '../classes/Dosen.php';
require_once '../classes/MataKuliah.php';
initStorage();

$mahasiswaList = getMahasiswaList();
$mkList        = getMataKuliahList();
$dosenList     = getDosenList();
$nilaiAll      = getNilaiAll();
$selectedNim   = $_GET['nim'] ?? '';
$type          = $_GET['type'] ?? 'mahasiswa';

function buildMhs2(array $mhs, array $mkList, array $nilaiAll): Mahasiswa {
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

<div class="card" style="margin-bottom:20px">
  <div class="card-header">
    <h3>🖨️ Cetak Laporan</h3>
    <div style="display:flex;gap:10px">
      <a href="laporan.php?type=mahasiswa" class="btn <?= $type==='mahasiswa'?'btn-primary':'btn-outline' ?> btn-sm">👨‍🎓 Laporan Mahasiswa</a>
      <a href="laporan.php?type=dosen"     class="btn <?= $type==='dosen'    ?'btn-info'   :'btn-outline' ?> btn-sm">👨‍🏫 Laporan Dosen</a>
    </div>
  </div>
</div>

<?php if ($type === 'mahasiswa'): ?>

<div style="display:grid;grid-template-columns:260px 1fr;gap:20px;align-items:start">
  <!-- Pilih mahasiswa -->
  <div class="card" style="position:sticky;top:90px">
    <div class="card-header"><h3>Pilih Mahasiswa</h3></div>
    <div style="padding:8px">
      <?php foreach ($mahasiswaList as $mhs): ?>
      <a href="laporan.php?type=mahasiswa&nim=<?= urlencode($mhs['nim']) ?>"
         style="display:block;padding:11px 14px;border-radius:10px;text-decoration:none;font-size:14px;font-weight:500;margin-bottom:2px;transition:.2s;
                color:<?= ($selectedNim===$mhs['nim'])?'var(--primary)':'var(--text-main)' ?>;
                background:<?= ($selectedNim===$mhs['nim'])?'rgba(26,86,219,.08)':'transparent' ?>;
                border-left:3px solid <?= ($selectedNim===$mhs['nim'])?'var(--primary)':'transparent' ?>">
        <?= htmlspecialchars($mhs['nama']) ?>
        <span style="display:block;font-size:12px;color:var(--text-muted)"><?= $mhs['nim'] ?></span>
      </a>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Output KHS -->
  <div id="print-area">
    <?php if ($selectedNim && isset($mahasiswaList[$selectedNim])): ?>
      <?php
      $mhs = $mahasiswaList[$selectedNim];
      $obj = buildMhs2($mhs, $mkList, $nilaiAll);
      echo $obj->cetakLaporan(); // Interface CetakLaporan – Polymorphism
      ?>
      <div style="margin-top:16px;display:flex;gap:10px" class="no-print">
        <button class="btn btn-primary" onclick="window.print()">🖨️ Print / Save PDF</button>
        <a href="khs.php?nim=<?= urlencode($selectedNim) ?>" class="btn btn-outline">← KHS</a>
      </div>
    <?php else: ?>
      <div class="empty-state card" style="padding:60px">
        <div class="empty-icon">🖨️</div>
        <p>Pilih mahasiswa di sebelah kiri untuk melihat laporannya.</p>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php else: // Laporan Dosen ?>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:20px">
  <?php foreach ($dosenList as $d):
    $dosenObj = new Dosen($d['nidn'],$d['nama'],$d['email'],$d['mk']);
    // Polymorphism: cetakLaporan() dipanggil pada class Dosen
    echo $dosenObj->cetakLaporan();
  endforeach; ?>
  <?php if (empty($dosenList)): ?>
    <div class="empty-state card" style="padding:60px;grid-column:1/-1">
      <div class="empty-icon">👨‍🏫</div><p>Belum ada data dosen.</p>
    </div>
  <?php endif; ?>
</div>
<div class="no-print" style="margin-top:16px">
  <button class="btn btn-info" onclick="window.print()">🖨️ Print Semua Laporan Dosen</button>
</div>

<?php endif; ?>

<style>
@media print {
  .sidebar, .topbar, .no-print { display:none !important; }
  .main-content { margin-left:0 !important; }
  .page-wrapper { padding:0 !important; }
  body { background:#fff !important; }
}
</style>

<?php
$content = ob_get_clean();
renderLayout('Cetak Laporan', 'laporan', $content);
?>
