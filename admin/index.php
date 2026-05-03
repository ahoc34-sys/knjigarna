<?php
// '../' ker je admin/index.php v podmapi — poti do assets/ in includes/ so drugačne
$naslovStrani = 'Administracija — Knjigarna';
$rootPath = '../';
$cssPath = '../';
$jsPath = '../';
require_once '../includes/header.php';
?>

<!-- PRIJAVA (prikazana če ni session) -->
<div id="prijavniOvoj" class="d-flex align-items-center justify-content-center"
     style="min-height: 80vh; display:none !important;">
    <div class="card shadow p-4" style="width: 380px;">
        <h3 class="text-center mb-4">Admin prijava</h3>
        <div id="prijavnaNapaka" class="alert alert-danger d-none"></div>
        <div class="mb-3">
            <label class="form-label">Uporabniško ime</label>
            <input type="text" class="form-control" id="adminUser"
                   placeholder="admin">
        </div>
        <div class="mb-3">
            <label class="form-label">Geslo</label>
            <input type="password" class="form-control" id="adminPass"
                   placeholder="••••••••">
        </div>
        <button class="btn btn-primary w-100" id="btnPrijava">
            Prijava
        </button>
    </div>
</div>

<!-- ADMIN VSEBINA (prikazana po prijavi) JS - preveri session -->
<div id="adminVsebina" style="display:none;">

    <!-- Navigacija med sekcijami — data-tab pove kateri tab odpreti -->
    <div class="bg-light border-bottom py-2">
        <div class="container d-flex gap-2 align-items-center">
            <button class="btn btn-sm btn-outline-primary admin-tab"
                    data-tab="knjige">Knjige</button>
            <button class="btn btn-sm btn-outline-primary admin-tab"
                    data-tab="kategorije">Kategorije</button>
            <button class="btn btn-sm btn-outline-primary admin-tab"
                    data-tab="narocila">Naročila</button>
            <button class="btn btn-sm btn-danger ms-auto"
                    id="btnOdjava">Odjava</button>
        </div>
    </div>

    <div class="container py-4">

        <!-- SEKCIJA: KNJIGE — privzeto vidna, ostali dve sta skrite -->
        <div id="tabKnjige" class="admin-sekcija">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>Upravljanje knjig</h4>
                <button class="btn btn-success btn-sm"
                        id="btnNovaKnjiga">+ Nova knjiga</button>
            </div>

            <!-- Forma za dodajanje/urejanje knjige -->
            <!-- type="hidden" — shranjuje BookID pri urejanju, prazen pri novi knjigi -->
            <!-- JS ga prebere v shraniKnjigo() da ve ali je POST (nova) ali PUT (uredi) -->

            <div id="formaKnjige" class="card p-3 mb-4" style="display:none;">
                <h5 id="formaKnjigeNaslov">Nova knjiga</h5>
                <input type="hidden" id="knjigaID">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Naslov *</label>
                        <input type="text" class="form-control" id="knjigaNaslov">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Avtor *</label>
                        <input type="text" class="form-control" id="knjigaAvtor">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kategorija *</label>
                        <select class="form-select" id="knjigaKategorija"></select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Kratek opis</label>
                        <input type="text" class="form-control"
                               id="knjigaOpis" maxlength="255">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Vsebina</label>
                        <textarea class="form-control" id="knjigaVsebina"
                                  rows="4"></textarea>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Naslovna slika knjige</label>
                        <!-- accept="image/*" — brskalnik omeji izbiro na slikovne datoteke -->
                        <!-- PHP dodatno preveri tip z mime_content_type() — frontend ni zanesljiv -->
                        <input type="file" class="form-control" id="knjigaSlika"
                               accept="image/*">
                        <small class="text-muted">Dovoljeni formati: JPG, PNG, GIF, WEBP. Max 5MB.</small>
                        <div id="predogled" class="mt-2"></div>
                    </div>
                </div>
                <div class="mt-3">
                    <button class="btn btn-primary" id="btnShrani">
                        Shrani
                    </button>
                    <button class="btn btn-secondary ms-2"
                            id="btnPreklici">Prekliči</button>
                </div>
            </div>

            <!-- Tabela knjig -naloadiKnjigeAdmin() -->
            <div id="tabelaKnjig"></div>
        </div>

        <!-- SEKCIJA: KATEGORIJE -->
        <!-- Sekciji kategorije in narocila sta privzeto skriti -->
        <!-- JS jih pokaže ob kliku na zavihek -->
        <div id="tabKategorije" class="admin-sekcija" style="display:none;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>Upravljanje kategorij</h4>
            </div>

            <!-- Forma za novo kategorijo -->
            <div class="card p-3 mb-4">
                <h5>Dodaj kategorijo</h5>
                <div class="row g-2">
                    <div class="col-md-8">
                        <input type="text" class="form-control"
                               id="novaKategorija"
                               placeholder="Naziv kategorije">
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-success w-100"
                                id="btnDodajKategorijo">
                            + Dodaj
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tabela kategorij -->
            <div id="tabelaKategorij"></div>
        </div>

        <!-- SEKCIJA: NAROČILA -->
        <div id="tabNarocila" class="admin-sekcija" style="display:none;">
            <h4 class="mb-3">Pregled naročil</h4>
            <div id="tabelaNarocil"></div>
        </div>

    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

<script>
$(document).ready(function() {

    // Preveri prijavo ob nalaganju (session)
    // GET api/auth.php vrne { admin: true/false }
    $.getJSON('../api/auth.php', function(odgovor) {
        if (odgovor.admin) {
            pokaziAdmin();
        } else {
            pokaziPrijavo();
        }
    }).fail(function() {
        pokaziPrijavo(); // če klic ne uspe
    });

    // Prijava 
    $('#btnPrijava').on('click', function() {
        var user = $('#adminUser').val().trim();
        var pass = $('#adminPass').val().trim();

        $.ajax({
            url: '../api/auth.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ username: user, password: pass }),
            success: function() {
                pokaziAdmin();
            },
            error: function() {
                // GET api/auth.php vrne { admin: true/false }
                $('#prijavnaNapaka')
                    .text('Napačno uporabniško ime ali geslo.')
                    .removeClass('d-none');
            }
        });
    });

    // Enter na geslu
    $('#adminPass').on('keyup', function(e) {
        if (e.key === 'Enter') $('#btnPrijava').click();
    });

    // Odjava delete na auth.php pobriše session
    $('#btnOdjava').on('click', function() {
        $.ajax({
            url: '../api/auth.php',
            method: 'DELETE',
            success: function() {
                pokaziPrijavo();
            }
        });
    });

    // Delegiran listener — .admin-tab elementi so statični ampak vzorec je enak kot filter-pill
    $(document).on('click', '.admin-tab', function() {
        var tab = $(this).data('tab'); //prebere data-tab atribut gumba
        $('.admin-sekcija').hide(); //skrije vse sekcije
        // Zgradi ID iz tab vrednosti — 'knjige' -> '#tabKnjige'
        // charAt(0).toUpperCase() + slice(1) = prva črka velika (knjige -> Knjige)
        $('#tab' + tab.charAt(0).toUpperCase() + tab.slice(1)).show();

        // Naloži podatke za aktivni zavihek
        if (tab === 'knjige') naloadiKnjigeAdmin();
        if (tab === 'kategorije') naloadiKategorijeAdmin();
        if (tab === 'narocila') naloadiNarocilaAdmin();
    });

    // Nova knjiga 
    $('#btnNovaKnjiga').on('click', function() {
        $('#knjigaID').val(''); // prazni id - shraniKnjigo() bo POST
        $('#knjigaNaslov, #knjigaAvtor, #knjigaOpis, #knjigaVsebina').val(''); //počisti polja
        $('#knjigaSlika').val('');
        $('#predogled').html('');
        $('#formaKnjigeNaslov').text('Nova knjiga');
        $('#formaKnjige').show();
        naloadiKategorijeSelect(); // naloži kategorije v select dropdown
    });

    // Prekliči formo 
    $('#btnPreklici').on('click', function() {
        $('#formaKnjige').hide();
        $('#predogled').html('');
    });

    // Shrani knjigo 
    $('#btnShrani').on('click', function() {
        shraniKnjigo();
    });

    // Dodaj kategorijo 
    $('#btnDodajKategorijo').on('click', function() {
        dodajKategorijo();
    });

    // FileReader — brskalniški API za branje datotek lokalno
    // readAsDataURL() pretvori sliko v base64 string za src atribut — predogled brez nalaganja 
    $('#knjigaSlika').on('change', function() {
        var datoteka = this.files[0]; // files[0] = prva izbrana datoteka
        if (datoteka) {
            var bralnik = new FileReader();
            bralnik.onload = function(e) {
                // e.target.result = base64 string slike
                $('#predogled').html(
                    '<img src="' + e.target.result + '" ' +
                    'style="max-height: 150px; border-radius: 4px;" ' +
                    'class="mt-2">'
                );
            };
            bralnik.readAsDataURL(datoteka);
        }
    });

}); // konec $(document).ready() 

// Pokaži/skrij sekcije 
function pokaziAdmin() {
    $('#prijavniOvoj').hide();
    $('#adminVsebina').show();
    naloadiKnjigeAdmin(); // takoj naloži knjige
}

function pokaziPrijavo() {
    $('#adminVsebina').hide();
    // .css('display', 'flex') namesto .show() -> flex za centriranje
    $('#prijavniOvoj').css('display', 'flex');
}

// Knjige 
function naloadiKnjigeAdmin() {
    $.getJSON('../api/books.php', function(knjige) {
        var html = `
            <table class="table table-hover table-sm">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th><th>Naslov</th><th>Avtor</th>
                        <th>Kategorija</th><th>Akcija</th>
                    </tr>
                </thead><tbody>
        `;

        $.each(knjige, function(i, k) {
            html += `
                <tr>
                    <td>${k.BookID}</td>
                    <td>${k.Name}</td>
                    <td>${k.Author}</td>
                    <td>${k.CategoryTitle}</td>
                    <td>
                        <button class="btn btn-warning btn-sm"
                                onclick="urediKnjigo(${k.BookID})">
                            Uredi
                        </button>
                        <button class="btn btn-danger btn-sm ms-1"
                                onclick="izbrisiKnjigoAdmin(${k.BookID})">
                            Izbriši
                        </button>
                    </td>
                </tr>
            `;
        });

        html += '</tbody></table>';
        $('#tabelaKnjig').html(html);
    });
}

function naloadiKategorijeSelect() {
    $.getJSON('../api/categories.php', function(kategorije) {
        var html = '';
        $.each(kategorije, function(i, kat) {
            // Zgradi <option> elemente za select dropdown
            html += `<option value="${kat.BookCategoryID}">${kat.Title}</option>`;
        });
        $('#knjigaKategorija').html(html);
    });
}

function urediKnjigo(id) {
    // Naloži podatke knjige in jih vstavi v formo
    $.getJSON('../api/books.php?id=' + id, function(k) {
        $('#knjigaID').val(k.BookID); // nastavi id, shraniknjigo bo PUT
        $('#knjigaNaslov').val(k.Name);
        $('#knjigaAvtor').val(k.Author);
        $('#knjigaOpis').val(k.Description);
        $('#knjigaVsebina').val(k.Content);
        $('#knjigaSlika').val('');
        $('#predogled').html('');
        $('#formaKnjigeNaslov').text('Uredi knjigo');
        $('#formaKnjige').show();
        naloadiKategorijeSelect();

        // setTimeout — počaka 300ms da se select naloži preden nastavi vrednost // poglej rešitve
        // Brez tega bi select še bil prazen ko bi nastavili vrednost
        setTimeout(function() {
            $('#knjigaKategorija').val(k.BookCategoryID);
        }, 300);
    });
}

function shraniKnjigo() {
    var id = $('#knjigaID').val(); // prazen = nova knjiga, neprazen = urejanje

    if (!$('#knjigaNaslov').val().trim() || !$('#knjigaAvtor').val().trim()) {
        alert('Naslov in avtor sta obvezna.');
        return;
    }

    // FormData — edini način za pošiljanje datotek z AJAX
    // Ne uporabi JSON ker ne podpira binarnih datotek
    var formData = new FormData();
    formData.append('Name', $('#knjigaNaslov').val().trim());
    formData.append('Author', $('#knjigaAvtor').val().trim());
    formData.append('Description', $('#knjigaOpis').val().trim());
    formData.append('Content', $('#knjigaVsebina').val().trim());
    formData.append('BookCategoryID', $('#knjigaKategorija').val());

    var slika = $('#knjigaSlika')[0].files[0]; // [0] = DOM element, .files[0] = prva datoteka
    if (slika) {
        formData.append('BookCover', slika); // doda v FormData
    }

    var metoda = id ? 'PUT' : 'POST';
    var url = id
        ? '../api/books.php?id=' + id
        : '../api/books.php';

    // FormData ne podpira PUT —zato POST z &_method=PUT v URL-ju
    // PHP v books.php prebere _method in nastavi $metoda = 'PUT'
    if (metoda === 'PUT') {
        metoda = 'POST';
        url = '../api/books.php?id=' + id + '&_method=PUT';
    }

    $.ajax({
        url: url,
        method: metoda,
        data: formData,
        processData: false, // ne pretvori FormData v URL string — brskalnik to naredi sam
        contentType: false, // ne nastavi Content-Type — brskalnik ga nastavi z boundary vrednostjo
        success: function() {
            $('#formaKnjige').hide();
            $('#knjigaSlika').val('');
            $('#predogled').html('');
            naloadiKnjigeAdmin(); // osveži tbelo
        },
        error: function(xhr) {
            alert('Napaka: ' + (xhr.responseJSON ? xhr.responseJSON.napaka : 'Neznana napaka'));
        }
    });
}

function izbrisiKnjigoAdmin(id) {
    if (!confirm('Res želiš izbrisati to knjigo?')) return;

    $.ajax({
        url: '../api/books.php?id=' + id,
        method: 'DELETE',
        success: function() {
            naloadiKnjigeAdmin();
        },
        error: function(xhr) {
            alert('Napaka: ' + xhr.responseJSON.napaka);
        }
    });
}

// Kategorije 
function naloadiKategorijeAdmin() {
    $.getJSON('../api/categories.php', function(kategorije) {
        var html = `
            <table class="table table-hover table-sm">
                <thead class="table-dark">
                    <tr><th>ID</th><th>Naziv</th><th>Knjig</th><th>Akcija</th></tr>
                </thead><tbody>
        `;

        $.each(kategorije, function(i, kat) {
            html += `
                <tr>
                    <td>${kat.BookCategoryID}</td>
                    <td>${kat.Title}</td>
                    <td>${kat.StKnjig}</td>
                    <td>
                        <button class="btn btn-danger btn-sm"
                                onclick="izbrisiKategorijo(${kat.BookCategoryID}, ${kat.StKnjig})">
                            Izbriši
                        </button>
                    </td>
                </tr>
            `;
        });

        html += '</tbody></table>';
        $('#tabelaKategorij').html(html);
    });
}

function dodajKategorijo() {
    var naziv = $('#novaKategorija').val().trim();
    if (!naziv) { alert('Vpiši naziv kategorije.'); return; }

    $.ajax({
        url: '../api/categories.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ Title: naziv }),
        success: function() {
            $('#novaKategorija').val(''); // počisti input
            naloadiKategorijeAdmin();
        },
        error: function(xhr) {
            alert('Napaka: ' + xhr.responseJSON.napaka);
        }
    });
}

function izbrisiKategorijo(id, stKnjig) {
    // Preveri na frontendu — izogne se API klicu ki bi vedno vrnil 409
    if (stKnjig > 0) {
        alert('Kategorije ni mogoče izbrisati ker vsebuje ' + stKnjig + ' knjig.');
        return;
    }
    if (!confirm('Res želiš izbrisati to kategorijo?')) return;

    $.ajax({
        url: '../api/categories.php?id=' + id,
        method: 'DELETE',
        success: function() { naloadiKategorijeAdmin(); },
        error: function(xhr) { alert('Napaka: ' + xhr.responseJSON.napaka); }
    });
}

// Naročila (GET orders.php zahteva session)
function naloadiNarocilaAdmin() {
    $.getJSON('../api/orders.php', function(narocila) {
        var html = `
            <table class="table table-hover table-sm">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th><th>Ime</th><th>Email</th>
                        <th>Datum</th><th>Postavk</th>
                    </tr>
                </thead><tbody>
        `;

        // Zgradi vrstico za vsako naročilo — n = objekt naročila iz API-ja
        // StPostavk = COUNT iz API-ja — koliko knjig je v naročilu
        $.each(narocila, function(i, n) {
            html += `
                <tr>
                    <td>${n.OrderID}</td>
                    <td>${n.CustomerName}</td>
                    <td>${n.CustomerEmail}</td>
                    <td>${n.OrderDate}</td>
                    <td>${n.StPostavk}</td>
                </tr>
            `;
        });

        html += '</tbody></table>';
        $('#tabelaNarocil').html(html);

    // .fail() mora biti za closing }) getJSON callbacka
    // Sproži se če API vrne napako — npr. 403 ker admin ni prijavljen
    }).fail(function() {
        $('#tabelaNarocil').html(
            '<div class="alert alert-danger">Napaka pri nalaganju narocil.</div>'
        );
    });
}
</script>