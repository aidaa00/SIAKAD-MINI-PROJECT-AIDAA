<?php
require_once 'CetakLaporan.php';

abstract class User implements CetakLaporan {
    protected string $id;
    protected string $nama;
    protected string $email;

    public function __construct(string $id, string $nama, string $email) {
        $this->id    = $id;
        $this->nama  = $nama;
        $this->email = $email;
    }

    public function getId(): string   { return $this->id; }
    public function getNama(): string  { return $this->nama; }
    public function getEmail(): string { return $this->email; }

    public function setNama(string $nama): void   { $this->nama  = $nama; }
    public function setEmail(string $email): void { $this->email = $email; }

    abstract public function getRole(): string;
    abstract public function cetakLaporan(): string;
}
?>
