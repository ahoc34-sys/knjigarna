<?php
// Vključi DB povezavo, headerje in pomočne funkcije
require_once __DIR__ . '/../includes/config.php';

// Prebere HTTP metodo in ID iz URL-ja
$metoda = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Ni _method workarounda — kategorije ne nalagajo slik, vedno pride navaden JSON
switch ($metoda) {
    case 'GET':
        if ($id) {
            enaKategorija($id);
        } else {
            vseKategorije();
        }
        break;
    case 'POST':
        zahtevajAdmina();
        dodajKategorijo();
        break;
    case 'PUT':
        zahtevajAdmina();
        if (!$id) odgovori(['napaka' => 'ID kategorije je obvezen'], 400);
        urediKategorijo($id);
        break;
    case 'DELETE':
        zahtevajAdmina();
        if (!$id) odgovori(['napaka' => 'ID kategorije je obvezen'], 400);
        izbrisiKategorijo($id);
        break;
    default:
        odgovori(['napaka' => 'Metoda ni dovoljena'], 405);
}

// Vse kategorije 
function vseKategorije() {
    global $conn;

    // COUNT prešteje knjige v vsaki kategoriji 
    // LEFT JOIN namesto JOIN — vrne VSE kategorije, tudi tiste z 0 knjigami (za zdaj: Triler (0))
    // GROUP BY je obvezen ker je COUNT — SQL mora vedeti kako grupirati vrstice
    $rezultat = $conn->query(
        "SELECT bc.BookCategoryID, bc.Title,
                COUNT(b.BookID) AS StKnjig
         FROM BookCategory bc
         LEFT JOIN Book b ON bc.BookCategoryID = b.BookCategoryID
         GROUP BY bc.BookCategoryID, bc.Title
         ORDER BY bc.Title ASC"
    );

    $kategorije = [];
    while ($vrstica = $rezultat->fetch_assoc()) {
        $kategorije[] = $vrstica;
    }

    odgovori($kategorije);
}

// Ena kategorija GET po id
function enaKategorija($id) {
    global $conn;

    // Brez JOIN — kategorija nima relacij ki bi jih rabil tu
    $stmt = $conn->prepare(
        "SELECT * FROM BookCategory WHERE BookCategoryID = ?"
    );
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $rezultat = $stmt->get_result();

    if ($rezultat->num_rows === 0) {
        odgovori(['napaka' => 'Kategorija ni najdena'], 404);
    }

    odgovori($rezultat->fetch_assoc());
}

// Dodaj kategorijo -POST
function dodajKategorijo() {
    global $conn;
 
    // Samo preberiTelo() — ni $_POST variante ker ni nalaganja slik
    $data = preberiTelo();

    if (empty($data['Title'])) {
        odgovori(['napaka' => 'Naziv kategorije je obvezen'], 400);
    }

    $stmt = $conn->prepare(
        "INSERT INTO BookCategory (Title) VALUES (?)"
    );
    $stmt->bind_param('s', $data['Title']); // s - string
    $stmt->execute();
    // insert_id vrne ID novo ustvarjene kategorije 
    odgovori(['sporocilo' => 'Kategorija dodana', 'BookCategoryID' => $conn->insert_id], 201);
}

// Uredi kategorijo -PUT
function urediKategorijo($id) {
    global $conn;

    // Najprej preveri obstoj — ne bi hotel da UPDATE ne naredi nič
    $preveri = $conn->prepare("SELECT BookCategoryID FROM BookCategory WHERE BookCategoryID = ?");
    $preveri->bind_param('i', $id);
    $preveri->execute();
    if ($preveri->get_result()->num_rows === 0) {
        odgovori(['napaka' => 'Kategorija ni najdena'], 404);
    }

    $data = preberiTelo();

    if (empty($data['Title'])) {
        odgovori(['napaka' => 'Naziv kategorije je obvezen'], 400);
    }
    // 'si' = string za Title, integer za ID — vrstni red se mora vjemati z ? v SQL
    $stmt = $conn->prepare(
        "UPDATE BookCategory SET Title = ? WHERE BookCategoryID = ?"
    );
    $stmt->bind_param('si', $data['Title'], $id);
    $stmt->execute();

    odgovori(['sporocilo' => 'Kategorija posodobljena']);
}

// Izbriši kategorijo 
function izbrisiKategorijo($id) {
    global $conn;

    $preveri = $conn->prepare("SELECT BookCategoryID FROM BookCategory WHERE BookCategoryID = ?");
    $preveri->bind_param('i', $id);
    $preveri->execute();
    if ($preveri->get_result()->num_rows === 0) {
        odgovori(['napaka' => 'Kategorija ni najdena'], 404);
    }

    // Preveri ali ima kategorija knjige
    // Baza bi sama zavrgla brisanje (FOREIGN KEY) -surove MySQL napake
    $knjige = $conn->prepare("SELECT COUNT(*) AS st FROM Book WHERE BookCategoryID = ?");
    $knjige->bind_param('i', $id);
    $knjige->execute();
    $rezultat = $knjige->get_result()->fetch_assoc(); // fetch_assoc() brez zanke — ena vrstica z enim stolpcem (COUNT)

    if ($rezultat['st'] > 0) {
        // 409 Conflict — zahteva je OK ampak konflikt s stanjem v bazi
        // Ne 400 (Bad Request) ker zahteva ni napačna — samo trenutno ni mogoča!
        odgovori(['napaka' => 'Kategorije ni mogoče izbrisati ker vsebuje knjige'], 409);
    }

    $stmt = $conn->prepare("DELETE FROM BookCategory WHERE BookCategoryID = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();

    odgovori(['sporocilo' => 'Kategorija izbrisana']);
}