<!DOCTYPE html>
<html>
<head>
    <title>SPK Kedisiplinan Siswa</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
        button {
            padding: 10px 15px;
            margin: 10px 5px 10px 0;
            cursor: pointer;
        }
        #hasil { margin-top: 20px; }
    </style>
</head>
<body>

<h1>Sistem Pendukung Keputusan - Penentuan Tingkat Kedisiplinan Siswa</h1>

<?php
$koneksi_path = __DIR__ . '/koneksi.php';

if (!file_exists($koneksi_path)) {
    die("ERROR: File koneksi.php tidak ditemukan di: " . $koneksi_path);
}

include $koneksi_path;

if (!isset($conn)) {
    die("ERROR: Variable \$conn tidak terdefinisi. Periksa koneksi.php");
}

// ✅ FIX URUTAN KODE
$query = mysqli_query($conn, "
    SELECT * FROM siswa 
    ORDER BY CAST(SUBSTRING(kode, 2) AS UNSIGNED) ASC
");

if (!$query) {
    die("ERROR Query: " . mysqli_error($conn));
}

$rows = mysqli_fetch_all($query, MYSQLI_ASSOC);
?>

<h2>Data Siswa</h2>
<table>
<tr>
    <th>Kode</th>
    <th>Nama</th>
    <th>Kehadiran</th>
    <th>Terlambat</th>
    <th>Pelanggaran</th>
    <th>Sikap</th>
</tr>

<?php foreach($rows as $r): ?>
<tr>
    <td><?= htmlspecialchars($r['kode']) ?></td>
    <td><?= htmlspecialchars($r['nama']) ?></td>
    <td><?= $r['kehadiran'] ?></td>
    <td><?= $r['terlambat'] ?></td>
    <td><?= $r['pelanggaran'] ?></td>
    <td><?= $r['sikap'] ?></td>
</tr>
<?php endforeach; ?>
</table>

<button onclick="hitung()">Hitung SAW & Ranking</button>

<div id="tombolEvaluasi" style="display:none;">
    <a href="evaluasi.php">
        <button>Lihat Evaluasi SPK</button>
    </a>
</div>

<div id="hasil"></div>

<script>
function hitung() {
    fetch('hitung_saw.php')
    .then(res => res.json())
    .then(data => {

        let html = "<h2>Hasil Ranking SAW</h2>";
        html += "<table>";
        html += "<tr><th>Ranking</th><th>Kode</th><th>Nama</th><th>Skor</th></tr>";

        data.forEach(d => {
            html += `<tr>
                        <td>${d.ranking}</td>
                        <td>${d.kode}</td>
                        <td>${d.nama}</td>
                        <td>${d.skor}</td>
                     </tr>`;
        });

        html += "</table>";

        document.getElementById('hasil').innerHTML = html;

        // tampilkan tombol evaluasi
        document.getElementById('tombolEvaluasi').style.display = 'block';
    });
}
</script>

</body>
</html>