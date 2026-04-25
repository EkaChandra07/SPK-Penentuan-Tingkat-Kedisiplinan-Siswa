<?php
include 'koneksi.php';

$data = mysqli_query($conn, "SELECT * FROM siswa");
$siswa = mysqli_fetch_all($data, MYSQLI_ASSOC);

$krit = mysqli_query($conn, "SELECT * FROM kriteria ORDER BY id ASC");
$kriteria = mysqli_fetch_all($krit, MYSQLI_ASSOC);

$w1 = $kriteria[0]['bobot'];
$w2 = $kriteria[1]['bobot'];
$w3 = $kriteria[2]['bobot'];
$w4 = $kriteria[3]['bobot'];

$max1 = max(array_column($siswa, 'kehadiran'));
$max4 = max(array_column($siswa, 'sikap'));

$terlambat_nonzero = array_filter(array_column($siswa, 'terlambat'), fn($v) => $v > 0);
$pelanggaran_nonzero = array_filter(array_column($siswa, 'pelanggaran'), fn($v) => $v > 0);

$min2 = !empty($terlambat_nonzero) ? min($terlambat_nonzero) : 1;
$min3 = !empty($pelanggaran_nonzero) ? min($pelanggaran_nonzero) : 1;

$hasil = [];

mysqli_query($conn, "TRUNCATE TABLE hasil_saw");

foreach ($siswa as $s) {
    $r1 = $s['kehadiran'] / $max1;
    $r2 = ($s['terlambat'] == 0) ? 1 : ($min2 / $s['terlambat']);
    $r3 = ($s['pelanggaran'] == 0) ? 1 : ($min3 / $s['pelanggaran']);
    $r4 = $s['sikap'] / $max4;

    $skor = ($w1 * $r1) + ($w2 * $r2) + ($w3 * $r3) + ($w4 * $r4);

    $hasil[] = [
        'id' => $s['id'],
        'kode' => $s['kode'],
        'nama' => $s['nama'],
        'skor' => round($skor, 4)
    ];
}

usort($hasil, function($a, $b) {
    return $b['skor'] <=> $a['skor'];
});

foreach ($hasil as $i => $h) {
    $ranking = $i + 1;
    $hasil[$i]['ranking'] = $ranking;

    mysqli_query($conn, "
        INSERT INTO hasil_saw (siswa_id, skor, ranking)
        VALUES ({$h['id']}, {$h['skor']}, {$ranking})
    ");
}

header('Content-Type: application/json');
echo json_encode($hasil);
?>