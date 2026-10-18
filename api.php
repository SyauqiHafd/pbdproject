<?php

require_once "koneksi.php";

$menu = isset($_GET["menu"]) ? $_GET["menu"] : "";

$map = [
    "satuan" => ["view" => "skala",     "kolom" => "*"],
    "barang" => ["view" => "products",   "kolom" => "*"],
    "vendor" => ["view" => "companies",  "kolom" => "*"],
    "pengguna" => ["view" => "pengguna", "kolom" => "*"],
    "role"   => ["view" => "roles",      "kolom" => "*"],
    "margin" => ["view" => "margins",    "kolom" => "*"],
];

if (!isset($map[$menu])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Menu tidak dikenali"]);
    exit;
}

$view  = $map[$menu]["view"];
$kolom = $map[$menu]["kolom"];
$result = $conn->query("SELECT $kolom FROM $view");

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode([
    "status" => "success",
    "jumlah" => count($data),
    "data"   => $data
]);

$conn->close();