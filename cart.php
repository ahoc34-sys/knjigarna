<?php
$naslovStrani = 'Košarica — Knjigarna';
$rootPath = '';
$cssPath = '';
$jsPath = '';
require_once 'includes/header.php';
?>

<main class="py-5">
    <div class="container">

        <h1 class="h3 mb-4">🛒 Vaša košarica</h1>

        <!-- Seznam knjig v košarici -->
        <div id="vsebinakosarice"></div>

        <!-- Forma za naročilo - skrita dokler ni knjig -->
        <div id="formaNarocila" style="display:none;">
            <hr class="my-4">
            <h4>Podatki za naročilo</h4>

            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <label class="form-label">Ime in priimek *</label>
                    <input type="text" class="form-control" id="imeKupca"
                           placeholder="npr. Janez Novak">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email naslov *</label>
                    <input type="email" class="form-control" id="emailKupca"
                           placeholder="npr. janez@email.si">
                </div>
            </div>

            <div class="mt-3">
                <button class="btn btn-success btn-lg" id="btnNaroci">
                    ✅ Potrdi naročilo
                </button>
                <button class="btn btn-outline-danger ms-2" id="btnPocistiKosarico">
                    🗑️ Počisti košarico
                </button>
            </div>

            <!-- Sporočilo po naročilu -->
            <div id="sporocilo" class="mt-3"></div>
        </div>

    </div>
</main>

<?php require_once 'includes/footer.php'; ?>

<script>
$(document).ready(function() {
    prikaziKosarico(); // takoj ob nalaganju

    // Klik na Potrdi naročilo
    $('#btnNaroci').on('click', function() {
        oddajNarocilo();
    });

    // Klik na Počisti košarico - vgrajen dialog
    $('#btnPocistiKosarico').on('click', function() {
        if (confirm('Res želiš počistiti košarico?')) {
            shraniKosarico([]); //shrani prazen array
            prikaziKosarico(); // osveži prikaz
        }
    });
});

// Prikaži vsebino košarice - pokliče se ob nalaganju + spremembi
function prikaziKosarico() {
    var kosarica = preberiKosarico(); // prebere iz localStorage - main.js

    if (kosarica.length === 0) {
        $('#vsebinakosarice').html(`
            <div class="text-center py-5">
                <p class="text-muted fs-5">Košarica je prazna.</p>
                <a href="books.php" class="btn btn-primary">
                    Pojdi na knjige
                </a>
            </div>
        `);
        $('#formaNarocila').hide(); // .hide() = display:none — skrij formo
        return; //ne gradi tabele za prazno košarico
    }

    // Zgradi tabelo
    var html = `
        <table class="table table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Knjiga</th>
                    <th class="text-center">Količina</th>
                    <th class="text-center">Akcija</th>
                </tr>
            </thead>
            <tbody>
    `;

    $.each(kosarica, function(i, item) {
        html += `
            <tr>
                <td>${item.naslov}</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-outline-secondary"
                            onclick="spremenKolicino(${item.bookID}, -1)">−</button>
                    <span class="mx-2">${item.kolicina}</span>
                    <button class="btn btn-sm btn-outline-secondary"
                            onclick="spremenKolicino(${item.bookID}, 1)">+</button>
                </td>
                <td class="text-center">
                    <button class="btn btn-sm btn-danger"
                            onclick="odstraniIzKosarice(${item.bookID})">
                        Odstrani
                    </button>
                </td>
            </tr>
        `;
    });

    html += '</tbody></table>';
    $('#vsebinakosarice').html(html);
    $('#formaNarocila').show(); // .show() = display:block — prikaže formo za naročilo
}

// Spremeni količino
function spremenKolicino(bookID, sprememba) {
    var kosarica = preberiKosarico();

    // Če količina pade na 0 ali manj — odstrani knjigo iz košarice
    $.each(kosarica, function(i, item) {
        if (item.bookID === bookID) {
            item.kolicina += sprememba;
            if (item.kolicina <= 0) {
                kosarica.splice(i, 1); // odstrani iz polja (na indeksu i)
            }
            return false; // return false v $.each = break — ustavi zanko
        }
    });

    shraniKosarico(kosarica); //local storage
    prikaziKosarico(); // osveži - nove količine
}

// Odstrani knjigo iz košarice
function odstraniIzKosarice(bookID) {
    var kosarica = preberiKosarico();
    // .filter() vrne novo polje samo z elementi ki ustrezajo pogoju 
    //Ohrani vse knjige razen tiste z ujemajočim bookID
    kosarica = kosarica.filter(function(item) {
        return item.bookID !== bookID;
    });
    shraniKosarico(kosarica);
    prikaziKosarico();
}

// Oddaj naročilo na API
function oddajNarocilo() {
    var ime = $('#imeKupca').val().trim();
    var email = $('#emailKupca').val().trim();

    // Osnovna frontend validacija — PHP naredi podrobnejšo na strežniku
    if (!ime || !email) {
        $('#sporocilo').html(
            '<div class="alert alert-danger">Prosimo izpolni ime in email.</div>'
        );
        return;
    }

    var kosarica  = preberiKosarico();
    var postavke = [];

    // Pretvori košarico v format ki ga API pričakuje: [{BookID, Qty}, ...]
    $.each(kosarica, function(i, item) {
        postavke.push({ BookID: item.bookID, Qty: item.kolicina });
    });

    var podatki = {
        CustomerName: ime,
        CustomerEmail: email,
        postavke: postavke
    };

    // Onemogoči gumb med pošiljanjem — *!prepreči dvojno naročilo ob hitrem dvojnem kliku
    $('#btnNaroci').prop('disabled', true).text('Pošiljam...');

    $.ajax({
        url: 'api/orders.php',
        method: 'POST',
        contentType: 'application/json', // telo je json
        data: JSON.stringify(podatki), // js objekt v json string
        success: function(odgovor) {
            shraniKosarico([]); 
            prikaziKosarico();
            $('#formaNarocila').hide();
            // odgovor.OrderID — API vrne id naročila 
            $('#sporocilo').html(`
                <div class="alert alert-success">
                    ✅ Naročilo št. <strong>${odgovor.OrderID}</strong>
                    je bilo uspešno oddano! Hvala, ${ime}.
                </div>
            `);
            $('#sporocilo').show();
        },
        error: function(xhr) {
            // xhr.responseJSON vsebuje JSON odgovor strežnika (PHP napaka)
            // Če ni json odgovora — prikaži generično sporočilo
            var napaka = xhr.responseJSON
                ? xhr.responseJSON.napaka
                : 'Napaka pri oddaji naročila.';
            $('#sporocilo').html(
                '<div class="alert alert-danger">' + napaka + '</div>'
            );
            // !Znova omogoči gumb — lahko popravi podatke in pošlje znova
            $('#btnNaroci').prop('disabled', false).text('✅ Potrdi naročilo');
        }
    });
}
</script>