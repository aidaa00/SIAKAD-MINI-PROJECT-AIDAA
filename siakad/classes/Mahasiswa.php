<?php
require_once 'User.php';

class Mahasiswa extends User {
    private string $nim;
    private string $prodi;
    private array  $nilai = [];   // ['kode_mk' => ['mk' => MataKuliah, 'nilai' => float]]

    public function __construct(string $nim, string $nama, string $email, string $prodi) {
        parent::__construct($nim, $nama, $email);
        $this->nim   = $nim;
        $this->prodi = $prodi;
    }

    public function getNim(): string    { return $this->nim; }
    public function getProdi(): string  { return $this->prodi; }
    public function getRole(): string   { return 'Mahasiswa'; }
    public function getNilaiAll(): array { return $this->nilai; }

    public function inputNilai(MataKuliah $mk, float $nilai): void {
        $this->nilai[$mk->getKode()] = ['mk' => $mk, 'nilai' => $nilai];
    }

    public function hitungIPK(): float {
        if (empty($this->nilai)) return 0.0;
        $totalBobot = 0;
        $totalSks   = 0;
        foreach ($this->nilai as $entry) {
            $bobot       = $this->nilaiToBobot($entry['nilai']);
            $sks         = $entry['mk']->getSks();
            $totalBobot += $bobot * $sks;
            $totalSks   += $sks;
        }
        return $totalSks > 0 ? round($totalBobot / $totalSks, 2) : 0.0;
    }

    public function nilaiToBobot(float $nilai): float {
        if ($nilai >= 85) return 4.0;
        if ($nilai >= 75) return 3.5;   // A-
        if ($nilai >= 70) return 3.0;   // B+
        if ($nilai >= 65) return 2.5;   // B
        if ($nilai >= 60) return 2.0;   // B-
        if ($nilai >= 55) return 1.5;   // C+
        if ($nilai >= 50) return 1.0;   // C
        if ($nilai >= 40) return 0.5;   // D
        return 0.0;                     // E
    }

    public function nilaiToHuruf(float $nilai): string {
        if ($nilai >= 85) return 'A';
        if ($nilai >= 75) return 'A-';
        if ($nilai >= 70) return 'B+';
        if ($nilai >= 65) return 'B';
        if ($nilai >= 60) return 'B-';
        if ($nilai >= 55) return 'C+';
        if ($nilai >= 50) return 'C';
        if ($nilai >= 40) return 'D';
        return 'E';
    }

    public function cetakLaporan(): string {
        $ipk   = $this->hitungIPK();
        $rows  = '';
        $no    = 1;
        foreach ($this->nilai as $entry) {
            $huruf = $this->nilaiToHuruf($entry['nilai']);
            $bobot = $this->nilaiToBobot($entry['nilai']);
            $rows .= "<tr>
                        <td>{$no}</td>
                        <td>{$entry['mk']->getKode()}</td>
                        <td>{$entry['mk']->getNama()}</td>
                        <td>{$entry['mk']->getSks()}</td>
                        <td>{$entry['nilai']}</td>
                        <td><span class='badge badge-{$huruf}'>{$huruf}</span></td>
                        <td>{$bobot}</td>
                      </tr>";
            $no++;
        }
        return "
        <div class='khs-container'>
            <div class='khs-header'>
                <div class='khs-logo'>🎓</div>
                <div class='khs-title'>
                    <h2>KARTU HASIL STUDI (KHS)</h2>
                    <p>Semester Genap – Tahun Akademik 2024/2025</p>
                </div>
            </div>
            <div class='khs-info'>
                <div class='info-grid'>
                    <div class='info-item'><span class='info-label'>NIM</span><span class='info-value'>{$this->nim}</span></div>
                    <div class='info-item'><span class='info-label'>Nama</span><span class='info-value'>{$this->nama}</span></div>
                    <div class='info-item'><span class='info-label'>Program Studi</span><span class='info-value'>{$this->prodi}</span></div>
                    <div class='info-item'><span class='info-label'>Email</span><span class='info-value'>{$this->email}</span></div>
                </div>
            </div>
            <table class='khs-table'>
                <thead>
                    <tr>
                        <th>No</th><th>Kode MK</th><th>Mata Kuliah</th>
                        <th>SKS</th><th>Nilai</th><th>Huruf</th><th>Bobot</th>
                    </tr>
                </thead>
                <tbody>{$rows}</tbody>
            </table>
            <div class='khs-footer'>
                <div class='ipk-display'>
                    <span class='ipk-label'>IPK</span>
                    <span class='ipk-value'>{$ipk}</span>
                </div>
                <div class='ipk-predikat'>" . $this->predikatIPK($ipk) . "</div>
            </div>
        </div>";
    }

    private function predikatIPK(float $ipk): string {
        if ($ipk >= 3.51) return '🏆 Dengan Pujian (Cumlaude)';
        if ($ipk >= 3.01) return '⭐ Sangat Memuaskan';
        if ($ipk >= 2.51) return '✅ Memuaskan';
        if ($ipk >= 2.00) return '📘 Cukup';
        return '⚠️ Perlu Peningkatan';
    }
}
?>
