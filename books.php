<?php
// Spremenljivke pred - isto kot pri index
$naslovStrani = 'Knjige — Knjigarna';
$rootPath = '';
$cssPath = '';
$jsPath = '';
require_once 'includes/header.php';
?>

<!-- NASLOV STRANI -->
<section class="bg-light text-dark py-4">
    <div class="container">
        <h1 class="h3 mb-0">Katalog knjig</h1>
    </div>
</section>

<!-- ISKANJE -->
<section class="bg-light py-3 border-bottom">
    <div class="container">
        <div class="row g-2">
            <div class="col-md-8">
                <!-- id="iskalnik" — jQuery ga targeta z event listenerji -->
                <input type="text"
                       id="iskalnik"
                       class="form-control"
                       placeholder="Išči po naslovu ali vsebini...">
            </div>
            <div class="col-md-4">
                <button class="btn btn-primary w-100" id="btnIskanje">
                    🔍 Išči
                </button>
            </div>
        </div>
    </div>
</section>

<!-- FILTER KATEGORIJ -->
 <!-- id="filtriKategorij" — naloadiKategorije() iz main.js ga napolni -->
<!-- Drugačen id od index (#kategorije) — zato main.js targeta oba: $('#kategorije, #filtriKategorij') -->
<section class="py-3 bg-white border-bottom">
    <div class="container">
        <div id="filtriKategorij">
            <p class="text-muted mb-0">Nalagam kategorije...</p>
        </div>
    </div>
</section>

<!-- SEZNAM KNJIG -->
<main class="py-5">
    <div class="container">
        <!-- id="steviloRezultatov" — JS ga napolni z "Najdenih knjig: 15", prvo prazen -->
        <p class="text-muted mb-3" id="steviloRezultatov"></p>
        <div class="row g-3" id="knjige">
            <!-- Spinner se prikaže takoj, JS ga zamenja ko AJAX vrne podatke -->
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2 text-muted">Nalagam knjige...</p>
            </div>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>

<script>
// Globalna spremenljivka — dostopna vsem funkcijam v tem script bloku
// Znotraj $(document).ready() bi bila lokalna — naloziBooksPage() je ne bi videla
var aktivnaKategorija = null;

$(document).ready(function() {

    // URLSearchParams razčleni GET parametre iz URL-ja
    // window.location.search vrne del URL-ja za ? (npr. ?kategorija=4)
    var urlParams = new URLSearchParams(window.location.search);
    aktivnaKategorija = urlParams.get('kategorija');
    var iskanjeIzUrl = urlParams.get('iskanje') || '';

    // Nastavi vrednost iskalnika če je iskanje v URL-ju
    if (iskanjeIzUrl) {
        $('#iskalnik').val(iskanjeIzUrl);
    }

    // Naloži kategorije iz main.js — označi aktivno kategorijo če je v URL-ju
    naloadiKategorije(aktivnaKategorija);

    // Delegiran event listener — .filter-pill elementi še ne obstajajo ob nalaganju
    // JS jih doda dinamično - direktni $('.filter-pill').on('click') ne bi deloval
    // $(document).on() ujame klike na elementih ki nastanejo kasneje
    $(document).on('click', '.filter-pill', function(e) {
    e.preventDefault();
    var href = $(this).attr('href'); // prepreči da brskalnik sledi href linku

    // Preberi kategorijo iz href
    var params = new URLSearchParams(href.split('?')[1] || ''); // split('?')[1] vzame del URL-ja za ?
    aktivnaKategorija = params.get('kategorija');

    $('.filter-pill').removeClass('aktiven'); // z vseh
    $(this).addClass('aktiven'); // samo kliknjenemu

    $('#iskalnik').val(''); // počisti iskalnik
    naloziBooksPage(aktivnaKategorija, '');
});

    // Naloži knjige ob prvem obisku strani
    naloziBooksPage(aktivnaKategorija, iskanjeIzUrl);

    // Klik na gumb Išči
    $('#btnIskanje').on('click', function() {
        var iskanje = $('#iskalnik').val().trim();
        aktivnaKategorija = null;
        naloziBooksPage(null, iskanje);
        naloadiKategorije(null); // posodobi filter gumbe
    });

    // Enter v iskalnem polju - ne podvaja kode
    $('#iskalnik').on('keyup', function(e) {
        if (e.key === 'Enter') {
            $('#btnIskanje').click();
        }
    });

});

// Naloži knjige za books.php (z iskanjem in filtrom)
function naloziBooksPage(katID, iskanje) {
    var url = 'api/books.php?';
    // Dinamično gradi URL — samo doda parameter če je podan
    if (katID) url += 'kategorija=' + katID + '&';
    // encodeURIComponent kodira šumnike za URL (č - %C4%8D) — brez tega neveljaven URL
    if (iskanje) url += 'iskanje=' + encodeURIComponent(iskanje);

    // spinner
    $('#knjige').html(`
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status"></div>
        </div>
    `);

    $.getJSON(url, function(knjige) {
        $('#steviloRezultatov').text('Najdenih knjig: ' + knjige.length);

        if (knjige.length === 0) {
            $('#knjige').html(
                '<div class="col-12 text-center text-muted py-4">' +
                'Ni knjig za ta iskalni pogoj.</div>'
            );
            return; // ustavi izv.
        }

        // Zgradi cel HTML string in ga vstavi naenkrat — bolj učinkovito
        // zgradiKartico() pride iz main.js — dostopna ker je main.js naložen prej
        var html = '';
        $.each(knjige, function(i, knjiga) {
            html += zgradiKartico(knjiga);
        });
        $('#knjige').html(html);
    });
}
</script>