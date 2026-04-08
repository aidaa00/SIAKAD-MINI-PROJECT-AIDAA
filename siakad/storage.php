<?php
// storage.php – simple session-based data store

// Tambahkan ini di storage.php
function login($username, $password) {
    // Contoh data statis (Bisa diganti dengan data dari JSON/Database)
    $users = [
        ['username' => 'admin', 'password' => 'admin123', 'role' => 'dosen', 'nama' => 'Miss. Eka Yuniar'],
        ['username' => 'mhs', 'password' => 'mhs123', 'role' => 'mahasiswa', 'nama' => 'Sari` Sadida']
    ];

    foreach ($users as $user) {
        if ($user['username'] === $username && $user['password'] === $password) {
            return $user;
        }
    }
    return false;
}

function initStorage(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['mahasiswa']))  $_SESSION['mahasiswa']  = [];
    if (!isset($_SESSION['matakuliah'])) $_SESSION['matakuliah'] = [];
    if (!isset($_SESSION['dosen']))      $_SESSION['dosen']      = [];
    if (!isset($_SESSION['nilai']))      $_SESSION['nilai']      = []; // nim => [kode => nilai]

    // Seed demo data once
    if (empty($_SESSION['mahasiswa'])) {
        $_SESSION['mahasiswa'] = [
            '2021001' => ['nim'=>'2021001','nama'=>'Andi Pratama','email'=>'andi@univ.ac.id','prodi'=>'Teknik Informatika'],
            '2021002' => ['nim'=>'2021002','nama'=>'Siti Rahayu','email'=>'siti@univ.ac.id','prodi'=>'Sistem Informasi'],
            '2021003' => ['nim'=>'2021003','nama'=>'Budi Santoso','email'=>'budi@univ.ac.id','prodi'=>'Teknik Informatika'],
        ];
        $_SESSION['matakuliah'] = [
            'TI101' => ['kode'=>'TI101','nama'=>'Pemrograman Web','sks'=>3,'dosen'=>'Dr. Hendra'],
            'TI102' => ['kode'=>'TI102','nama'=>'Basis Data','sks'=>3,'dosen'=>'Dr. Wulan'],
            'TI103' => ['kode'=>'TI103','nama'=>'Algoritma & Pemrograman','sks'=>4,'dosen'=>'Dr. Rizal'],
            'TI104' => ['kode'=>'TI104','nama'=>'Jaringan Komputer','sks'=>2,'dosen'=>'Dr. Hendra'],
        ];
        $_SESSION['dosen'] = [
            '0001' => ['nidn'=>'0001','nama'=>'Dr. Hendra Kusuma','email'=>'hendra@univ.ac.id','mk'=>'Pemrograman Web, Jaringan Komputer'],
            '0002' => ['nidn'=>'0002','nama'=>'Dr. Wulan Sari','email'=>'wulan@univ.ac.id','mk'=>'Basis Data'],
            '0003' => ['nidn'=>'0003','nama'=>'Dr. Rizal Fauzi','email'=>'rizal@univ.ac.id','mk'=>'Algoritma & Pemrograman'],
        ];
        $_SESSION['nilai'] = [
            '2021001' => ['TI101'=>88,'TI102'=>76,'TI103'=>92,'TI104'=>70],
            '2021002' => ['TI101'=>65,'TI102'=>80,'TI103'=>74],
            '2021003' => ['TI101'=>55,'TI102'=>60],
        ];
    }
}

function getMahasiswaList(): array  { return $_SESSION['mahasiswa']  ?? []; }
function getMataKuliahList(): array { return $_SESSION['matakuliah'] ?? []; }
function getDosenList(): array      { return $_SESSION['dosen']      ?? []; }
function getNilaiAll(): array       { return $_SESSION['nilai']      ?? []; }
function getNilaiMahasiswa(string $nim): array { return $_SESSION['nilai'][$nim] ?? []; }

function saveMahasiswa(array $data): void {
    $_SESSION['mahasiswa'][$data['nim']] = $data;
}
function deleteMahasiswa(string $nim): void {
    unset($_SESSION['mahasiswa'][$nim], $_SESSION['nilai'][$nim]);
}

function saveMataKuliah(array $data): void {
    $_SESSION['matakuliah'][$data['kode']] = $data;
}
function deleteMataKuliah(string $kode): void {
    unset($_SESSION['matakuliah'][$kode]);
}

function saveDosen(array $data): void {
    $_SESSION['dosen'][$data['nidn']] = $data;
}
function deleteDosen(string $nidn): void {
    unset($_SESSION['dosen'][$nidn]);
}

function saveNilai(string $nim, string $kode, float $nilai): void {
    if (!isset($_SESSION['nilai'][$nim])) $_SESSION['nilai'][$nim] = [];
    $_SESSION['nilai'][$nim][$kode] = $nilai;
}
function deleteNilai(string $nim, string $kode): void {
    unset($_SESSION['nilai'][$nim][$kode]);
}
?>
