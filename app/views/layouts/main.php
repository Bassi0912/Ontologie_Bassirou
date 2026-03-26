<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Sémantiqua — Visualisation d'Ontologies OWL 2</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Raleway:wght@300;400;600&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/public/css/style.css">
</head>
<body>
<header class="site-header">
    <a href="/" class="logo">
        <!-- Logo W S entrelacés -->
        <svg width="38" height="38" viewBox="0 0 38 38" fill="none">
            <!-- Cercle externe -->
            <circle cx="19" cy="19" r="17" stroke="#1de9b6" stroke-width="1.2" fill="none"/>
            <!-- Arc décoratif intérieur -->
            <circle cx="19" cy="19" r="12" stroke="#1de9b6" stroke-width="0.5" fill="none" opacity="0.3"/>
            <!-- Nœuds du graphe -->
            <circle cx="19" cy="7"  r="2.5" fill="#1de9b6"/>
            <circle cx="9"  cy="25" r="2"   fill="#00bcd4"/>
            <circle cx="29" cy="25" r="2"   fill="#00bcd4"/>
            <circle cx="19" cy="19" r="1.5" fill="#f5a623"/>
            <!-- Liens -->
            <line x1="19" y1="7"  x2="19" y2="19" stroke="#1de9b6" stroke-width="1"   opacity="0.8"/>
            <line x1="19" y1="19" x2="9"  y2="25" stroke="#00bcd4" stroke-width="0.8" opacity="0.7"/>
            <line x1="19" y1="19" x2="29" y2="25" stroke="#00bcd4" stroke-width="0.8" opacity="0.7"/>
            <line x1="9"  y1="25" x2="29" y2="25" stroke="#1de9b6" stroke-width="0.6" stroke-dasharray="2,2" opacity="0.4"/>
            <!-- Points décoratifs -->
            <circle cx="29" cy="9"  r="1.2" fill="#1de9b6" opacity="0.5"/>
            <circle cx="9"  cy="9"  r="1.2" fill="#1de9b6" opacity="0.5"/>
            <circle cx="4"  cy="19" r="1"   fill="#00bcd4" opacity="0.4"/>
            <circle cx="34" cy="19" r="1"   fill="#00bcd4" opacity="0.4"/>
        </svg>
        <div class="logo-text">
            <span class="logo-name">Web Sémantiqua</span>
            <span class="logo-tagline">Visualisation d'Ontologies OWL 2</span>
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
        <svg width="16" height="16" viewBox="0 0 38 38" fill="none">
            <circle cx="19" cy="19" r="17" stroke="#1de9b6" stroke-width="1.2" fill="none" opacity="0.4"/>
            <circle cx="19" cy="7" r="2" fill="#1de9b6" opacity="0.5"/>
            <circle cx="9" cy="25" r="1.5" fill="#00bcd4" opacity="0.5"/>
            <circle cx="29" cy="25" r="1.5" fill="#00bcd4" opacity="0.5"/>
            <line x1="19" y1="7" x2="19" y2="19" stroke="#1de9b6" stroke-width="1" opacity="0.4"/>
            <line x1="19" y1="19" x2="9" y2="25" stroke="#00bcd4" stroke-width="0.8" opacity="0.4"/>
            <line x1="19" y1="19" x2="29" y2="25" stroke="#00bcd4" stroke-width="0.8" opacity="0.4"/>
            <circle cx="19" cy="19" r="1.2" fill="#f5a623" opacity="0.5"/>
        </svg>
        <span style="font-family:'Cinzel',serif;font-size:.65rem;letter-spacing:.06em">Web Sémantiqua</span>
    </div>
    <span>OWL 2 &mdash; D3.js v7 &mdash; EasyRdf &mdash; PHP 8 MVC</span>
</footer>
<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/7.8.5/d3.min.js"></script>
<script src="/public/js/viz.js"></script>
</body>
</html>
