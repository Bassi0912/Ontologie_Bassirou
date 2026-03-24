<section class="hero">
    <h1>Visualisez vos<br><em>Ontologies OWL 2</em></h1>
    <p>Explorez la hiérarchie de classes, les propriétés et les relations de vos ontologies avec des visualisations interactives D3.js.</p>
    <div class="hero-actions">
        <a href="/upload" class="btn btn-primary">Charger une ontologie</a>
        <?php if ($hasFile): ?>
            <a href="/ontology" class="btn btn-secondary">Visualiser l'ontologie courante</a>
        <?php endif; ?>
    </div>
    <div class="hero-features">
        <div class="feature">
            <div class="feature-icon">◎</div>
            <h3>Radiale</h3>
            <p>Vue centrée sur un concept, avec ses descendants en anneaux concentriques.</p>
        </div>
        <div class="feature">
            <div class="feature-icon">⬡</div>
            <h3>Coupe</h3>
            <p>Hiérarchie en cercles imbriqués, zoomable.</p>
        </div>
        <div class="feature">
            <div class="feature-icon">⟶</div>
            <h3>Progressive</h3>
            <p>Graphe de force avec relations nommées et hiérarchie.</p>
        </div>
        <div class="feature">
            <div class="feature-icon">🌳</div>
            <h3>Arbre</h3>
            <p>Arbre effondrable, navigable niveau par niveau.</p>
        </div>
    </div>
</section>
