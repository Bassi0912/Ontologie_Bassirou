<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OntoViz — Visualiseur d'Ontologies OWL</title>
    <link rel="stylesheet" href="/public/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Syne:wght@400;600;800&display=swap" rel="stylesheet">
</head>
<body>
    <header class="site-header">
        <a href="/" class="logo">OntoViz<span class="logo-dot">.</span></a>
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
        <span>OntoViz &mdash; OWL 2 / D3.js / EasyRdf / PHP 8 MVC</span>
    </footer>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/7.8.5/d3.min.js"></script>
    <script src="/public/js/viz.js"></script>
</body>
</html>
