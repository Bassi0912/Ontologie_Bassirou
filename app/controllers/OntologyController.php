<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\OntologyModel;

class OntologyController extends Controller {
    public function upload(): void {

        if (!isset($_FILES['ontology']) || $_FILES['ontology']['error'] !== UPLOAD_ERR_OK) {
            $this->redirect('/upload?error=no_file');
            return;
        }

        $file = $_FILES['ontology'];
        $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, ['rdf', 'owl', 'xml', 'ttl', 'n3', 'nt', 'jsonld'])) {
            $this->redirect('/upload?error=bad_format');
            return;
        }

        $dest = DATA_PATH . '/' . uniqid('onto_', true) . '.' . $ext;
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            $this->redirect('/upload?error=upload_failed');
            return;
        }

        $_SESSION['ontology_file'] = $dest;
        $_SESSION['ontology_name'] = htmlspecialchars($file['name']);
        $this->redirect('/ontology');
    }

    public function index(): void {
        if (!isset($_SESSION['ontology_file'])) {
            $this->redirect('/');
            return;
        }

        try {
            $model   = new OntologyModel($_SESSION['ontology_file']);
            $classes = $model->getClasses();
            $props   = $model->getProperties();
        } catch (\Exception $e) {
            $classes = [];
            $props   = [];
        }

        $this->render('ontology.index', [
            'classes'      => $classes,
            'props'        => $props,
            'ontologyName' => $_SESSION['ontology_name'] ?? 'ontologie',
        ]);
    }
}
