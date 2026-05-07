<!DOCTYPE html>
<html>
<head>
    <title>Prediksi Kedisiplinan Siswa (Machine Learning)</title>

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

            background: rgba(110, 70, 229, 0.44);
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
            padding:40px;
        }

        .card{
            background:white;
            max-width:600px;
            margin:auto;
            padding:35px;
            border-radius:20px;
            box-shadow:0 5px 20px rgba(0,0,0,0.1);
        }

        h2{
            margin-bottom:25px;
            color:#4338ca;
            text-align:center;
        }

        .form-group{
            margin-bottom:20px;
        }

        label{
            display:block;
            margin-bottom:8px;
            font-weight:bold;
            color:#374151;
        }

        input{
            width:100%;
            padding:12px;
            border:1px solid #cbd5e1;
            border-radius:10px;
            outline:none;
            transition:0.3s;
            font-size:15px;
        }

        input:focus{
            border-color:#4f46e5;
            box-shadow:0 0 8px rgba(79,70,229,0.3);
        }

        button{
            background: linear-gradient(to right, #4f46e5, #2563eb);
            color:white;
            border:none;
            padding:12px 18px;
            border-radius:10px;
            cursor:pointer;
            font-weight:bold;
            transition:0.3s;
            margin-top:10px;
        }

        button:hover{
            transform:translateY(-2px);
            opacity:0.9;
        }

        .hasil{
            margin-top:25px;
            padding:20px;
            border-radius:12px;
            background:#eef2ff;
            border-left:6px solid #4f46e5;
            text-align:center;
        }

        .hasil h3{
            margin-bottom:10px;
            color:#4338ca;
        }

        .hasil b{
            font-size:22px;
            color:#1e3a8a;
        }

        .btn-kembali{
            text-align:center;
            margin-top:25px;
        }

    </style>
</head>

<body>

<!-- NAVBAR -->
<div class="navbar">

    <h1>Machine Learning SPK</h1>

    <div class="menu">
        <a href="index.php">Home</a>
        <a href="evaluasi.php">Evaluasi</a>
        <a href="integrasi.php">Machine Learning</a>
    </div>

</div>

<div class="container">

<div class="card">

<h2>Prediksi Tingkat Kedisiplinan Siswa</h2>

<form method="POST">

    <div class="form-group">
        <label>Kehadiran (%)</label>
        <input type="number" name="kehadiran" required>
    </div>

    <div class="form-group">
        <label>Terlambat</label>
        <input type="number" name="terlambat" required>
    </div>

    <div class="form-group">
        <label>Pelanggaran</label>
        <input type="number" name="pelanggaran" required>
    </div>

    <div class="form-group">
        <label>Nilai Sikap</label>
        <input type="number" name="sikap" required>
    </div>

    <button type="submit">Prediksi Sekarang</button>

</form>

<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $data = [
        'kehadiran' => $_POST['kehadiran'],
        'terlambat' => $_POST['terlambat'],
        'pelanggaran' => $_POST['pelanggaran'],
        'sikap' => $_POST['sikap']
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/json",
            'method'  => 'POST',
            'content' => json_encode($data),
        ]
    ];

    $context  = stream_context_create($options);

    $result = file_get_contents(
        'http://127.0.0.1:5000/prediksi',
        false,
        $context
    );

    $hasil = json_decode($result, true);

    echo "<div class='hasil'>";
    echo "<h3>Hasil Prediksi Machine Learning</h3>";
    echo "<b>" . $hasil['prediksi'] . "</b>";
    echo "</div>";
}

?>

<div class="btn-kembali">
    <a href="index.php">
        <button>Kembali ke Halaman Utama</button>
    </a>
</div>

</div>

</div>

</body>
</html>