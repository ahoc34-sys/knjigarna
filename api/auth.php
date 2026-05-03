<?php
require_once __DIR__ . '/../includes/config.php';

// session_start() mora biti pred vsakim branjem ali pisanjem $_SESSION
// Aktivira session sistem — naloži shranjene podatke za ta brskalnik
session_start();

$metoda = $_SERVER['REQUEST_METHOD'];

switch ($metoda) {
    case 'POST':
        prijava();
        break;
    case 'DELETE':
        odjava();
        break;
    case 'GET':
        preveriPrijavo();
        break; // frontend preveri ali je admin še prijavljen
    default:
        odgovori(['napaka' => 'Metoda ni dovoljena'], 405);
}

// Prijava 
function prijava() {
    $data = preberiTelo(); // JSON pride iz admin/index.php AJAX klica

    $uporabniskoIme = $data['username'] ?? '';
    $geslo = $data['password'] ?? '';

    if (empty($uporabniskoIme) || empty($geslo)) {
        odgovori(['napaka' => 'Uporabniško ime in geslo sta obvezna'], 400);
    }

    // Primerjam z ADMIN_USER in ADMIN_PASS konstantama iz config.php -strogo primerjanje
    if ($uporabniskoIme === ADMIN_USER && $geslo === ADMIN_PASS) {
        $_SESSION['admin'] = true; // Nastavi session — ta vrednost ostane shranjena dokler ne pokliče session_destroy()
        odgovori(['sporocilo' => 'Prijava uspešna', 'admin' => true]);
    } else {
        // Različno od 403 Forbidden - si prijavljen, nimaš dovoljenja
        odgovori(['napaka' => 'Napačno uporabniško ime ali geslo'], 401);
    }
}

// Odjava 
function odjava() {
    $_SESSION = []; // pobriše vse podatke iz session polja v spominu
    session_destroy(); // izbriše session datoteko na strežniku + razveljavi cookie
    odgovori(['sporocilo' => 'Odjava uspešna']);
}

// Preveri ali je admin prijavljen 
// !empty() — vrne true če vrednost OBSTAJA in NI prazna
// Obratno od empty() ki vrne true za null, '', 0, []
function preveriPrijavo() {
    $prijavljen = !empty($_SESSION['admin']);

    // Vrne admin: true/false
    // admin/index.php pokliče ta endpoint ob nalaganju — glede na odgovor
    // pokaže prijavni obrazec ali admin vsebino
    odgovori(['admin' => $prijavljen]);
} 