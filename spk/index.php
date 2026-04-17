<!DOCTYPE html>
<html>
<head>
    <title>SPK Kedisiplinan Siswa</title>
</head>
<body>

<h1>Sistem Pendukung Keputusan - Penentuan Tingkat Kedisiplinan Siswa</h1>

<?php
include 'koneksi.php';

// FIX: ambil data dan simpan ke $rows
$query = mysqli_query($conn, "SELECT * FROM siswa");
$rows  = mysqli_fetch_all($query, MYSQLI_ASSOC);
?>

<h2>Data Siswa</h2>
<table border="1">
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
  <td><?= $r['kode'] ?></td>
  <td><?= $r['nama'] ?></td>
  <td><?= $r['kehadiran'] ?></td>
  <td><?= $r['terlambat'] ?></td>
  <td><?= $r['pelanggaran'] ?></td>
  <td><?= $r['sikap'] ?></td>
</tr>
<?php endforeach; ?>
</table>

<br>
<button onclick="hitung()">Hitung SAW & Ranking</button>

<div id="hasil"></div>

<script>
function hitung(){
    fetch('hitung_saw.php')
    .then(res => res.json())
    .then(data => {

        // FIX: tampilkan tabel, bukan list biar lebih rapi
        let html = "<h2>Hasil Ranking SAW</h2>";
        html += "<table border='1'><tr><th>Ranking</th><th>Kode</th><th>Nama</th><th>Skor</th></tr>";

        data.forEach((d) => {
            html += `<tr>
                <td>${d.ranking}</td>
                <td>${d.kode}</td>
                <td>${d.nama}</td>
                <td>${d.skor}</td>
             </tr>`;
});

        html += "</table>";

        document.getElementById("hasil").innerHTML = html;
    });
}
</script>

</body>
</html>