<footer class="text-dark mt-5 py-4">
    <div class="container">
        <div class="row"> <!-- Na mobilnih - samodejno postavijo eden pod drugega -->

            <div class="col-md-4 mb-3">
                <h5 class="fw-bold">Knjigarna</h5>
                <p class="small" style="color: #b0a0cc;">
                    Vaša zanesljiva spletna knjigarna.
                    Knjige za vsakogar.
                </p>
                <p class="small mb-0" style="color: #b0a0cc;">
                    &copy; <?= date('Y') ?> Knjigarna. Vse pravice pridržane.
                </p>
            </div>

            <div class="col-md-4 mb-3">
                <h6 class="fw-bold">Hitre povezave</h6>
                <ul class="list-unstyled small">
                    <!-- $rootPath '' za glavne strani, '../' za admin/index.php -->
                    <li><a href="<?= $rootPath ?? '' ?>index.php">Domov</a></li>
                    <li><a href="<?= $rootPath ?? '' ?>books.php">Knjige</a></li>
                    <li><a href="<?= $rootPath ?? '' ?>cart.php">Košarica</a></li>
                </ul>
            </div>

            <div class="col-md-4 mb-3">
                <h6 class="fw-bold">Kontakt</h6>
                <ul class="list-unstyled small">
                    <li>info@knjigarna.si</li>
                    <li>01 234 567</li>
                    <li>Random ulica 1, Ljubljana</li>
                </ul>
            </div>

        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery — mora biti pred main.js ker main.js ga uporablja ($. funkcije) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- JS - jsPath je '' za glavne strani, '../' za admin -->
<script src="<?= $jsPath ?? '' ?>assets/js/main.js"></script>
</body>
</html>