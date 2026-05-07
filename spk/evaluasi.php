<?php
require_once 'koneksi.php';

if (!isset($conn) || !$conn) {
    die("<h3>ERROR: Koneksi database gagal!</h3>");
}

$cek = mysqli_query($conn, "SELECT COUNT(*) AS total FROM hasil_saw");

$jumlah = mysqli_fetch_assoc($cek)['total'];

if ($jumlah == 0) {
    die("
        <h3>Silakan lakukan perhitungan SAW terlebih dahulu.</h3>
        <a href='index.php'>← Kembali</a>
    ");
}

$gt_query = mysqli_query($conn, "
    SELECT siswa_id, ranking_pakar
    FROM ground_truth
    ORDER BY ranking_pakar ASC
");

$ground_truth = [];

while ($row = mysqli_fetch_assoc($gt_query)) {
    $ground_truth[$row['siswa_id']] = $row['ranking_pakar'];
}

$spk_query = mysqli_query($conn, "
    SELECT h.siswa_id, s.nama, h.skor, h.ranking
    FROM hasil_saw h
    JOIN siswa s ON h.siswa_id = s.id
    ORDER BY h.ranking ASC
");

$hasil = mysqli_fetch_all($spk_query, MYSQLI_ASSOC);

$n = count($hasil);
$sum_d2 = 0;

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

$top_spk_id = $hasil[0]['siswa_id'] ?? 0;
$top_pakar_id = array_search(1, $ground_truth);

$akurasi_top1 = ($top_spk_id == $top_pakar_id) ? 100 : 0;

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

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
            font-family: Arial;
            background: linear-gradient(to bottom right, #eef2ff, #dbeafe);
            color:#1e293b;
        }

        .navbar{
            position: sticky;
            top:0;
            z-index:1000;

            background: rgba(9, 3, 130, 0.44);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);

            color:white;

            padding:18px 30px;

            display:flex;
            justify-content:space-between;
            align-items:center;

            box-shadow:0 2px 10px rgba(0,0,0,0.2);
        }

        .navbar a{
            color:white;
            text-decoration:none;
            margin-left:20px;
            font-weight:bold;
        }

        .container{
            padding:30px;
        }

        .card{
            background:white;
            padding:30px;
            border-radius:15px;
            box-shadow:0 4px 15px rgba(0,0,0,0.1);
        }

        h1{
            margin-bottom:20px;
        }

        table{
            width:100%;
            border-collapse:collapse;
            margin-top:20px;
        }

        th{
            background:#4f46e5;
            color:white;
        }

        th, td{
            border:1px solid #ddd;
            padding:12px;
            text-align:center;
        }

        tr:nth-child(even){
            background:#f8fafc;
        }

        .hasil{
            margin-top:20px;
            padding:20px;
            background:#eef2ff;
            border-left:5px solid #4f46e5;
            border-radius:10px;
        }

        button{
            background: linear-gradient(to right, #4f46e5, #2563eb);
            color:white;
            border:none;
            padding:12px 18px;
            border-radius:10px;
            cursor:pointer;
            margin-top:20px;
        }

    </style>
</head>

<body>

<div class="navbar">
    <h2>Evaluasi SPK</h2>

    <div>
        <a href="index.php">Home</a>
        <a href="integrasi.php">Machine Learning</a>
    </div>
</div>

<div class="container">

<div class="card">

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

<div class="hasil">
    <h3>Interpretasi: <?= $interpretasi ?></h3>
    <p><strong>Rekomendasi:</strong> <?= $rekomendasi ?></p>
</div>

<a href="index.php">
    <button>Kembali ke Halaman Utama</button>
</a>

</div>

</div>

</body>
</html>