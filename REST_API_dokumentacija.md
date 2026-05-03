# REST API Dokumentacija - Knjigarna

## Splošne informacije

- **Base URL:** `http://localhost:8888/knjigarna/api`
- **Format:** JSON
- **Kodiranje:** UTF-8

## Avtentikacija

Admin endpoint-i zahtevajo aktivno PHP session.
Najprej pokliči `POST /api/auth.php` za prijavo.

---

## Endpointi

---

### 1. Knjige `/api/books.php`

#### GET - Pridobi vse knjige
GET /api/books.php

Opcijski parametri:
| Parameter | Tip | Opis |
|---|---|---|
| `iskanje` | string | Išči po naslovu ali vsebini |
| `kategorija` | integer | Filtriraj po ID kategorije |

**Primer zahteve:**
GET /api/books.php?iskanje=harry&kategorija=4

**Uspešen odgovor (200):**
```json
[
  {
    "BookID": "1",
    "Name": "Harry Potter in Kamen modrosti",
    "Author": "J.K. Rowling",
    "Description": "Dečko ki je preživel...",
    "BookCover": null,
    "BookCategoryID": "4",
    "CategoryTitle": "Fantazija"
  }
]
```

---

#### GET - Pridobi eno knjigo
GET /api/books.php?id={id}

**Primer zahteve:**
GET /api/books.php?id=1

**Uspešen odgovor (200):**
```json
{
  "BookID": "1",
  "Name": "Harry Potter in Kamen modrosti",
  "Author": "J.K. Rowling",
  "Description": "Dečko ki je preživel...",
  "Content": "Harry Potter je živel...",
  "BookCover": null,
  "BookCategoryID": "4",
  "CategoryTitle": "Fantazija"
}
```

**Napaka (404):**
```json
{ "napaka": "Knjiga ni najdena" }
```

---

#### POST - Dodaj knjigo Admin
POST /api/books.php

**Telo zahteve:**
```json
{
  "Name": "Nova knjiga",
  "Author": "Ime Avtorja",
  "Description": "Kratek opis",
  "Content": "Dolg opis knjige",
  "BookCategoryID": 1
}
```

**Uspešen odgovor (201):**
```json
{ "sporocilo": "Knjiga dodana", "BookID": 16 }
```

**Napaka (400):**
```json
{ "napaka": "Naslov je obvezen" }
```

**Napaka (403):**
```json
{ "napaka": "Dostop zavrnjen. Prijavi se kot admin." }
```

---

#### PUT - Uredi knjigo Admin
PUT /api/books.php?id={id}

**Telo zahteve:**
```json
{
  "Name": "Posodobljen naslov",
  "Author": "Ime Avtorja",
  "Description": "Posodobljen opis",
  "Content": "Posodobljena vsebina",
  "BookCategoryID": 2
}
```

**Uspešen odgovor (200):**
```json
{ "sporocilo": "Knjiga posodobljena" }
```

**Napaka (404):**
```json
{ "napaka": "Knjiga ni najdena" }
```

---

#### DELETE - Izbriši knjigo Admin
DELETE /api/books.php?id={id}

**Uspešen odgovor (200):**
```json
{ "sporocilo": "Knjiga izbrisana" }
```

**Napaka (404):**
```json
{ "napaka": "Knjiga ni najdena" }
```

---

### 2. Kategorije `/api/categories.php`

#### GET - Pridobi vse kategorije
GET /api/categories.php

**Uspešen odgovor (200):**
```json 
[
  {
    "BookCategoryID": "1",
    "Title": "Roman",
    "StKnjig": "5"
  },
  {
    "BookCategoryID": "4",
    "Title": "Fantazija",
    "StKnjig": "4"
  }
]
```

---

#### GET - Pridobi eno kategorijo
GET /api/categories.php?id={id}

**Uspešen odgovor (200):**
```json
{
  "BookCategoryID": "1",
  "Title": "Roman"
}
```

**Napaka (404):**
```json
{ "napaka": "Kategorija ni najdena" }
```

---

#### POST - Dodaj kategorijo Admin
POST /api/categories.php

**Telo zahteve:**
```json
{ "Title": "Nova kategorija" }
```

**Uspešen odgovor (201):**
```json
{
  "sporocilo": "Kategorija dodana",
  "BookCategoryID": 10
}
```

---

#### PUT - Uredi kategorijo Admin
PUT /api/categories.php?id={id}

**Telo zahteve:**
```json
{ "Title": "Posodobljeno ime" }
```

**Uspešen odgovor (200):**
```json
{ "sporocilo": "Kategorija posodobljena" }
```

---

#### DELETE - Izbriši kategorijo Admin
DELETE /api/categories.php?id={id}

**Uspešen odgovor (200):**
```json
{ "sporocilo": "Kategorija izbrisana" }
```

**Napaka (409) — kategorija ima knjige:**
```json
{ "napaka": "Kategorije ni mogoče izbrisati ker vsebuje knjige" }
```

---

### 3. Naročila `/api/orders.php`

#### GET - Pridobi vsa naročila Admin
GET /api/orders.php

**Uspešen odgovor (200):**
```json
[
  {
    "OrderID": "1",
    "CustomerName": "Janez Novak",
    "CustomerEmail": "janez@test.si",
    "OrderDate": "2026-05-01 16:42:27",
    "StPostavk": "2"
  }
]
```

---

#### GET - Pridobi eno naročilo Admin
GET /api/orders.php?id={id}

**Uspešen odgovor (200):**
```json
{
  "OrderID": "1",
  "CustomerName": "Janez Novak",
  "CustomerEmail": "janez@test.si",
  "OrderDate": "2026-05-01 16:42:27",
  "postavke": [
    {
      "ItemID": "1",
      "BookID": "1",
      "Qty": "1",
      "NaslovKnjige": "Harry Potter in Kamen modrosti",
      "Avtor": "J.K. Rowling"
    }
  ]
}
```

---

#### POST - Oddaj naročilo (javno)
POST /api/orders.php

**Telo zahteve:**
```json
{
  "CustomerName": "Janez Novak",
  "CustomerEmail": "janez@test.si",
  "postavke": [
    { "BookID": 1, "Qty": 1 },
    { "BookID": 2, "Qty": 2 }
  ]
}
```

**Uspešen odgovor (201):**
```json
{
  "sporocilo": "Naročilo uspešno oddano",
  "OrderID": 4
}
```

**Napaka (400):**
```json
{ "napaka": "Email naslov ni veljaven" }
```

---

### 4. Avtentikacija `/api/auth.php`

#### POST - Admin prijava
POST /api/auth.php

**Telo zahteve:**
```json
{
  "username": "admin",
  "password": "admin123"
}
```

**Uspešen odgovor (200):**
```json
{ "sporocilo": "Prijava uspešna", "admin": true }
```

**Napaka (401):**
```json
{ "napaka": "Napačno uporabniško ime ali geslo" }
```

---

#### DELETE - Odjava
DELETE /api/auth.php

**Uspešen odgovor (200):**
```json
{ "sporocilo": "Odjava uspešna" }
```

---

#### GET - Preveri status prijave
GET /api/auth.php

**Odgovor (200):**
```json
{ "admin": true }
```

---

## HTTP statusne kode

| Koda | Pomen | Kdaj |
|---|---|---|
| 200 | OK | Uspešen GET/PUT/DELETE |
| 201 | Created | Uspešen POST — nov zapis |
| 400 | Bad Request | Manjkajoči ali napačni podatki |
| 401 | Unauthorized | Napačno geslo |
| 403 | Forbidden | Nisi prijavljen kot admin |
| 404 | Not Found | Zapis ne obstaja |
| 405 | Method Not Allowed | Nepodprta HTTP metoda |
| 409 | Conflict | Konflikt — brisanje kategorije z knjigami |
| 500 | Server Error | Napaka DB povezave |

---

## Primeri napak za vse metode

#### Nepodprta metoda (405)
PATCH /api/books.php
```json
{ "napaka": "Metoda ni dovoljena" }
```

#### Dostop zavrnjen (403)
POST /api/books.php  (brez prijave)
```json
{ "napaka": "Dostop zavrnjen. Prijavi se kot admin." }
```