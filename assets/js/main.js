// Pomožna funkcija — sprejme JSON objekt knjige, vrne HTML string kartice
function zgradiKartico(knjiga) {
    var opis = knjiga.Description || '';
    if (opis.length > 50) {
        opis = opis.substring(0, 50) + '...';
    }
    // Ternary operator — če ima knjiga sliko prikaži jo, sicer privzeto SVG
    var slika = knjiga.BookCover
        ? 'assets/uploads/' + knjiga.BookCover
        : 'assets/images/no-cover.svg'; // privzeta slika če BookCover je null

    // Template literal (backtick ``) — omogoča večvrstični string in ${} za vstavljanje vrednosti *imej slo tipkovnico
    return `
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="knjiga-kartica">
                <a href="book.php?id=${knjiga.BookID}">
                    <div class="knjiga-slika-wrap">
                        <img src="${slika}" alt="${knjiga.Name}">
                        <span class="knjiga-avtor-badge">${knjiga.Author}</span>
                    </div>
                    <div class="knjiga-vsebina">
                        <div class="knjiga-naslov">${knjiga.Name}</div>
                        <div class="knjiga-opis">${opis}</div>
                    </div>
                </a>
                <div class="px-3 pb-3">
                    <button class="btn btn-sm btn-primary w-100"
                            onclick="dodajVKosarico(${knjiga.BookID}, '${knjiga.Name}')">
                        + Dodaj v košarico
                    </button>
                </div>
            </div>
        </div>
    `;
}

// Naloži kategorije iz API-ja in zgradi filter gumbe
function naloadiKategorije(aktivnaID) {
    // $.getJSON — jQuery AJAX GET zahteva ki samodejno razčleni JSON odgovor
    $.getJSON('api/categories.php', function(kategorije) {
        // Gumb "Vse" — aktiven če ni izbrana nobena kategorija (!aktivnaID)
        var html = '<a href="books.php" class="filter-pill ' +
                   (!aktivnaID ? 'aktiven' : '') + '">Vse</a>';

        // $.each — jQuery zanka čez polje, i = indeks, kat = element
        $.each(kategorije, function(i, kat) {
            // == namerno — aktivnaID je string iz URL, BookCategoryID je number
            var aktiven = (aktivnaID == kat.BookCategoryID) ? 'aktiven' : '';
            html += `<a href="books.php?kategorija=${kat.BookCategoryID}"
                        class="filter-pill ${aktiven}">
                        ${kat.Title}
                        <small>(${kat.StKnjig})</small>
                     </a>`;
        });
        // Posodobi oba elementa — #kategorije na index.php, #filtriKategorij na books.php
        $('#kategorije, #filtriKategorij').html(html);
    });
}

// Naloži knjige iz API-ja in jih prikaže — pokliče zgradiKartico() za vsako
function naloadiKnjige(url) {
    $('#knjige').html(`
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status"></div> 
            <p class="mt-2 text-muted">Nalagam...</p>
        </div>
    `); // spinner med nalaganjem

    $.getJSON(url, function(knjige) {
        if (knjige.length === 0) { // Če ni rezultatov
            $('#knjige').html(
                '<div class="col-12 text-center text-muted py-4">Ni knjig.</div>'
            );
            return;
        }
        // Zgradi HTML za vse knjige in jih vstavi naenkrat — bolše od vstavljanja ene po ene
        var html = '';
        $.each(knjige, function(i, knjiga) {
            html += zgradiKartico(knjiga); // doda HTML kartice na konec stringa
        });
        $('#knjige').html(html); // .html() zamenja vsebino elementa z novim HTML
    });
}

// Košarica 
// localStorage shranjuje podatke v brskalniku — ostanejo tudi po zaprtju
function preberiKosarico() {
    var kosarica = localStorage.getItem('kosarica'); //prebere
    // Če obstaja — razčleni JSON string v JS objekt, sicer vrni prazen array
    return kosarica ? JSON.parse(kosarica) : [];
}

function shraniKosarico(kosarica) {
    // JSON.stringify pretvori JS objekt v string — localStorage shrani samo stringe
    localStorage.setItem('kosarica', JSON.stringify(kosarica));
    posodobiStevec();
}

function posodobiStevec() {
    var kosarica = preberiKosarico();
    var skupaj = 0;
    // Sešteje vse količine — prikaže skupno število artiklov ne samo vrst knjig
    $.each(kosarica, function(i, item) {
        skupaj += item.kolicina;
    });
    $('#stKosarici').text(skupaj); // posodobi badge v navigaciji
}

function dodajVKosarico(bookID, naslov) {
    var kosarica = preberiKosarico();
    var obstaja = false;

    $.each(kosarica, function(i, item) {
        if (item.bookID === bookID) {
            item.kolicina++;
            obstaja = true;
            return false; // return false v $.each = break — ustavi zanko
        }
    });

    if (!obstaja) {
        // Knjige še ni — dodaj nov objekt v array
        kosarica.push({ bookID: bookID, naslov: naslov, kolicina: 1 });
    }

    shraniKosarico(kosarica);
    alert('"' + naslov + '" dodano v košarico!');
}

// Inicializacija ob nalaganju strani 
// $(document).ready() — izvede kodo šele ko je HTML popolnoma naložen
// Brez tega bi JS poskušal manipulirati elemente ki še ne obstajajo
$(document).ready(function() {
    posodobiStevec(); // badge košarice

    // $('#knjige').length — preveri ali element z id="knjige" sploh obstaja na tej strani
    // Brez tega bi JS poskušal naložiti knjige na vsaki strani (tudi cart.php, book.php...)
    if ($('#knjige').length) {
        naloadiKategorije(null); // nobena ni aktivna
        naloadiKnjige('api/books.php'); // naloži vse knjige
    }
});