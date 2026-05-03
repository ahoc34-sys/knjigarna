<?php
require_once __DIR__ . '/../includes/config.php';

// Prebere http metodo
$metoda = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null; // pretvorba v število

// FormData (slike) ne podpira PUT — preberemo iz query parametra -POST z &_method=PUT v URL-ju
if (isset($_GET['_method']) && $_GET['_method'] === 'PUT') {
    $metoda = 'PUT';
}

// glede na metodo pokliče pravo funkcijo (GET, POST, PUT, DELETE)
switch ($metoda) {
    case 'GET':
        if ($id) {
            enaknjiga($id);
        } else {
            vseknjige();
        }
        break;
    case 'POST':
        zahtevajAdmina();
        dodajKnjigo();
        break;
    case 'PUT':
        zahtevajAdmina();
        if (!$id) odgovori(['napaka' => 'ID knjige je obvezen'], 400);
        urediKnjigo($id);
        break;
    case 'DELETE':
        zahtevajAdmina();
        if (!$id) odgovori(['napaka' => 'ID knjige je obvezen'], 400);
        izbrisiKnjigo($id);
        break;
    default:
        odgovori(['napaka' => 'Metoda ni dovoljena'], 405); // vrni ustrezno HTTP kodo
}

// Vse knjige 
function vseknjige() {
    global $conn; // definiran v config
    // Opcijska GET parametra za iskanje in filter kategorije
    $iskanje = $_GET['iskanje'] ?? '';
    $katID = isset($_GET['kategorija']) ? (int)$_GET['kategorija'] : null;

    // Osnovna poizvedba — JOIN poveže Book z BookCategory da dobim ime kategorije
    $sql    = "SELECT b.BookID, b.Name, b.Author, b.Description,
                      b.BookCover, b.BookCategoryID,
                      c.Title AS CategoryTitle
               FROM Book b
               JOIN BookCategory c ON b.BookCategoryID = c.BookCategoryID
               WHERE 1=1"; // WHERE 1=1 je trik — omogoča dinamično dodajanje AND pogojev brez preverjanja ali je to prvi pogoj
    $params = []; // Zbirajo se parametri za prepared statment
    $types  = '';

    // Če je iskanje — pogoj LIKE na oba stolpca
    // % na obeh straneh -> "vsebuje" — doda dvakrat ker sta dva ?
    if ($iskanje !== '') {
        $sql .= " AND (b.Name LIKE ? OR b.Content LIKE ?)";
        $iskanje  = "%$iskanje%";
        $params[] = $iskanje;
        $params[] = $iskanje;
        $types .= 'ss'; // dva s ker sta dva parametra
    }

    // Če je filtriranje po kategoriji
    if ($katID) {
        $sql .= " AND b.BookCategoryID = ?";
        $params[] = $katID;
        $types .= 'i';
    }

    $sql .= " ORDER BY b.Name ASC";

    // Če imam parametre - prepared statement (zaščita pred SQL injection)
    // Če nimam parametrov - direktna poizvedba je varna (ni zunanjih vrednosti)
    if ($params) {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params); // ... razpakira polje v ločene argumente
        $stmt->execute();
        $rezultat = $stmt->get_result();
    } else {
        $rezultat = $conn->query($sql);
    }

    $knjige = [];
    while ($vrstica = $rezultat->fetch_assoc()) {
        $knjige[] = $vrstica;
    }

    odgovori($knjige); // json_encode + http_response_code(200) + exit()
}

// Ena knjiga GET po id
function enaknjiga($id) {
    global $conn;
    // b.* = vsi stolpci iz Book (vključno s Content)
    $stmt = $conn->prepare(
        "SELECT b.*, c.Title AS CategoryTitle 
         FROM Book b
         JOIN BookCategory c ON b.BookCategoryID = c.BookCategoryID
         WHERE b.BookID = ?"
    );
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $rezultat = $stmt->get_result();

    if ($rezultat->num_rows === 0) {
        odgovori(['napaka' => 'Knjiga ni najdena'], 404);
    }
    // fetch_assoc() brez zanke — pričakuje eno vrstico
    odgovori($rezultat->fetch_assoc());
}

// Dodaj knjigo 
function dodajKnjigo() {
    global $conn;
    // FormData (slika) pride v $_POST, navaden JSON pa v request body - preberiTelo()
    $data = !empty($_POST) ? $_POST : preberiTelo();
    // Validacija obveznih polj... napake/shranil prazne vrednosti
    if (empty($data['Name'])) odgovori(['napaka' => 'Naslov je obvezen'], 400);
    if (empty($data['Author'])) odgovori(['napaka' => 'Avtor je obvezen'], 400);
    if (empty($data['BookCategoryID'])) odgovori(['napaka' => 'Kategorija je obvezna'], 400);

    $bookCover = naloadiSliko(); // vrne ime datoteke ali null če slike ni

    $stmt = $conn->prepare(
        "INSERT INTO Book (Name, Author, Description, Content, BookCover, BookCategoryID)
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param(
        'sssssi', // 'sssssi' = 4x string, 1x string (BookCover lahko null), 1x integer
        $data['Name'],
        $data['Author'],
        $data['Description'],
        $data['Content'],
        $bookCover,
        $data['BookCategoryID']
    );
    $stmt->execute();
    // insert_id vrne AUTO_INCREMENT ID novo vstavljene vrstice
    odgovori(['sporocilo' => 'Knjiga dodana', 'BookID' => $conn->insert_id], 201);
}

// Uredi knjigo -PUT
function urediKnjigo($id) {
    global $conn;
    // preverim ali knjiga sploh obstaja + pobere staro sliko
    $preveri = $conn->prepare("SELECT BookID, BookCover FROM Book WHERE BookID = ?");
    $preveri->bind_param('i', $id);
    $preveri->execute();
    $obstoječa = $preveri->get_result()->fetch_assoc();

    if (!$obstoječa) {
        odgovori(['napaka' => 'Knjiga ni najdena'], 404);
    }

    $data = !empty($_POST) ? $_POST : preberiTelo();

    // Naloži novo sliko ali ohrani staro
    $bookCover = naloadiSliko();
    if (!$bookCover) {
        $bookCover = $obstoječa['BookCover'];
    }
    // 'sssssii' = 5x string + 2x integer (BookCategoryID in BookID)
    $stmt = $conn->prepare(
        "UPDATE Book SET Name=?, Author=?, Description=?, Content=?,
         BookCover=?, BookCategoryID=? WHERE BookID=?"
    );
    $stmt->bind_param(
        'sssssii',
        $data['Name'],
        $data['Author'],
        $data['Description'],
        $data['Content'],
        $bookCover,
        $data['BookCategoryID'],
        $id
    );
    $stmt->execute();

    odgovori(['sporocilo' => 'Knjiga posodobljena']);
}

// Izbriši knjigo 
function izbrisiKnjigo($id) {
    global $conn;
    // Preveri obstoj pred brisanjem — brez tega ne ve ali je bilo kaj izbrisano
    $preveri = $conn->prepare("SELECT BookID FROM Book WHERE BookID = ?");
    $preveri->bind_param('i', $id);
    $preveri->execute();
    if ($preveri->get_result()->num_rows === 0) {
        odgovori(['napaka' => 'Knjiga ni najdena'], 404);
    }

    $stmt = $conn->prepare("DELETE FROM Book WHERE BookID = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();

    odgovori(['sporocilo' => 'Knjiga izbrisana']);
}

// Nalaganje slike z $_FILES
function naloadiSliko() {
    if (!isset($_FILES['BookCover']) || // Ni datoteke - vrni null (knjiga brez slike je OK)
        $_FILES['BookCover']['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($_FILES['BookCover']['error'] !== UPLOAD_ERR_OK) {
        odgovori(['napaka' => 'Napaka pri nalaganju slike'], 400);
    }
    // mime_content_type() preveri DEJANSKI tip datoteke — ne samo končnico
    // *praksa za varnostni ukrep: brez tega bi nekdo preimenoval virus.exe v slika.jpg
    $dovoljeniTipi = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $tip = mime_content_type($_FILES['BookCover']['tmp_name']);

    if (!in_array($tip, $dovoljeniTipi)) {
        odgovori(['napaka' => 'Dovoljene so samo slike (jpg, png, gif, webp)'], 400);
    }
    // 5 * 1024 * 1024 = 5MB v bajtih — bolj berljivo kot pisati 5242880
    if ($_FILES['BookCover']['size'] > 5 * 1024 * 1024) {
        odgovori(['napaka' => 'Slika je prevelika (max 5MB)'], 400);
    }
    // uniqid() generira unikaten ID na podlagi časa — prepreči enaka imena datotek
    $ext = pathinfo($_FILES['BookCover']['name'], PATHINFO_EXTENSION);
    $imeDat = 'cover_' . uniqid() . '.' . strtolower($ext);
    $pot = __DIR__ . '/../assets/uploads/' . $imeDat;
    
    // move_uploaded_file premakne začasno datoteko (/tmp/) na pravo mesto
    // PHP začasno shrani vsako naloženo datoteko — brez tega bi se izbrisala ob koncu zahteve
    if (!move_uploaded_file($_FILES['BookCover']['tmp_name'], $pot)) {
        odgovori(['napaka' => 'Shranjevanje slike ni uspelo'], 500);
    }
    // Vrne samo ime datoteke (ne polno pot) — shranimo v bazo
    // Frontend zgradi pot: 'assets/uploads/' + BookCover
    return $imeDat;
}