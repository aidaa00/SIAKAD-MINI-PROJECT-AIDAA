<?php
require_once '../storage.php';
require_once '../layout.php';
require_once '../classes/Mahasiswa.php';
require_once '../classes/MataKuliah.php';
initStorage();

$msg         = '';
$mahasiswaList = getMahasiswaList();
$mkList        = getMataKuliahList();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act   = $_POST['act'] ?? '';
    $nim   = $_POST['nim']  ?? '';
    $kode  = $_POST['kode'] ?? '';
    $nilai = (float)($_POST['nilai'] ?? -1);

    if ($act === 'save') {
        if ($nim && $kode && $nilai >= 0 && $nilai <= 100) {
            saveNilai($nim, $kode, $nilai);
            $msg = "<div class='alert alert-success'>✅ Nilai berhasil disimpan!</div>";
        } else {
            $msg = "<div class='alert alert-danger'>❌ Pilih mahasiswa, mata kuliah, dan isi nilai (0–100)!</div>";
        }
    }
    if ($act === 'delete') {
        deleteNilai($nim, $kode);
        $msg = "<div class='alert alert-info'>🗑️ Nilai berhasil dihapus.</div>";
    }
}

$nilaiAll = getNilaiAll();

// Build object helper
function buildMhsObj(array $mhs, array $mkList, array $nilaiAll): Mahasiswa {
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

<?= $msg ?>

<div style="display:grid;grid-template-columns:340px 1fr;gap:24px;align-items:start">

<!-- Form Input -->
<div class="card" style="position:sticky;top:90px">
  <div class="card-header"><h3>📝 Input / Update Nilai</h3></div>
  <div class="card-body">
    <form method="POST">
      <input type="hidden" name="act" value="save">
      <div class="form-group" style="margin-bottom:14px">
        <label class="form-label">Mahasiswa *</label>
        <select class="form-control" name="nim" required>
          <option value="">-- Pilih Mahasiswa --</option>
          <?php foreach ($mahasiswaList as $m): ?>
            <option value="<?= $m['nim'] ?>"><?= $m['nim'] ?> – <?= htmlspecialchars($m['nama']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group" style="margin-bottom:14px">
        <label class="form-label">Mata Kuliah *</label>
        <select class="form-control" name="kode" required>
          <option value="">-- Pilih Mata Kuliah --</option>
          <?php foreach ($mkList as $mk): ?>
            <option value="<?= $mk['kode'] ?>"><?= $mk['kode'] ?> – <?= htmlspecialchars($mk['nama']) ?> (<?= $mk['sks'] ?> SKS)</option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group" style="margin-bottom:20px">
        <label class="form-label">Nilai (0 – 100) *</label>
        <input class="form-control" type="number" name="nilai" min="0" max="100" step="0.5" placeholder="Masukkan nilai angka" required>
      </div>
      <button class="btn btn-success" type="submit" style="width:100%">💾 Simpan Nilai</button>
    </form>

    <hr style="margin:20px 0;border-color:var(--border)">
    <div style="background:rgba(26,86,219,.05);border-radius:10px;padding:14px;font-size:12px">
      <div style="font-weight:700;margin-bottom:8px;color:var(--primary)">📊 Konversi Nilai</div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:4px;color:var(--text-muted)">
        <span>85–100 → A (4.0)</span><span>75–84 → A- (3.5)</span>
        <span>70–74 → B+ (3.0)</span><span>65–69 → B (2.5)</span>
        <span>60–64 → B- (2.0)</span><span>55–59 → C+ (1.5)</span>
        <span>50–54 → C (1.0)</span><span>40–49 → D (0.5)</span>
        <span>0–39  → E (0.0)</span>
      </div>
    </div>
  </div>
</div>

<!-- Tabel Nilai per Mahasiswa -->
<div>
  <?php foreach ($mahasiswaList as $mhs): ?>
    <?php
    $nilaiMhs = getNilaiMahasiswa($mhs['nim']);
    $obj      = buildMhsObj($mhs, $mkList, $nilaiAll);
    $ipk      = $obj->hitungIPK();
    ?>
    <div class="card" style="margin-bottom:16px">
      <div class="card-header">
        <h3>
          <span style="font-size:13px;background:rgba(26,86,219,.1);color:var(--primary);padding:2px 8px;border-radius:6px"><?= $mhs['nim'] ?></span>
          <?= htmlspecialchars($mhs['nama']) ?>
        </h3>
        <span style="font-weight:700;font-family:'Syne',sans-serif;color:var(--primary)">IPK: <?= $ipk ?></span>
      </div>
      <?php if (empty($nilaiMhs)): ?>
        <div class="empty-state" style="padding:24px"><p>Belum ada nilai untuk mahasiswa ini.</p></div>
      <?php else: ?>
      <div class="table-wrapper">
        <table class="data-table">
          <thead><tr><th>Kode</th><th>Mata Kuliah</th><th>SKS</th><th>Nilai</th><th>Huruf</th><th>Bobot</th><th>Aksi</th></tr></thead>
          <tbody>
          <?php foreach ($nilaiMhs as $kode => $n):
            if (!isset($mkList[$kode])) continue;
            $mk   = $mkList[$kode];
            $mObj = new Mahasiswa('x','x','x','x');
            $huruf = $mObj->nilaiToHuruf((float)$n);
            $bobot = $mObj->nilaiToBobot((float)$n);
          ?>
            <tr>
              <td><code style="font-size:12px;background:rgba(6,182,212,.08);color:var(--accent-dark);padding:2px 7px;border-radius:5px"><?= $kode ?></code></td>
              <td style="font-weight:500"><?= htmlspecialchars($mk['nama']) ?></td>
              <td><?= $mk['sks'] ?></td>
              <td style="font-weight:700;color:var(--primary)"><?= $n ?></td>
              <td><span class="badge badge-<?= $huruf ?>"><?= $huruf ?></span></td>
              <td style="font-weight:600"><?= $bobot ?></td>
              <td>
                <form method="POST" style="display:inline" onsubmit="return confirm('Hapus nilai ini?')">
                  <input type="hidden" name="act" value="delete">
                  <input type="hidden" name="nim" value="<?= $mhs['nim'] ?>">
                  <input type="hidden" name="kode" value="<?= $kode ?>">
                  <button class="btn btn-danger btn-sm" type="submit">🗑️</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
</div>

</div>

<?php
$content = ob_get_clean();
renderLayout('Input Nilai', 'nilai', $content);
?>
