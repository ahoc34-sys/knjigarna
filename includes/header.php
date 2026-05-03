<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $naslovStrani ?? 'Knjigarna' ?></title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- $cssPath je '' za glavne strani, '../' za admin/index.php (je v podmapi) -->
    <link href="<?= $cssPath ?? '' ?>assets/css/style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
    <div class="container">

        <!-- Logotip -->
        <a class="navbar-brand" href="<?= $rootPath ?? '' ?>index.php">
            Knjigarna
        </a>

        <!-- Hamburger gumb za mobilne naprave -->
        <button class="navbar-toggler navbar-light bg-light" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navigacija">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigacijske povezave -->
        <div class="collapse navbar-collapse" id="navigacija">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>"
                       href="<?= $rootPath ?? '' ?>index.php">Domov</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'books.php' ? 'active' : '' ?>"
                       href="<?= $rootPath ?? '' ?>books.php">Knjige</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'cart.php' ? 'active' : '' ?>"
                       href="<?= $rootPath ?? '' ?>cart.php">
                        &#128722; Košarica
                        <span class="badge bg-danger" id="stKosarici">0</span>
                    </a>
                </li>
            </ul>
        </div>

    </div>
</nav>