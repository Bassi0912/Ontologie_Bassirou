<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\OntologyModel;

class ApiController extends Controller {
    private function getModel(): ?OntologyModel {
        if (!isset($_SESSION['ontology_file']) || !file_exists($_SESSION['ontology_file'])) {
            return null;
        }
        try {
            return new OntologyModel($_SESSION['ontology_file']);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function hierarchy(): void {
        $model = $this->getModel();
        if (!$model) { $this->json(['error' => 'No ontology loaded']); return; }
        $root  = $_GET['root'] ?? null;
        $this->json($model->getHierarchyTree($root));
    }

    public function properties(): void {
        $model = $this->getModel();
        if (!$model) { $this->json(['error' => 'No ontology loaded']); return; }
        $root = $_GET['root'] ?? null;
        $this->json($model->getPropertyTree($root));
    }

    public function concept(): void {
        $model = $this->getModel();
        if (!$model) { $this->json(['error' => 'No ontology loaded']); return; }

        $uri = $_GET['uri'] ?? null;
        if (!$uri) { $this->json(['error' => 'Missing uri']); return; }

        $classes = $model->getClasses();
        $props   = $model->getProperties();

        $concept = null;
        foreach ($classes as $c) {
            if ($c['uri'] === $uri || $c['id'] === $uri) { $concept = $c; break; }
        }

        $relatedProps = array_filter($props, fn($p) =>
            ($p['domain'] && ($p['domain'] === $uri || (str_contains($p['domain'], '#') && substr($p['domain'], strrpos($p['domain'],'#')+1) === $uri)))
        );

        $this->json(['concept' => $concept, 'properties' => array_values($relatedProps)]);
    }

    public function radial(): void {
        $model = $this->getModel();
        if (!$model) { $this->json(['error' => 'No ontology loaded']); return; }
        $root  = $_GET['root'] ?? null;
        $depth = (int)($_GET['depth'] ?? 3);
        if (!$root) { $this->json($model->getHierarchyTree()); return; }
        $this->json($model->getRadialData($root, $depth));
    }

    public function progressive(): void {
        $model = $this->getModel();
        if (!$model) { $this->json(['error' => 'No ontology loaded']); return; }
        $root  = $_GET['root'] ?? null;
        $depth = (int)($_GET['depth'] ?? 2);
        if (!$root) {
            $tree = $model->getHierarchyTree();
            $nodes = []; $links = [];
            foreach ($tree['children'] ?? [] as $child) {
                $this->flattenForProg($child, $nodes, $links, 0, $depth);
            }
            $this->json(['nodes' => $nodes, 'links' => $links]);
            return;
        }
        $this->json($model->getProgressiveData($root, $depth));
    }

    private function flattenForProg(array $node, array &$nodes, array &$links, int $cur, int $max): void {
        if (empty($node)) return;
        $flat = $node; unset($flat['children']);
        $nodes[] = $flat;
        if ($cur < $max) {
            foreach ($node['children'] ?? [] as $child) {
                $links[] = ['source' => $node['id'], 'target' => $child['id'], 'label' => null, 'type' => 'hierarchy'];
                $this->flattenForProg($child, $nodes, $links, $cur + 1, $max);
            }
        }
    }
}
