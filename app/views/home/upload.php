<section class="upload-page">
    <h2>Charger une ontologie</h2>
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-error">
            <?php
            $msgs = [
                'no_file'       => 'Aucun fichier sélectionné.',
                'bad_format'    => 'Format non supporté. Utilisez .rdf, .owl, .ttl, .n3, .nt ou .jsonld.',
                'upload_failed' => 'Erreur lors de l\'upload.',
            ];
            echo $msgs[$_GET['error']] ?? 'Erreur inconnue.';
            ?>
        </div>
    <?php endif; ?>
    <form method="POST" action="/upload" enctype="multipart/form-data" class="upload-form">
        <label class="file-drop" id="dropzone">
            <input type="file" name="ontology" id="fileInput" accept=".rdf,.owl,.xml,.ttl,.n3,.nt,.jsonld">
            <div class="drop-content">
                <div class="drop-icon">📂</div>
                <p>Glissez-déposez votre fichier OWL/RDF ici</p>
                <small>ou cliquez pour sélectionner</small>
                <div id="fileName" class="file-name"></div>
            </div>
        </label>
        <p class="formats">Formats acceptés : <code>.rdf</code> <code>.owl</code> <code>.ttl</code> <code>.n3</code> <code>.nt</code> <code>.jsonld</code></p>
        <button type="submit" class="btn btn-primary">Charger et visualiser</button>
    </form>
    <div class="demo-section">
        <h3>Exemple : humans.rdfs</h3>
        <form method="POST" action="/upload" enctype="multipart/form-data">
            <input type="hidden" name="use_demo" value="1">
            <button type="submit" class="btn btn-secondary" onclick="loadDemo(event)">Charger l'exemple fourni</button>
        </form>
    </div>
</section>
<script>
document.getElementById('fileInput').addEventListener('change', function() {
    document.getElementById('fileName').textContent = this.files[0]?.name || '';
});
function loadDemo(e) {
    e.preventDefault();
    fetch('/public/assets/humans.rdf')
      .then(r => r.blob())
      .then(blob => {
        const form = new FormData();
        form.append('ontology', new File([blob], 'humans.rdf', {type:'application/rdf+xml'}));
        return fetch('/upload', {method:'POST', body: form});
      })
      .then(r => { if(r.redirected) window.location = r.url; });
}
</script>
