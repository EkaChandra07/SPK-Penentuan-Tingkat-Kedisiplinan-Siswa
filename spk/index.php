<!DOCTYPE html>
<html>
<head>
    <title>SPK Kedisiplinan Siswa</title>

    <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom right, #eef2ff, #dbeafe);
            color:#1e293b;
        }

        /* NAVBAR */
        .navbar{
            position: sticky;
            top:0;
            z-index:1000;

            background: rgba(78, 70, 229, 0.41);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);

            color:white;

            padding:18px 30px;

            display:flex;
            justify-content:space-between;
            align-items:center;

            box-shadow:0 2px 10px rgba(0,0,0,0.2);
        }

        .navbar h1{
            font-size:24px;
        }

        .navbar .menu a{
            color:white;
            text-decoration:none;
            margin-left:20px;
            font-weight:bold;
            transition:0.3s;
        }

        .navbar .menu a:hover{
            opacity:0.8;
        }

        .container{
            padding:30px;
        }

        .card{
            background:white;
            border-radius:15px;
            padding:25px;
            box-shadow:0 4px 15px rgba(0,0,0,0.1);
            margin-bottom:30px;
        }

        h2{
            margin-bottom:15px;
            color:#4338ca;
        }

        table{
            border-collapse: collapse;
            width:100%;
            margin-top:15px;
            overflow:hidden;
            border-radius:10px;
            background:white;
        }

        th{
            background:#4f46e5;
            color:white;
        }

        th, td{
            border:1px solid #ddd;
            padding:10px;
            text-align:center;
        }

        tr:nth-child(even){
            background:#f8fafc;
        }

        tr:hover{
            background:#e0e7ff;
            transition:0.2s;
        }

        button{
            background: linear-gradient(to right, #4f46e5, #2563eb);
            color:white;
            border:none;
            padding:12px 18px;
            border-radius:10px;
            cursor:pointer;
            font-weight:bold;
            margin-top:15px;
            transition:0.3s;
        }

        button:hover{
            transform:translateY(-2px);
            opacity:0.9;
        }

        #hasil{
            margin-top:25px;
        }

    </style>
</head>

<body>

<div class="navbar">
    <h1>SPK Kedisiplinan Siswa</h1>

    <div class="menu">
        <a href="index.php">Home</a>
        <a href="evaluasi.php">Evaluasi</a>
        <a href="integrasi.php">Machine Learning</a>
    </div>
</div>

<div class="container">

<div class="card">

<?php
$koneksi_path = __DIR__ . '/koneksi.php';

if (!file_exists($koneksi_path)) {
    die("ERROR: File koneksi.php tidak ditemukan di: " . $koneksi_path);
}

include $koneksi_path;

if (!isset($conn)) {
    die("ERROR: Variable \$conn tidak terdefinisi. Periksa koneksi.php");
}

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

    <a href="integrasi.php">
        <button>Prediksi Machine Learning</button>
    </a>
</div>

<div id="hasil"></div>

</div>

</div>

<script>
function hitung() {
    fetch('hitung_saw.php')
    .then(res => res.json())
    .then(data => {

        let html = "<div class='card'>";
        html += "<h2>Hasil Ranking SAW</h2>";
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
        html += "</div>";

        document.getElementById('hasil').innerHTML = html;

        document.getElementById('tombolEvaluasi').style.display = 'block';
    });
}
</script>

</body>
</html>