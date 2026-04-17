<?php
include 'koneksi.php';

// Ambil data siswa
$data = mysqli_query($conn, "SELECT * FROM siswa");
$siswa = mysqli_fetch_all($data, MYSQLI_ASSOC);

// Ambil bobot
$krit = mysqli_query($conn, "SELECT * FROM kriteria ORDER BY id");
$k = mysqli_fetch_all($krit, MYSQLI_ASSOC);

// Bobot
$w1 = $k[0]['bobot'];
$w2 = $k[1]['bobot'];
$w3 = $k[2]['bobot'];
$w4 = $k[3]['bobot'];

// Max & Min
$max1 = max(array_column($siswa, 'kehadiran'));
$min2 = min(array_column($siswa, 'terlambat'));
$min3 = min(array_column($siswa, 'pelanggaran'));
$max4 = max(array_column($siswa, 'sikap'));

$hasil = [];

foreach ($siswa as $s) {

    // Normalisasi
    $r1 = $s['kehadiran'] / $max1;
    $r2 = ($s['terlambat'] == 0) ? 1 : $min2 / $s['terlambat'];
    $r3 = ($s['pelanggaran'] == 0) ? 1 : $min3 / $s['pelanggaran'];
    $r4 = $s['sikap'] / $max4;

    // Hitung skor
    $skor = ($w1*$r1) + ($w2*$r2) + ($w3*$r3) + ($w4*$r4);

    // ✅ FIX DI SINI (pakai $s, bukan $row)
    $hasil[] = [
        'kode' => $s['kode'],
        'nama' => $s['nama'],
        'skor' => round($skor, 4),
    ];
}

// Sorting
usort($hasil, function($a, $b){
    return $b['skor'] <=> $a['skor'];
});

// Tambah ranking
foreach ($hasil as $i => $h) {
    $hasil[$i]['ranking'] = $i + 1;
}

// Output JSON
header('Content-Type: application/json');
echo json_encode($hasil);
?>