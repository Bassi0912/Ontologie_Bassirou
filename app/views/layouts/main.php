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
        <svg width="36" height="36" viewBox="0 0 36 36" fill="none">
            <!-- Cercle externe -->
            <circle cx="18" cy="18" r="16" stroke="#a78bfa" stroke-width="1.2" fill="none"/>
            <!-- Cercle intérieur décoratif -->
            <circle cx="18" cy="18" r="10" stroke="#a78bfa" stroke-width="0.4" fill="none" opacity="0.25"/>
            <!-- Nœuds -->
            <circle cx="18" cy="6"  r="2.5" fill="#a78bfa"/>
            <circle cx="8"  cy="24" r="2"   fill="#c4b5fd"/>
            <circle cx="28" cy="24" r="2"   fill="#c4b5fd"/>
            <circle cx="18" cy="18" r="1.5" fill="#f472b6"/>
            <!-- Liens -->
            <line x1="18" y1="6"  x2="18" y2="18" stroke="#a78bfa" stroke-width="1"   opacity="0.8"/>
            <line x1="18" y1="18" x2="8"  y2="24" stroke="#c4b5fd" stroke-width="0.8" opacity="0.7"/>
            <line x1="18" y1="18" x2="28" y2="24" stroke="#c4b5fd" stroke-width="0.8" opacity="0.7"/>
            <line x1="8"  y1="24" x2="28" y2="24" stroke="#a78bfa" stroke-width="0.5" stroke-dasharray="2,2" opacity="0.4"/>
            <!-- Points périphériques -->
            <circle cx="28" cy="8"  r="1.1" fill="#a78bfa" opacity="0.45"/>
            <circle cx="8"  cy="8"  r="1.1" fill="#a78bfa" opacity="0.45"/>
            <circle cx="3"  cy="18" r="0.9" fill="#c4b5fd" opacity="0.35"/>
            <circle cx="33" cy="18" r="0.9" fill="#c4b5fd" opacity="0.35"/>
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
        <svg width="16" height="16" viewBox="0 0 36 36" fill="none">
            <circle cx="18" cy="18" r="16" stroke="#a78bfa" stroke-width="1.2" fill="none" opacity="0.4"/>
            <circle cx="18" cy="6"  r="2"   fill="#a78bfa" opacity="0.5"/>
            <circle cx="8"  cy="24" r="1.5" fill="#c4b5fd" opacity="0.5"/>
            <circle cx="28" cy="24" r="1.5" fill="#c4b5fd" opacity="0.5"/>
            <circle cx="18" cy="18" r="1.2" fill="#f472b6" opacity="0.5"/>
            <line x1="18" y1="6"  x2="18" y2="18" stroke="#a78bfa" stroke-width="0.8" opacity="0.4"/>
            <line x1="18" y1="18" x2="8"  y2="24" stroke="#c4b5fd" stroke-width="0.7" opacity="0.4"/>
            <line x1="18" y1="18" x2="28" y2="24" stroke="#c4b5fd" stroke-width="0.7" opacity="0.4"/>
        </svg>
        <span style="font-family:'Cinzel',serif;font-size:.62rem;letter-spacing:.05em">Web Sémantiqua</span>
    </div>
    <span>OWL 2 &mdash; D3.js v7 &mdash; EasyRdf &mdash; PHP 8 MVC</span>
</footer>
<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/7.8.5/d3.min.js"></script>
<script src="/public/js/viz.js"></script>
</body>
</html>
