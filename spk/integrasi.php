<!DOCTYPE html>
<html>
<head>
    <title>Prediksi Kedisiplinan Siswa (Machine Learning)</title>

    <style>
        body{
            font-family: Arial;
            margin: 20px;
        }

        input{
            padding: 8px;
            width: 300px;
            margin-bottom: 10px;
        }

        button{
            padding: 10px 15px;
            cursor: pointer;
        }

        .hasil{
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #000;
            width: 300px;
        }
    </style>
</head>
<body>

<h1>Prediksi Kedisiplinan Siswa Menggunakan Machine Learning</h1>

<form method="POST">

    <p>Kehadiran</p>
    <input type="number" name="kehadiran" required>

    <p>Terlambat</p>
    <input type="number" name="terlambat" required>

    <p>Pelanggaran</p>
    <input type="number" name="pelanggaran" required>

    <p>Nilai Sikap</p>
    <input type="number" name="sikap" required>

    <br><br>

    <button type="submit">Prediksi</button>

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
    echo "<h3>Hasil Prediksi:</h3>";
    echo "<b>" . $hasil['prediksi'] . "</b>";
    echo "</div>";
}

?>

<br><br>

<a href="index.php">
    <button>Kembali ke Halaman Utama</button>
</a>

</body>
</html>