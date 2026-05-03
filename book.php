<?php
$naslovStrani = 'Knjiga — Knjigarna'; // posodobi JS na ime knjige: document.title = knjiga.Name
$rootPath = '';
$cssPath = '';
$jsPath = '';
require_once 'includes/header.php';
?>

<main class="py-5">
    <div class="container">

        <!-- Nazaj gumb -->
        <a href="books.php" class="btn btn-outline-secondary btn-sm mb-4">
               Nazaj na knjige
        </a>

        <!-- Vsebina knjige - napolni JS -->
        <div id="knjigaVsebina">
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2 text-muted">Nalagam...</p>
            </div>
        </div>

    </div>
</main>

<?php require_once 'includes/footer.php'; ?>

<script>
$(document).ready(function() {

    // Preberi id knjige iz URL
    var urlParams = new URLSearchParams(window.location.search);
    var id = urlParams.get('id'); //null

    // če ni podan - napaka + ustavi
    if (!id) {
        $('#knjigaVsebina').html(
            '<div class="alert alert-danger">Knjiga ni določena.</div>'
        );
        return; // ustavi $(document).ready() — ne nadaljujemo brez ID-ja
    }

    // AJAX GET na api/books.php?id=5 — vrne eno knjigo z vsemi podatki vključno s Content
    $.getJSON('api/books.php?id=' + id, function(knjiga) {

        // Posodobi naslov zavihka (ime knjige)
        document.title = knjiga.Name + ' — Knjigarna';

        // Ternary — če ima sliko sestavi pot, sicer privzeta SVG
        var slika = knjiga.BookCover
            ? 'assets/uploads/' + knjiga.BookCover
            : 'assets/images/no-cover.svg';

        // Zgradi in vstavi cel HTML naenkrat
        // || '' — če je Description null ne prikaže "null" 
        // Content je polni opis — vrne ga samo enaknjiga(), vseknjige() ga izpusti
        // dodajVKosarico() pride iz main.js
        $('#knjigaVsebina').html(`
            <div class="row g-4">

                <!-- Slika -->
                <div class="col-md-3">
                    <img src="${slika}"
                         alt="${knjiga.Name}"
                         class="img-fluid rounded shadow">
                </div>

                <!-- Podatki -->
                <div class="col-md-9">
                    <span class="badge bg-secondary mb-2">
                        ${knjiga.CategoryTitle}
                    </span>
                    <h1 class="h2">${knjiga.Name}</h1>
                    <p class="text-muted mb-3">✍️ ${knjiga.Author}</p>
                    <p class="lead">${knjiga.Description || ''}</p>
                    <hr>
                    <p>${knjiga.Content || ''}</p> 

                    <button class="btn btn-primary btn-lg mt-3"
                            onclick="dodajVKosarico(${knjiga.BookID}, '${knjiga.Name}')">
                        🛒 Dodaj v košarico
                    </button>
                </div>

            </div>
        `);

    // .fail() — izvede se če AJAX klic ne uspe (npr. 404 — knjiga ne obstaja)
    // sporočilo namesto prazne strani
    }).fail(function() {
        $('#knjigaVsebina').html(
            '<div class="alert alert-danger">Knjiga ni najdena.</div>'
        );
    });

});
</script>