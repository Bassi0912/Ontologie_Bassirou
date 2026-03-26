<section class="hero">
    <div class="hero-eyebrow">Visual Knowledge Engineering</div>
    <h1>Explorez vos<br>ontologies OWL 2<br><em>visuellement.</em></h1>
    <p class="hero-desc">OntGraph transforme vos fichiers RDF/OWL en visualisations interactives. Hiérarchies, propriétés, relations — tout devient lisible.</p>
    <div class="hero-actions">
        <a href="/upload" class="btn btn-primary">Charger une ontologie</a>
        <?php if ($hasFile): ?>
            <a href="/ontology" class="btn btn-secondary">Continuer la session</a>
        <?php endif; ?>
    </div>
    <div class="hero-features">
        <div class="feature">
            <div class="feature-icon">◎</div>
            <h3>Radiale</h3>
            <p>Vue centrée sur un concept. Ses descendants s'organisent en anneaux concentriques colorés.</p>
        </div>
        <div class="feature">
            <div class="feature-icon">⬡</div>
            <h3>Coupe</h3>
            <p>Hiérarchie en cercles imbriqués (circle packing). Zoomez sur n'importe quel nœud.</p>
        </div>
        <div class="feature">
            <div class="feature-icon">⟶</div>
            <h3>Progressive</h3>
            <p>Graphe de force avec relations nommées orientées. Drag & drop interactif.</p>
        </div>
        <div class="feature">
            <div class="feature-icon">🌳</div>
            <h3>Arbre</h3>
            <p>Arbre effondrable, niveau par niveau. Naviguez dans la hiérarchie en un clic.</p>
        </div>
        <div class="feature">
            <div class="feature-icon">☀</div>
            <h3>Sunburst</h3>
            <p>Diagramme solaire par niveaux. Taille proportionnelle à la profondeur du sous-arbre.</p>
        </div>
    </div>
</section>
