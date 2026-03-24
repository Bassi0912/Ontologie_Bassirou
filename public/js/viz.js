/* OntoViz - D3.js Visualizations */
(function () {
    if (!document.getElementById('vizSvg')) return; // not on viz page

    // ── State ────────────────────────────────────────────────────────────────
    const state = {
        currentViz: 'radial',
        currentRoot: null,       // URI or null
        currentRootLabel: 'Racine',
        depth: 3,
        history: [],             // breadcrumb stack [{uri, label}]
    };

    const COLOR_PALETTE = [
        '#7ee8a2','#5bc8f5','#f5a623','#e056fd','#ff6b6b',
        '#48dbfb','#ff9ff3','#feca57','#54a0ff','#5f27cd'
    ];

    // ── DOM refs ──────────────────────────────────────────────────────────────
    const svg         = d3.select('#vizSvg');
    const container   = document.getElementById('vizContainer');
    const infoPanel   = document.getElementById('infoPanel');
    const infoTitle   = document.getElementById('infoTitle');
    const infoComment = document.getElementById('infoComment');
    const infoProps   = document.getElementById('infoProps');
    const breadcrumb  = document.getElementById('breadcrumb');
    const depthSlider = document.getElementById('depthSlider');
    const depthVal    = document.getElementById('depthVal');

    function W() { return container.clientWidth; }
    function H() { return container.clientHeight; }

    // ── Tabs ──────────────────────────────────────────────────────────────────
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            state.currentViz = btn.dataset.viz;
            render();
        });
    });

    // ── Depth slider ──────────────────────────────────────────────────────────
    depthSlider.addEventListener('input', () => {
        state.depth = +depthSlider.value;
        depthVal.textContent = state.depth;
        render();
    });

    // ── Reset & Fullscreen ────────────────────────────────────────────────────
    document.getElementById('btnReset').addEventListener('click', () => {
        state.currentRoot = null;
        state.currentRootLabel = 'Racine';
        state.history = [];
        updateBreadcrumb();
        render();
    });
    document.getElementById('btnFullscreen').addEventListener('click', () => {
        container.requestFullscreen?.();
    });

    // ── Sidebar clicks ────────────────────────────────────────────────────────
    document.querySelectorAll('.concept-item').forEach(li => {
        li.addEventListener('click', () => {
            const uri = li.dataset.uri;
            const label = li.textContent.trim();
            navigateTo(uri, label);
        });
    });
    document.querySelectorAll('.prop-item').forEach(li => {
        li.addEventListener('click', () => {
            const uri = li.dataset.uri;
            const label = li.textContent.trim();
            state.currentViz = 'progressive';
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.toggle('active', b.dataset.viz === 'progressive');
            });
            navigateTo(uri, label);
        });
    });

    // Search
    document.getElementById('searchConcept').addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.concept-item, .prop-item').forEach(li => {
            li.style.display = li.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });

    // ── Info close ────────────────────────────────────────────────────────────
    document.getElementById('infoClose').addEventListener('click', () => {
        infoPanel.style.display = 'none';
    });

    // ── Navigation ────────────────────────────────────────────────────────────
    function navigateTo(uri, label) {
        if (state.currentRoot) {
            state.history.push({ uri: state.currentRoot, label: state.currentRootLabel });
        }
        state.currentRoot = uri;
        state.currentRootLabel = label;
        updateBreadcrumb();
        render();
    }

    function updateBreadcrumb() {
        breadcrumb.innerHTML = '';
        const rootSpan = document.createElement('span');
        rootSpan.className = 'bc-item';
        rootSpan.textContent = 'Racine';
        rootSpan.addEventListener('click', () => {
            state.currentRoot = null;
            state.currentRootLabel = 'Racine';
            state.history = [];
            updateBreadcrumb();
            render();
        });
        breadcrumb.appendChild(rootSpan);

        state.history.forEach((h, i) => {
            const sep = document.createElement('span');
            sep.textContent = ' › ';
            sep.style.color = 'var(--muted)';
            breadcrumb.appendChild(sep);
            const s = document.createElement('span');
            s.className = 'bc-item';
            s.textContent = h.label;
            s.addEventListener('click', () => {
                state.currentRoot = h.uri;
                state.currentRootLabel = h.label;
                state.history = state.history.slice(0, i);
                updateBreadcrumb();
                render();
            });
            breadcrumb.appendChild(s);
        });

        if (state.currentRoot) {
            const sep = document.createElement('span');
            sep.textContent = ' › ';
            sep.style.color = 'var(--muted)';
            breadcrumb.appendChild(sep);
            const s = document.createElement('span');
            s.className = 'bc-item';
            s.textContent = state.currentRootLabel;
            s.style.color = 'var(--text)';
            breadcrumb.appendChild(s);
        }
    }

    function showInfo(label, comment, uri) {
        infoTitle.textContent = label;
        infoComment.textContent = comment || '(pas de description)';
        infoProps.innerHTML = '';

        if (uri) {
            fetch('/api/concept?uri=' + encodeURIComponent(uri))
                .then(r => r.json())
                .then(data => {
                    if (data.properties && data.properties.length) {
                        const h4 = document.createElement('h4');
                        h4.textContent = 'Propriétés:';
                        infoProps.appendChild(h4);
                        data.properties.forEach(p => {
                            const d = document.createElement('div');
                            d.className = 'info-prop';
                            const rShort = p.range ? p.range.split(/[#/]/).pop() : '?';
                            d.innerHTML = `<span>${p.label}</span> → ${rShort}`;
                            infoProps.appendChild(d);
                        });
                    }
                });
        }

        infoPanel.style.display = 'block';
    }

    // ── Render dispatcher ─────────────────────────────────────────────────────
    function render() {
        svg.selectAll('*').remove();
        svg.attr('width', W()).attr('height', H());

        const rootParam = state.currentRoot
            ? '?root=' + encodeURIComponent(state.currentRoot) + '&depth=' + state.depth
            : '?depth=' + state.depth;

        switch (state.currentViz) {
            case 'radial':      fetch('/api/hierarchy' + rootParam).then(r=>r.json()).then(drawRadial); break;
            case 'packing':     fetch('/api/hierarchy' + rootParam).then(r=>r.json()).then(drawPacking); break;
            case 'progressive': fetch('/api/progressive' + rootParam).then(r=>r.json()).then(drawProgressive); break;
            case 'tree':        fetch('/api/hierarchy' + rootParam).then(r=>r.json()).then(drawTree); break;
            case 'sunburst':    fetch('/api/hierarchy' + rootParam).then(r=>r.json()).then(drawSunburst); break;
        }
    }

    // ── 1. RADIAL (concentric circles by depth) ───────────────────────────────
    function drawRadial(data) {
        if (!data || (!data.children && !data.id)) return;

        const w = W(), h = H(), cx = w / 2, cy = h / 2;
        const root = d3.hierarchy(data)
            .sum(() => 1)
            .sort((a, b) => a.data.label?.localeCompare(b.data.label));

        const maxDepth = root.height || 1;
        const ringGap = Math.min(w, h) / 2 / (maxDepth + 1.5);

        const g = svg.append('g').attr('transform', `translate(${cx},${cy})`);

        // Draw concentric guide circles
        for (let d = 1; d <= maxDepth; d++) {
            g.append('circle')
                .attr('r', d * ringGap)
                .attr('fill', 'none')
                .attr('stroke', 'var(--border)')
                .attr('stroke-dasharray', '4,6')
                .attr('opacity', 0.3);
        }

        // Color scale per parent
        const colorMap = {};
        root.children?.forEach((c, i) => {
            colorMap[c.data.id] = COLOR_PALETTE[i % COLOR_PALETTE.length];
        });

        function getColor(node) {
            if (node.depth === 0) return 'var(--accent)';
            let n = node;
            while (n.depth > 1) n = n.parent;
            return colorMap[n.data.id] || COLOR_PALETTE[0];
        }

        // Place nodes
        root.each(node => {
            if (node.depth === 0) { node.x = 0; node.y = 0; return; }

            const siblings = node.parent.children;
            const idx = siblings.indexOf(node);
            const total = siblings.length;
            const parentAngle = node.parent._angle ?? 0;
            const spread = node.parent.depth === 0 ? Math.PI * 2 : Math.PI * 0.8;
            const angle = parentAngle - spread / 2 + (spread / Math.max(total - 1, 1)) * idx;
            node._angle = angle;
            node.x = Math.cos(angle) * node.depth * ringGap;
            node.y = Math.sin(angle) * node.depth * ringGap;
        });

        // Edges
        const link = g.selectAll('.radial-link')
            .data(root.links())
            .join('line')
            .attr('class', 'radial-link')
            .attr('x1', d => d.source.x).attr('y1', d => d.source.y)
            .attr('x2', d => d.target.x).attr('y2', d => d.target.y)
            .attr('stroke', d => getColor(d.target))
            .attr('stroke-opacity', 0.4)
            .attr('stroke-width', 1.5);

        // Nodes
        const node = g.selectAll('.radial-node')
            .data(root.descendants())
            .join('g')
            .attr('class', 'radial-node')
            .attr('transform', d => `translate(${d.x},${d.y})`)
            .style('cursor', 'pointer')
            .on('click', (e, d) => {
                if (d.data.uri) navigateTo(d.data.uri, d.data.label);
                showInfo(d.data.label, d.data.comment, d.data.uri);
            });

        node.append('circle')
            .attr('r', d => d.depth === 0 ? 22 : Math.max(8, 18 - d.depth * 3))
            .attr('fill', d => getColor(d))
            .attr('fill-opacity', 0.85)
            .attr('stroke', d => getColor(d))
            .attr('stroke-width', 2);

        node.append('text')
            .attr('dy', d => d.depth === 0 ? '0.35em' : -16)
            .attr('text-anchor', 'middle')
            .attr('font-size', d => d.depth === 0 ? 12 : 10)
            .attr('font-weight', d => d.depth === 0 ? 700 : 400)
            .attr('fill', d => d.depth === 0 ? '#000' : 'var(--text)')
            .text(d => d.data.label);

        // Zoom
        svg.call(d3.zoom().scaleExtent([0.3, 4]).on('zoom', e => g.attr('transform', `translate(${cx + e.transform.x},${cy + e.transform.y}) scale(${e.transform.k})`)));
    }

    // ── 2. CIRCLE PACKING (coupe) ─────────────────────────────────────────────
    function drawPacking(data) {
        if (!data) return;
        const w = W(), h = H();

        const root = d3.hierarchy(data)
            .sum(() => 1)
            .sort((a, b) => b.value - a.value);

        const pack = d3.pack().size([w - 20, h - 20]).padding(4);
        pack(root);

        const colorMap = {};
        (root.children || []).forEach((c, i) => {
            colorMap[c.data.id] = COLOR_PALETTE[i % COLOR_PALETTE.length];
        });
        function col(node) {
            if (node.depth === 0) return 'var(--surface2)';
            let n = node; while (n.depth > 1) n = n.parent;
            return colorMap[n.data.id] || COLOR_PALETTE[0];
        }

        const g = svg.append('g').attr('transform', 'translate(10,10)');

        const circles = g.selectAll('.pack-node')
            .data(root.descendants())
            .join('g')
            .attr('class', 'pack-node')
            .attr('transform', d => `translate(${d.x},${d.y})`);

        circles.append('circle')
            .attr('class', 'pack-circle')
            .attr('r', d => d.r)
            .attr('fill', d => col(d))
            .attr('stroke', d => col(d))
            .style('cursor', d => d.data.uri ? 'pointer' : 'default')
            .on('click', (e, d) => {
                if (d.data.uri) navigateTo(d.data.uri, d.data.label);
                if (d.data.comment !== undefined) showInfo(d.data.label, d.data.comment, d.data.uri);
            });

        circles.filter(d => d.r > 14)
            .append('text')
            .attr('class', 'pack-label')
            .attr('dy', '0.35em')
            .text(d => d.data.label)
            .attr('font-size', d => Math.min(12, d.r / 3))
            .style('pointer-events', 'none');

        svg.call(d3.zoom().scaleExtent([0.3, 8]).on('zoom', e => g.attr('transform', `translate(${10 + e.transform.x},${10 + e.transform.y}) scale(${e.transform.k})`)));
    }

    // ── 3. PROGRESSIVE (force graph) ─────────────────────────────────────────
    function drawProgressive(data) {
        if (!data || !data.nodes) return;
        const { nodes, links } = data;
        const w = W(), h = H();

        // Arrow marker
        svg.append('defs').append('marker')
            .attr('id', 'arrow')
            .attr('viewBox', '0 -5 10 10')
            .attr('refX', 20).attr('refY', 0)
            .attr('markerWidth', 6).attr('markerHeight', 6)
            .attr('orient', 'auto')
            .append('path').attr('d', 'M0,-5L10,0L0,5').attr('fill', 'var(--accent2)');

        const g = svg.append('g');

        const simulation = d3.forceSimulation(nodes)
            .force('link', d3.forceLink(links).id(d => d.id).distance(d => d.type === 'hierarchy' ? 80 : 150).strength(0.5))
            .force('charge', d3.forceManyBody().strength(-200))
            .force('center', d3.forceCenter(w / 2, h / 2))
            .force('collision', d3.forceCollide(30));

        const link = g.selectAll('.prog-link')
            .data(links)
            .join('line')
            .attr('class', d => 'link ' + (d.type === 'property' ? 'property' : ''))
            .attr('marker-end', d => d.type === 'property' ? 'url(#arrow)' : null);

        const linkLabel = g.selectAll('.link-label')
            .data(links.filter(l => l.label))
            .join('text')
            .attr('class', 'link-label')
            .text(d => d.label);

        const node = g.selectAll('.prog-node')
            .data(nodes)
            .join('g')
            .attr('class', 'prog-node')
            .style('cursor', 'pointer')
            .call(d3.drag()
                .on('start', (e, d) => { if (!e.active) simulation.alphaTarget(0.3).restart(); d.fx = d.x; d.fy = d.y; })
                .on('drag',  (e, d) => { d.fx = e.x; d.fy = e.y; })
                .on('end',   (e, d) => { if (!e.active) simulation.alphaTarget(0); d.fx = null; d.fy = null; }))
            .on('click', (e, d) => {
                if (d.uri) navigateTo(d.uri, d.label);
                showInfo(d.label, d.comment, d.uri);
            });

        const colorIdx = {};
        nodes.forEach((n, i) => { colorIdx[n.id] = COLOR_PALETTE[i % COLOR_PALETTE.length]; });

        node.append('circle')
            .attr('r', 18)
            .attr('fill', d => colorIdx[d.id])
            .attr('fill-opacity', 0.8)
            .attr('stroke', d => colorIdx[d.id])
            .attr('stroke-width', 2);

        node.append('text')
            .attr('dy', -22)
            .attr('text-anchor', 'middle')
            .attr('font-size', 10)
            .attr('fill', 'var(--text)')
            .text(d => d.label);

        simulation.on('tick', () => {
            link.attr('x1', d => d.source.x).attr('y1', d => d.source.y)
                .attr('x2', d => d.target.x).attr('y2', d => d.target.y);
            linkLabel.attr('x', d => (d.source.x + d.target.x) / 2)
                     .attr('y', d => (d.source.y + d.target.y) / 2);
            node.attr('transform', d => `translate(${d.x},${d.y})`);
        });

        svg.call(d3.zoom().scaleExtent([0.2, 4]).on('zoom', e => g.attr('transform', e.transform)));
    }

    // ── 4. COLLAPSIBLE TREE ───────────────────────────────────────────────────
    function drawTree(data) {
        if (!data) return;
        const w = W(), h = H();
        const margin = { top: 20, right: 120, bottom: 20, left: 80 };
        const iw = w - margin.left - margin.right;
        const ih = h - margin.top - margin.bottom;

        const g = svg.append('g').attr('transform', `translate(${margin.left},${margin.top})`);

        const root = d3.hierarchy(data);
        root.x0 = ih / 2;
        root.y0 = 0;

        // Collapse all except first level
        root.children?.forEach(c => c.children?.forEach(collapse));

        function collapse(d) { if (d.children) { d._children = d.children; d._children.forEach(collapse); d.children = null; } }
        function expand(d)   { if (d._children) { d.children = d._children; d.children.forEach(expand); d._children = null; } }

        let i = 0;
        function update(source) {
            const treeLayout = d3.tree().size([ih, iw]);
            treeLayout(root);

            const nodes = root.descendants();
            const nodeUpdate = g.selectAll('.tree-node')
                .data(nodes, d => d.id || (d.id = ++i));

            const nodeEnter = nodeUpdate.enter().append('g')
                .attr('class', 'tree-node')
                .attr('transform', d => `translate(${source.y0},${source.x0})`)
                .style('cursor', 'pointer')
                .on('click', (e, d) => {
                    if (e.shiftKey || !d.children && !d._children) {
                        if (d.data.uri) navigateTo(d.data.uri, d.data.label);
                        showInfo(d.data.label, d.data.comment, d.data.uri);
                        return;
                    }
                    if (d.children) { d._children = d.children; d.children = null; }
                    else { d.children = d._children; d._children = null; }
                    update(d);
                });

            nodeEnter.append('circle')
                .attr('r', 7)
                .attr('fill', d => d._children ? 'var(--accent)' : (d.children ? 'var(--accent2)' : 'var(--surface2)'))
                .attr('stroke', d => d._children || d.children ? 'var(--accent)' : 'var(--border)')
                .attr('stroke-width', 2);

            nodeEnter.append('text')
                .attr('dy', '0.35em')
                .attr('x', d => d.children || d._children ? -12 : 12)
                .attr('text-anchor', d => d.children || d._children ? 'end' : 'start')
                .attr('font-size', 11)
                .attr('fill', 'var(--text)')
                .text(d => d.data.label);

            const nodeAll = nodeEnter.merge(nodeUpdate);
            nodeAll.transition().duration(400)
                .attr('transform', d => `translate(${d.y},${d.x})`);
            nodeAll.select('circle')
                .attr('fill', d => d._children ? 'var(--accent)' : (d.children ? 'var(--accent2)' : 'var(--surface2)'));

            nodeUpdate.exit().transition().duration(400)
                .attr('transform', `translate(${source.y},${source.x})`).remove();

            // Links
            const links = root.links();
            const linkSel = g.selectAll('.tree-link').data(links, d => d.target.id);
            const linkEnter = linkSel.enter().insert('path', 'g')
                .attr('class', 'tree-link link')
                .attr('d', () => {
                    const o = { x: source.x0, y: source.y0 };
                    return diagonal(o, o);
                });
            linkEnter.merge(linkSel).transition().duration(400).attr('d', d => diagonal(d.source, d.target));
            linkSel.exit().transition().duration(400).attr('d', () => { const o = { x: source.x, y: source.y }; return diagonal(o, o); }).remove();

            nodes.forEach(d => { d.x0 = d.x; d.y0 = d.y; });
        }

        function diagonal(s, t) {
            return `M${s.y},${s.x}C${(s.y + t.y) / 2},${s.x} ${(s.y + t.y) / 2},${t.x} ${t.y},${t.x}`;
        }

        update(root);
        svg.call(d3.zoom().scaleExtent([0.2, 4]).on('zoom', e => g.attr('transform', `translate(${margin.left + e.transform.x},${margin.top + e.transform.y}) scale(${e.transform.k})`)));
    }

    // ── 5. SUNBURST ───────────────────────────────────────────────────────────
    function drawSunburst(data) {
        if (!data) return;
        const w = W(), h = H();
        const radius = Math.min(w, h) / 2;
        const cx = w / 2, cy = h / 2;

        const root = d3.hierarchy(data)
            .sum(() => 1)
            .sort((a, b) => b.value - a.value);

        const partition = d3.partition().size([2 * Math.PI, radius]);
        partition(root);

        const color = d3.scaleOrdinal(COLOR_PALETTE);

        const arc = d3.arc()
            .startAngle(d => d.x0)
            .endAngle(d => d.x1)
            .innerRadius(d => d.y0)
            .outerRadius(d => d.y1 - 2);

        const g = svg.append('g').attr('transform', `translate(${cx},${cy})`);

        g.selectAll('.arc')
            .data(root.descendants().filter(d => d.depth))
            .join('path')
            .attr('class', 'arc')
            .attr('d', arc)
            .attr('fill', d => { let n = d; while (n.depth > 1) n = n.parent; return color(n.data.id); })
            .attr('fill-opacity', d => Math.max(0.15, 1 - d.depth * 0.2))
            .attr('stroke', 'var(--bg)')
            .attr('stroke-width', 1.5)
            .style('cursor', 'pointer')
            .on('click', (e, d) => {
                if (d.data.uri) navigateTo(d.data.uri, d.data.label);
                showInfo(d.data.label, d.data.comment, d.data.uri);
            })
            .append('title').text(d => d.data.label);

        // Labels for larger arcs
        g.selectAll('.sb-label')
            .data(root.descendants().filter(d => d.depth && (d.x1 - d.x0) > 0.1))
            .join('text')
            .attr('class', 'arc-label')
            .attr('transform', d => {
                const a = (d.x0 + d.x1) / 2 - Math.PI / 2;
                const r = (d.y0 + d.y1) / 2;
                return `rotate(${a * 180 / Math.PI}) translate(${r},0) rotate(${a > 0 ? 0 : 180})`;
            })
            .attr('text-anchor', 'middle')
            .attr('font-size', 10)
            .text(d => d.data.label.slice(0, 12));

        svg.call(d3.zoom().scaleExtent([0.3, 4]).on('zoom', e => g.attr('transform', `translate(${cx + e.transform.x},${cy + e.transform.y}) scale(${e.transform.k})`)));
    }

    // ── Initial render ────────────────────────────────────────────────────────
    window.addEventListener('resize', () => render());
    render();
})();
