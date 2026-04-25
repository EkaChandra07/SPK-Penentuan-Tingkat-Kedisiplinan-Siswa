<?php
include 'koneksi.php';

/* =========================================================
   CEK APAKAH HASIL SAW SUDAH ADA
========================================================= */
$cek = mysqli_query($conn, "SELECT COUNT(*) AS total FROM hasil_saw");
$jumlah = mysqli_fetch_assoc($cek)['total'];

if ($jumlah == 0) {
    die("
        <h3>Silakan lakukan perhitungan SAW terlebih dahulu dari halaman utama.</h3>
        <a href='index.php'>← Kembali ke Halaman Utama</a>
    ");
}

/* =========================================================
   AMBIL DATA GROUND TRUTH (RANKING PAKAR)
========================================================= */
$gt_query = mysqli_query($conn, "
    SELECT siswa_id, ranking_pakar
    FROM ground_truth
    ORDER BY ranking_pakar ASC
");

$ground_truth = [];
while ($row = mysqli_fetch_assoc($gt_query)) {
    $ground_truth[$row['siswa_id']] = $row['ranking_pakar'];
}

/* =========================================================
   AMBIL HASIL RANKING SPK
========================================================= */
$spk_query = mysqli_query($conn, "
    SELECT h.siswa_id, s.nama, h.skor, h.ranking
    FROM hasil_saw h
    JOIN siswa s ON h.siswa_id = s.id
    ORDER BY h.ranking ASC
");

$hasil = mysqli_fetch_all($spk_query, MYSQLI_ASSOC);

$n = count($hasil);
$sum_d2 = 0;

/* =========================================================
   HITUNG SPEARMAN RANK CORRELATION
========================================================= */
foreach ($hasil as $item) {
    $id = $item['siswa_id'];

    if (isset($ground_truth[$id])) {
        $rank_spk   = $item['ranking'];
        $rank_pakar = $ground_truth[$id];

        $d = $rank_spk - $rank_pakar;
        $sum_d2 += pow($d, 2);
    }
}

$rs = ($n > 1)
    ? 1 - ((6 * $sum_d2) / ($n * (($n * $n) - 1)))
    : 0;

/* =========================================================
   HITUNG AKURASI TOP-1
========================================================= */
$top_spk_id = $hasil[0]['siswa_id'] ?? 0;
$top_pakar_id = array_search(1, $ground_truth);

$akurasi_top1 = ($top_spk_id == $top_pakar_id) ? 100 : 0;

/* =========================================================
   HITUNG AKURASI TOP-3
========================================================= */
$top3_spk = array_slice(array_column($hasil, 'siswa_id'), 0, 3);

$top3_pakar = [];
foreach ($ground_truth as $id => $rank) {
    if ($rank <= 3) {
        $top3_pakar[] = $id;
    }
}

$cocok = count(array_intersect($top3_spk, $top3_pakar));
$akurasi_top3 = (count($top3_pakar) >= 3)
    ? ($cocok / 3) * 100
    : 0;

/* =========================================================
   INTERPRETASI HASIL
========================================================= */
if ($rs >= 0.90) {
    $interpretasi = "Sangat Baik";
    $rekomendasi = "Model SPK sangat sesuai dengan penilaian pakar.";
} elseif ($rs >= 0.70) {
    $interpretasi = "Baik";
    $rekomendasi = "Model cukup akurat, namun bobot dapat ditinjau ulang.";
} else {
    $interpretasi = "Perlu Perbaikan";
    $rekomendasi = "Bobot kriteria perlu dievaluasi ulang.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Evaluasi Kinerja SPK</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        table {
            border-collapse: collapse;
            width: 60%;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #000;
            padding: 10px;
        }

        th {
            background-color: #f2f2f2;
            text-align: center;
        }

        td:last-child {
            text-align: center;
        }
    </style>
</head>
<body>

<h1>Evaluasi Kinerja SPK Kedisiplinan Siswa</h1>

<table>
    <tr>
        <th>Metrik</th>
        <th>Nilai</th>
    </tr>
    <tr>
        <td>Spearman Rank Correlation</td>
        <td><?= round($rs, 4) ?></td>
    </tr>
    <tr>
        <td>Akurasi Top-1</td>
        <td><?= $akurasi_top1 ?>%</td>
    </tr>
    <tr>
        <td>Akurasi Top-3</td>
        <td><?= round($akurasi_top3, 2) ?>%</td>
    </tr>
</table>

<h3>Interpretasi: <?= $interpretasi ?></h3>
<p><strong>Rekomendasi:</strong> <?= $rekomendasi ?></p>

<a href="index.php">← Kembali ke Halaman Utama</a>

</body>
</html>