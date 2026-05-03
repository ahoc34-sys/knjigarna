<?php
// Nastavi spremenljivke pred require_once — header.php jih pričakuje
$naslovStrani = 'Knjigarna — Domov';
$rootPath = '';
$cssPath = '';
$jsPath = '';
require_once 'includes/header.php';
?>

<!-- HERO SEKCIJA -->
<section class="hero">
    <div class="container">
        <h1>Dobrodošli v Knjigarni</h1>
        <p>Odkrijte 15 naslovov — od klasičnih romanov do sodobnih trilerjev.</p>
        <a href="books.php" class="btn btn-light btn-lg me-2">Brskaj po knjigah</a>
        <a href="books.php" class="btn btn-outline-light btn-lg">Po kategorijah</a>
    </div>
</section>

<!-- KATEGORIJE — prazen div, main.js ga napolni z AJAX klicem na api/categories.php -->
<section class="py-4 bg-white border-bottom">
    <div class="container">
        <h2 class="h5 mb-3">Kategorije</h2>
        <!-- id="kategorije" — naloadiKategorije() v main.js ga napolni s filter gumbi -->
        <div id="kategorije">
            <p class="text-muted">Nalagam kategorije...</p> <!-- dokler js ne naloži kategorij -->
        </div>
    </div>
</section>

<!-- NOVE KNJIGE -->
<main class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 mb-0">Nove knjige</h2>
            <a href="books.php" class="btn btn-sm btn-outline-primary">
                Vse knjige →
            </a>
        </div>

        <!-- id="knjige" — naloadiKnjige() v main.js ga napolni s karticami -->
        <div class="row g-3" id="knjige">
            <div class="col-12 text-center py-5">
                <!-- Spinner se prikaže takoj, JS ga zamenja z knjigami ko AJAX vrne podatke -->
                <div class="spinner-border text-primary" role="status"></div> 
                <p class="mt-2 text-muted">Nalagam knjige...</p>
            </div>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
<!-- Na dnu strani — skripte ne blokirajo nalaganja HTML -->
<!-- $(document).ready() v main.js najde #knjige - pokliče naloadiKnjige() in naloadiKategorije() -->