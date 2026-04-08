<?php
class MataKuliah {
    private string $kode;
    private string $nama;
    private int    $sks;
    private string $dosen;

    public function __construct(string $kode, string $nama, int $sks, string $dosen = '') {
        $this->kode  = $kode;
        $this->nama  = $nama;
        $this->sks   = $sks;
        $this->dosen = $dosen;
    }

    public function getKode(): string  { return $this->kode; }
    public function getNama(): string  { return $this->nama; }
    public function getSks(): int      { return $this->sks; }
    public function getDosen(): string { return $this->dosen; }

    public function setNama(string $nama): void    { $this->nama  = $nama; }
    public function setSks(int $sks): void         { $this->sks   = $sks; }
    public function setDosen(string $dosen): void  { $this->dosen = $dosen; }

    public function toArray(): array {
        return [
            'kode'  => $this->kode,
            'nama'  => $this->nama,
            'sks'   => $this->sks,
            'dosen' => $this->dosen,
        ];
    }
}
?>
