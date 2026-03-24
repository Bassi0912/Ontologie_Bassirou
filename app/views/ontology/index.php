<div class="viz-app">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <h2 class="sidebar-title">Concepts</h2>
        <input type="text" id="searchConcept" placeholder="Rechercher..." class="search-input">
        <ul class="concept-list" id="conceptList">
            <?php foreach ($classes as $c): ?>
                <li class="concept-item" 
                    data-uri="<?= htmlspecialchars($c['uri']) ?>"
                    data-id="<?= htmlspecialchars($c['id']) ?>"
                    title="<?= htmlspecialchars($c['comment']) ?>">
                    <?= htmlspecialchars($c['label']) ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <h2 class="sidebar-title mt">Propriétés</h2>
        <ul class="concept-list" id="propList">
            <?php foreach ($props as $p): ?>
                <li class="prop-item"
                    data-uri="<?= htmlspecialchars($p['uri']) ?>"
                    data-id="<?= htmlspecialchars($p['id']) ?>"
                    title="<?= htmlspecialchars($p['comment']) ?>">
                    <?= htmlspecialchars($p['label']) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </aside>

    <!-- Main viz area -->
    <div class="viz-main">
        <!-- Toolbar -->
        <div class="toolbar">
            <div class="viz-tabs">
                <button class="tab-btn active" data-viz="radial">◎ Radiale</button>
                <button class="tab-btn" data-viz="packing">⬡ Coupe</button>
                <button class="tab-btn" data-viz="progressive">⟶ Progressive</button>
                <button class="tab-btn" data-viz="tree">🌳 Arbre</button>
                <button class="tab-btn" data-viz="sunburst">☀ Sunburst</button>
            </div>
            <div class="toolbar-right">
                <label>Profondeur: <input type="range" id="depthSlider" min="1" max="6" value="3"> <span id="depthVal">3</span></label>
                <button id="btnReset" class="btn-icon" title="Réinitialiser">↺</button>
                <button id="btnFullscreen" class="btn-icon" title="Plein écran">⤢</button>
            </div>
        </div>

        <!-- Breadcrumb -->
        <div class="breadcrumb" id="breadcrumb">
            <span class="bc-item" data-uri="root">Racine</span>
        </div>

        <!-- SVG canvas -->
        <div class="canvas-container" id="vizContainer">
            <svg id="vizSvg"></svg>
        </div>

        <!-- Info panel -->
        <div class="info-panel" id="infoPanel" style="display:none">
            <button class="info-close" id="infoClose">×</button>
            <h3 id="infoTitle"></h3>
            <p id="infoComment"></p>
            <div id="infoProps"></div>
        </div>
    </div>
</div>

<script>
window.ONTOVIZ_CLASSES = <?= json_encode($classes) ?>;
window.ONTOVIZ_PROPS   = <?= json_encode($props) ?>;
</script>
