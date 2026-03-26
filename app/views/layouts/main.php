<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OntGraph — Visual Knowledge Engineering</title>
    <link rel="stylesheet" href="/public/css/style.css">
</head>
<body>
<header class="site-header">
    <a href="/" class="logo">
        <!-- Icône hexagone -->
        <svg width="34" height="34" viewBox="0 0 34 34" fill="none">
            <polygon points="17,1 31,9 31,25 17,33 3,25 3,9" stroke="#1de9b6" stroke-width="1.5" fill="none"/>
            <circle cx="17" cy="11" r="2.5" fill="#1de9b6"/>
            <circle cx="10" cy="22" r="2" fill="#00bcd4"/>
            <circle cx="24" cy="22" r="2" fill="#00bcd4"/>
            <line x1="17" y1="11" x2="10" y2="22" stroke="#1de9b6" stroke-width="1" opacity="0.7"/>
            <line x1="17" y1="11" x2="24" y2="22" stroke="#1de9b6" stroke-width="1" opacity="0.7"/>
            <line x1="10" y1="22" x2="24" y2="22" stroke="#00bcd4" stroke-width="0.8" opacity="0.5" stroke-dasharray="2,2"/>
        </svg>
        <div class="logo-text">
            <span class="logo-name">OntGraph</span>
            <span class="logo-tagline">Visual Knowledge Engineering</span>
        </div>
    </a>
    <nav>
        <a href="/">Accueil</a>
        <a href="/upload">Charger</a>
        <?php if (isset($_SESSION['ontology_name'])): ?>
            <a href="/ontology" class="active"><?= htmlspecialchars($_SESSION['ontology_name']) ?></a>
        <?php endif; ?>
    </nav>
</header>
<main>
    <?php require $viewFile; ?>
</main>
<footer class="site-footer">
    <div style="display:flex;align-items:center;gap:8px">
        <svg width="16" height="16" viewBox="0 0 34 34" fill="none">
            <polygon points="17,1 31,9 31,25 17,33 3,25 3,9" stroke="#1de9b6" stroke-width="1.5" fill="none" opacity="0.5"/>
            <circle cx="17" cy="11" r="2" fill="#1de9b6" opacity="0.5"/>
            <circle cx="10" cy="22" r="1.5" fill="#00bcd4" opacity="0.5"/>
            <circle cx="24" cy="22" r="1.5" fill="#00bcd4" opacity="0.5"/>
        </svg>
        <span>OntGraph &mdash; Visual Knowledge Engineering</span>
    </div>
    <span>OWL 2 / D3.js v7 / EasyRdf / PHP 8 MVC</span>
</footer>
<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/7.8.5/d3.min.js"></script>
<script src="/public/js/viz.js"></script>
</body>
</html>
