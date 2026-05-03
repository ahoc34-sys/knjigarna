<?php
$host = 'localhost';
$db_name = 'knjigarna';
$user = 'root';
$pass = '';
$port = 3306;

// Vzpostavi povezavo z sql
$conn = new mysqli($host, $user, $pass, $db_name, $port);
$conn->set_charset('utf8mb4');

// Preveri povezavo
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['napaka' => 'Napaka povezave z bazo']);
    exit();
}

// Nastavi JSON header - vsak API odgovor bo JSON
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *'); // Težave z AJAX klici iz brskalnika (CORS)
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Če pride OPTIONS request - takoj odgovori in končaj
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Admin podatki (fiksni)
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'admin123');

// Pomožne funkcije - ni treba koperati isto kodo povsod
// odgovori() - json_encode + http_response_code v vsakem endpointu
// preberiTelo() - $_POST ne dela z JSON, je treba file_get_contents('php://input')
// zahtevajAdmina() - preverjanje sessiona pred vsakim admin endpointom

// Pomožna funkcija - pošlje JSON odgovor in konča
function odgovori($podatki, $koda = 200) {
    http_response_code($koda);
    echo json_encode($podatki, JSON_UNESCAPED_UNICODE);
    exit();
}

// Pomožna funkcija - prebere JSON telo POST/PUT zahteve
function preberiTelo() {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

// Pomožna funkcija - preveri ali je admin prijavljen
function zahtevajAdmina() {
    session_start();
    if (empty($_SESSION['admin'])) {
        odgovori(['napaka' => 'Dostop zavrnjen. Prijavi se kot admin.'], 403);
    }
}