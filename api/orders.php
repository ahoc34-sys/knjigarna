<?php
require_once __DIR__ . '/../includes/config.php';

$metoda = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

switch ($metoda) {
    case 'GET': // GET zahteva admin — naročila so zasebni podatki kupcev
        zahtevajAdmina();
        if ($id) {
            enoNarocilo($id);
        } else {
            vsaNarocila();
        }
        break;
    case 'POST': // POST je javen — vsak kupec lahko odda naročilo, ni zahtevajAdmina()
        oddajNarocilo();
        break;
    default: // Ni PUT in DELETE — naročil se ne ureja/briše
        odgovori(['napaka' => 'Metoda ni dovoljena'], 405);
}

// Vsa naročila -admin
function vsaNarocila() {
    global $conn;

    // COUNT prešteje knjige v vsakem naročilu 
    // LEFT JOIN vrne vsa naročila tudi če nimajo postavk
    // GROUP BY obvezen ker je COUNT
    // ORDER BY DESC — najnovejša naročila prva
    $rezultat = $conn->query(
        "SELECT o.OrderID, o.CustomerName, o.CustomerEmail,
                o.OrderDate, COUNT(oi.ItemID) AS StPostavk
         FROM Orders o
         LEFT JOIN OrderItem oi ON o.OrderID = oi.OrderID
         GROUP BY o.OrderID
         ORDER BY o.OrderDate DESC"
    );

    $narocila = [];
    while ($vrstica = $rezultat->fetch_assoc()) {
        $narocila[] = $vrstica;
    }

    odgovori($narocila);
}

// GET, eno naročilo z postavkami -admin
function enoNarocilo($id) {
    global $conn;
    // Prva poizvedba — podatki naročila
    $stmt = $conn->prepare("SELECT * FROM Orders WHERE OrderID = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $rezultat = $stmt->get_result();

    if ($rezultat->num_rows === 0) {
        odgovori(['napaka' => 'Naročilo ni najdeno'], 404);
    }
    // shrani naročilo v spremenljivko
    $narocilo = $rezultat->fetch_assoc();

    // Pridobi postavke naročila // JOIN Book da dobim ime in avtorja — ne samo BookID
    $stmt2 = $conn->prepare(
        "SELECT oi.ItemID, oi.BookID, oi.Qty,
                b.Name AS NaslovKnjige, b.Author AS Avtor
         FROM OrderItem oi
         JOIN Book b ON oi.BookID = b.BookID
         WHERE oi.OrderID = ?"
    );
    $stmt2->bind_param('i', $id);
    $stmt2->execute();
    $postavke = $stmt2->get_result();

    // da doda prazno polje v naročilo -gnezdenje: naročilo vsebuje seznam knjih
    $narocilo['postavke'] = [];
    while ($vrstica = $postavke->fetch_assoc()) {
        $narocilo['postavke'][] = $vrstica;
    }
    // Vrne gnezden JSON: { OrderID, CustomerName, postavke: [{...}, {...}] }
    odgovori($narocilo);
}

// Oddaj naročilo (javno — vsak uporabnik) 
function oddajNarocilo() {
    global $conn;

    $data = preberiTelo(); // JSON pride iz cart.php AJAX klica

    // Validacija
    if (empty($data['CustomerName'])) {
        odgovori(['napaka' => 'Ime je obvezno'], 400);
    }
    if (empty($data['CustomerEmail'])) {
        odgovori(['napaka' => 'Email je obvezen'], 400);
    }
    if (!filter_var($data['CustomerEmail'], FILTER_VALIDATE_EMAIL)) {
        odgovori(['napaka' => 'Email naslov ni veljaven'], 400);
    }
    // is_array preveri da so postavke res polje ne string ali null
    if (empty($data['postavke']) || !is_array($data['postavke'])) {
        odgovori(['napaka' => 'Naročilo mora vsebovati vsaj eno knjigo'], 400);
    }

    // Vstavi naročilo -sql samodejno nastavi datum
    $stmt = $conn->prepare(
        "INSERT INTO Orders (CustomerName, CustomerEmail) VALUES (?, ?)"
    );
    $stmt->bind_param('ss', $data['CustomerName'], $data['CustomerEmail']);
    $stmt->execute(); // shrani id ustvarjenega naročila za vstavljanje postavk
    $narociloID = $conn->insert_id;

    // Prepared statement pripravi 1x pred zanko — bolj učinkovito
    // Baza ga enkrat preveri in optimizira, v zanki samo zamenjaj parametre
    $stmt2 = $conn->prepare(
        "INSERT INTO OrderItem (OrderID, BookID, Qty) VALUES (?, ?, ?)"
    );

    foreach ($data['postavke'] as $postavka) {
        $bookID = (int)($postavka['BookID'] ?? 0);
        $qty = (int)($postavka['Qty'] ?? 1); // ?? privzeto 0/1
        
        // continue preskoči to iteracijo in nadaljuje z naslednjo — ne ustavi zanke
        if ($bookID === 0) continue; //neveljaven id preskoči

        // Preveri ali knjiga obstaja
        $preveri = $conn->prepare("SELECT BookID FROM Book WHERE BookID = ?");
        $preveri->bind_param('i', $bookID);
        $preveri->execute();
        if ($preveri->get_result()->num_rows === 0) continue;

        $stmt2->bind_param('iii', $narociloID, $bookID, $qty); // 3x integeri
        $stmt2->execute();
    }

    // Vrne OrderID — cart.php ga prikaže v potrditvenem sporočilu
    odgovori([
        'sporocilo' => 'Naročilo uspešno oddano',
        'OrderID' => $narociloID
    ], 201);
}