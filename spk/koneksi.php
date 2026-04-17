<?php
$conn = mysqli_connect("localhost", "root", "", "spk_siswa");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>