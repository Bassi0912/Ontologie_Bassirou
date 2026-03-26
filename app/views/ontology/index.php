<div class="viz-app">
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <svg width="20" height="20" viewBox="0 0 34 34" fill="none">
                    <polygon points="17,1 31,9 31,25 17,33 3,25 3,9" stroke="#1de9b6" stroke-width="1.5" fill="none"/>
                    <circle cx="17" cy="11" r="2" fill="#1de9b6"/>
                    <circle cx="10" cy="22" r="1.5" fill="#00bcd4"/>
                    <circle cx="24" cy="22" r="1.5" fill="#00bcd4"/>
                    <line x1="17" y1="11" x2="10" y2="22" stroke="#1de9b6" stroke-width="1"/>
                    <line x1="17" y1="11" x2="24" y2="22" stroke="#1de9b6" stroke-width="1"/>
                </svg>
                <div>
                    <div class="sidebar-logo-name" style="font-family:'Cinzel',serif;font-size:.9rem">Web Sémantiqua</div>
                    <div class="sidebar-logo-tag">Explorateur de connaissances</div>
                </div>
            </div>
        </div>
        <input type="text" id="searchConcept" placeholder="Rechercher un concept…" class="search-input">
        <div class="sidebar-title">Concepts (<?= count($classes) ?>)</div>
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
        <div class="sidebar-title mt">Propriétés (<?= count($props) ?>)</div>
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

    <div class="viz-main">
        <div class="toolbar">
            <div class="viz-tabs">
                <button class="tab-btn active" data-viz="radial">◎ Radiale</button>
                <button class="tab-btn" data-viz="packing">⬡ Coupe</button>
                <button class="tab-btn" data-viz="progressive">⟶ Progressive</button>
                <button class="tab-btn" data-viz="tree">🌳 Arbre</button>
                <button class="tab-btn" data-viz="sunburst">☀ Sunburst</button>
            </div>
            <div class="toolbar-right">
                <label style="display:flex;align-items:center;gap:6px">
                    Profondeur
                    <input type="range" id="depthSlider" min="1" max="6" value="3">
                    <span id="depthVal">3</span>
                </label>
                <button id="btnReset" class="btn-icon" title="Réinitialiser">↺</button>
                <button id="btnFullscreen" class="btn-icon" title="Plein écran">⤢</button>
            </div>
        </div>
        <div class="breadcrumb" id="breadcrumb">
            <span class="bc-item" data-uri="root">Racine</span>
        </div>
        <div class="canvas-container" id="vizContainer">
            <svg id="vizSvg"></svg>
        </div>
        <div class="info-panel" id="infoPanel" style="display:none">
            <button class="info-close" id="infoClose">×</button>
            <div class="info-eyebrow">Concept</div>
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
