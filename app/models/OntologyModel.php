<?php
namespace App\Models;

use EasyRdf\Graph;
use EasyRdf\RdfNamespace;

class OntologyModel {
    private Graph $graph;
    private string $filePath;

    public function __construct(string $filePath) {
        $this->filePath = $filePath;
        $this->graph = new Graph();
        RdfNamespace::set('owl', 'http://www.w3.org/2002/07/owl#');
        RdfNamespace::set('rdfs', 'http://www.w3.org/2000/01/rdf-schema#');
        RdfNamespace::set('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');

        $format = $this->detectFormat($filePath);
        $this->graph->parseFile($filePath, $format);
    }

    private function detectFormat(string $path): string {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return match($ext) {
            'ttl' => 'turtle',
            'n3'  => 'n3',
            'nt'  => 'ntriples',
            'json', 'jsonld' => 'jsonld',
            default => 'rdfxml',
        };
    }

    /**
     * Get all classes in the ontology
     */
    public function getClasses(): array {
        $classes = [];
        // OWL classes
        foreach ($this->graph->allOfType('owl:Class') as $class) {
            $uri = $class->getUri();
            if ($uri) $classes[$uri] = $this->classInfo($class);
        }
        // RDFS classes
        foreach ($this->graph->allOfType('rdfs:Class') as $class) {
            $uri = $class->getUri();
            if ($uri && !isset($classes[$uri])) $classes[$uri] = $this->classInfo($class);
        }
        return array_values($classes);
    }

    private function classInfo($class): array {
        $uri = $class->getUri();
        $label = $class->getLiteral('rdfs:label', 'en') 
               ?? $class->getLiteral('rdfs:label', 'fr')
               ?? $class->getLiteral('rdfs:label');
        $comment = $class->getLiteral('rdfs:comment', 'en')
                 ?? $class->getLiteral('rdfs:comment', 'fr')
                 ?? $class->getLiteral('rdfs:comment');

        $parents = [];
        foreach ($class->allResources('rdfs:subClassOf') as $parent) {
            if ($parent->getUri()) $parents[] = $parent->getUri();
        }

        return [
            'uri'     => $uri,
            'id'      => $this->shortName($uri),
            'label'   => $label ? (string)$label : $this->shortName($uri),
            'comment' => $comment ? (string)$comment : '',
            'parents' => $parents,
        ];
    }

    /**
     * Get all properties (rdf:Property, owl:ObjectProperty, owl:DatatypeProperty)
     */
    public function getProperties(): array {
        $props = [];
        $types = ['rdf:Property', 'owl:ObjectProperty', 'owl:DatatypeProperty', 'owl:AnnotationProperty'];
        foreach ($types as $type) {
            foreach ($this->graph->allOfType($type) as $prop) {
                $uri = $prop->getUri();
                if (!$uri || isset($props[$uri])) continue;
                $label = $prop->getLiteral('rdfs:label', 'en') ?? $prop->getLiteral('rdfs:label', 'fr') ?? $prop->getLiteral('rdfs:label');
                $comment = $prop->getLiteral('rdfs:comment', 'en') ?? $prop->getLiteral('rdfs:comment', 'fr') ?? $prop->getLiteral('rdfs:comment');
                $domain = $prop->getResource('rdfs:domain');
                $range  = $prop->getResource('rdfs:range');
                $subOf  = [];
                foreach ($prop->allResources('rdfs:subPropertyOf') as $sp) {
                    if ($sp->getUri()) $subOf[] = $sp->getUri();
                }
                $props[$uri] = [
                    'uri'     => $uri,
                    'id'      => $this->shortName($uri),
                    'label'   => $label ? (string)$label : $this->shortName($uri),
                    'comment' => $comment ? (string)$comment : '',
                    'domain'  => $domain ? $domain->getUri() : null,
                    'range'   => $range ? $range->getUri() : null,
                    'subPropertyOf' => $subOf,
                    'type'    => $type,
                ];
            }
        }
        return array_values($props);
    }

    /**
     * Build hierarchy tree rooted at a given concept (or all roots if null)
     */
    public function getHierarchyTree(?string $rootUri = null): array {
        $classes = $this->getClasses();
        $byUri = [];
        foreach ($classes as $c) $byUri[$c['uri']] = $c;

        // Find children map
        $children = [];
        foreach ($classes as $c) {
            foreach ($c['parents'] as $p) {
                $children[$p][] = $c['uri'];
            }
        }

        // Find root nodes (no parents or parent is owl:Thing)
        if ($rootUri) {
            return $this->buildTree($rootUri, $byUri, $children, 0);
        }

        $roots = [];
        foreach ($classes as $c) {
            $realParents = array_filter($c['parents'], fn($p) => isset($byUri[$p]));
            if (empty($realParents)) {
                $roots[] = $this->buildTree($c['uri'], $byUri, $children, 0);
            }
        }
        return ['id' => 'root', 'label' => 'Ontologie', 'uri' => null, 'children' => $roots];
    }

    private function buildTree(string $uri, array &$byUri, array &$children, int $depth): array {
        if (!isset($byUri[$uri])) return [];
        $node = $byUri[$uri];
        $node['depth'] = $depth;
        $node['children'] = [];
        if (isset($children[$uri])) {
            foreach ($children[$uri] as $childUri) {
                $node['children'][] = $this->buildTree($childUri, $byUri, $children, $depth + 1);
            }
        }
        return $node;
    }

    /**
     * Get radial data for a concept: center + rings of descendants
     */
    public function getRadialData(string $rootUri, int $depth = 3): array {
        $tree = $this->getHierarchyTree($rootUri);
        return $tree;
    }

    /**
     * Get progressive visualization data: hierarchy + named relations
     */
    public function getProgressiveData(string $rootUri, int $depth = 2): array {
        $tree  = $this->getHierarchyTree($rootUri);
        $props = $this->getProperties();

        $nodes = [];
        $links = [];
        $this->flattenTree($tree, $nodes);

        $nodeUris = array_column($nodes, 'uri');

        foreach ($props as $prop) {
            if ($prop['domain'] && in_array($prop['domain'], $nodeUris)
             && $prop['range']  && in_array($prop['range'],  $nodeUris)) {
                $links[] = [
                    'source' => $this->shortName($prop['domain']),
                    'target' => $this->shortName($prop['range']),
                    'label'  => $prop['label'],
                    'type'   => 'property',
                ];
            }
        }

        // Also add hierarchy links
        foreach ($nodes as $n) {
            foreach ($n['parents'] as $p) {
                $pShort = $this->shortName($p);
                if (in_array($pShort, array_column($nodes, 'id'))) {
                    $links[] = [
                        'source' => $pShort,
                        'target' => $n['id'],
                        'label'  => null,
                        'type'   => 'hierarchy',
                    ];
                }
            }
        }

        return ['nodes' => $nodes, 'links' => $links];
    }

    private function flattenTree(array $node, array &$out): void {
        if (empty($node) || !isset($node['id'])) return;
        $flat = $node;
        unset($flat['children']);
        $out[] = $flat;
        foreach ($node['children'] ?? [] as $child) {
            $this->flattenTree($child, $out);
        }
    }

    /**
     * Get property hierarchy tree
     */
    public function getPropertyTree(?string $rootUri = null): array {
        $props = $this->getProperties();
        $byUri = [];
        foreach ($props as $p) $byUri[$p['uri']] = $p;

        $children = [];
        foreach ($props as $p) {
            foreach ($p['subPropertyOf'] as $parent) {
                $children[$parent][] = $p['uri'];
            }
        }

        if ($rootUri) {
            return $this->buildPropTree($rootUri, $byUri, $children);
        }

        $roots = [];
        foreach ($props as $p) {
            if (empty($p['subPropertyOf'])) {
                $roots[] = $this->buildPropTree($p['uri'], $byUri, $children);
            }
        }
        return ['id' => 'properties', 'label' => 'Propriétés', 'uri' => null, 'children' => $roots];
    }

    private function buildPropTree(string $uri, array &$byUri, array &$children): array {
        if (!isset($byUri[$uri])) return [];
        $node = $byUri[$uri];
        $node['children'] = [];
        if (isset($children[$uri])) {
            foreach ($children[$uri] as $c) {
                $node['children'][] = $this->buildPropTree($c, $byUri, $children);
            }
        }
        return $node;
    }

    private function shortName(string $uri): string {
        if (str_contains($uri, '#')) {
            return substr($uri, strrpos($uri, '#') + 1);
        }
        return basename($uri);
    }

    public function getGraph(): Graph {
        return $this->graph;
    }
}
