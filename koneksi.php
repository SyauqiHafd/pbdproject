<?php

$host     = "localhost";      
$username = "root";           
$password = "";               
$database = "proyek_uts";     
$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die(json_encode([
        "status"  => "error",
        "message" => "Koneksi database gagal: " . $conn->connect_error
    ]));
}

$conn->set_charset("utf8mb4");