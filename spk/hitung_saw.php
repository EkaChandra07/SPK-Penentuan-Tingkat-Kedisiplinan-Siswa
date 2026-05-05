<?php
// ✅ Load koneksi dengan validasi
require_once __DIR__ . '/koneksi.php';

// ✅ Cek apakah koneksi berhasil
if (!isset($conn) || !$conn) {
    http_response_code(500);
    echo json_encode(['error' => 'Koneksi database gagal']);
    exit;
}

// ✅ Ambil data siswa
$data = mysqli_query($conn, "SELECT * FROM siswa");
if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Query siswa gagal: ' . mysqli_error($conn)]);
    exit;
}
$siswa = mysqli_fetch_all($data, MYSQLI_ASSOC);

if (empty($siswa)) {
    http_response_code(400);
    echo json_encode(['error' => 'Data siswa kosong']);
    exit;
}

// ✅ Ambil kriteria
$krit = mysqli_query($conn, "SELECT * FROM kriteria ORDER BY id ASC");
if (!$krit) {
    http_response_code(400);
    echo json_encode(['error' => 'Query kriteria gagal: ' . mysqli_error($conn)]);
    exit;
}
$kriteria = mysqli_fetch_all($krit, MYSQLI_ASSOC);

if (count($kriteria) < 4) {
    http_response_code(400);
    echo json_encode(['error' => 'Kriteria harus ada 4 data']);
    exit;
}

$w1 = (float)$kriteria[0]['bobot'];
$w2 = (float)$kriteria[1]['bobot'];
$w3 = (float)$kriteria[2]['bobot'];
$w4 = (float)$kriteria[3]['bobot'];

// ✅ Hitung nilai maksimum dan minimum
$kehadiran_vals = array_column($siswa, 'kehadiran');
$sikap_vals = array_column($siswa, 'sikap');
$terlambat_vals = array_column($siswa, 'terlambat');
$pelanggaran_vals = array_column($siswa, 'pelanggaran');

$max1 = !empty($kehadiran_vals) ? max($kehadiran_vals) : 1;
$max4 = !empty($sikap_vals) ? max($sikap_vals) : 1;

$terlambat_nonzero = array_filter($terlambat_vals, fn($v) => $v > 0);
$pelanggaran_nonzero = array_filter($pelanggaran_vals, fn($v) => $v > 0);

$min2 = !empty($terlambat_nonzero) ? min($terlambat_nonzero) : 1;
$min3 = !empty($pelanggaran_nonzero) ? min($pelanggaran_nonzero) : 1;

$hasil = [];

// ✅ Bersihkan tabel hasil_saw
$truncate = mysqli_query($conn, "TRUNCATE TABLE hasil_saw");
if (!$truncate) {
    http_response_code(400);
    echo json_encode(['error' => 'Gagal membersihkan tabel: ' . mysqli_error($conn)]);
    exit;
}

// ✅ Hitung skor SAW untuk setiap siswa
foreach ($siswa as $s) {
    $r1 = ($max1 > 0) ? $s['kehadiran'] / $max1 : 0;
    $r2 = ($s['terlambat'] == 0) ? 1 : ($min2 / $s['terlambat']);
    $r3 = ($s['pelanggaran'] == 0) ? 1 : ($min3 / $s['pelanggaran']);
    $r4 = ($max4 > 0) ? $s['sikap'] / $max4 : 0;

    $skor = ($w1 * $r1) + ($w2 * $r2) + ($w3 * $r3) + ($w4 * $r4);

    $hasil[] = [
        'id' => (int)$s['id'],
        'kode' => $s['kode'],
        'nama' => $s['nama'],
        'skor' => round($skor, 4)
    ];
}

// ✅ Urutkan berdasarkan skor descending
usort($hasil, function($a, $b) {
    return $b['skor'] <=> $a['skor'];
});

// ✅ Simpan hasil ranking ke database
foreach ($hasil as $i => $h) {
    $ranking = $i + 1;
    $hasil[$i]['ranking'] = $ranking;

    $siswa_id = mysqli_real_escape_string($conn, $h['id']);
    $skor = mysqli_real_escape_string($conn, $h['skor']);
    
    $insert = mysqli_query($conn, "
        INSERT INTO hasil_saw (siswa_id, skor, ranking)
        VALUES ({$siswa_id}, {$skor}, {$ranking})
    ");
    
    if (!$insert) {
        error_log("Insert error untuk siswa {$siswa_id}: " . mysqli_error($conn));
    }
}

// ✅ Output JSON
header('Content-Type: application/json; charset=utf-8');
echo json_encode($hasil, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>