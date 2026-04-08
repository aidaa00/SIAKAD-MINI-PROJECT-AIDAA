<?php
require_once 'User.php';

class Dosen extends User {
    private string $nidn;
    private string $mataKuliahAmpu;

    public function __construct(string $nidn, string $nama, string $email, string $mataKuliahAmpu) {
        parent::__construct($nidn, $nama, $email);
        $this->nidn           = $nidn;
        $this->mataKuliahAmpu = $mataKuliahAmpu;
    }

    public function getNidn(): string             { return $this->nidn; }
    public function getMataKuliahAmpu(): string    { return $this->mataKuliahAmpu; }
    public function setMataKuliahAmpu(string $mk): void { $this->mataKuliahAmpu = $mk; }
    public function getRole(): string             { return 'Dosen'; }

    public function cetakLaporan(): string {
        return "
        <div class='khs-container'>
            <div class='khs-header'>
                <div class='khs-logo'>👨‍🏫</div>
                <div class='khs-title'>
                    <h2>PROFIL DOSEN</h2>
                    <p>Data Pengajar – SIAKAD Mini</p>
                </div>
            </div>
            <div class='khs-info'>
                <div class='info-grid'>
                    <div class='info-item'><span class='info-label'>NIDN</span><span class='info-value'>{$this->nidn}</span></div>
                    <div class='info-item'><span class='info-label'>Nama</span><span class='info-value'>{$this->nama}</span></div>
                    <div class='info-item'><span class='info-label'>Email</span><span class='info-value'>{$this->email}</span></div>
                    <div class='info-item'><span class='info-label'>Mata Kuliah Ampu</span><span class='info-value'>{$this->mataKuliahAmpu}</span></div>
                </div>
            </div>
        </div>";
    }
}
?>
