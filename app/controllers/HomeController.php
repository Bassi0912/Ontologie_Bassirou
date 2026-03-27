<?php
namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller {
    public function index(): void {
        $hasFile = isset($_SESSION['ontology_file']) && file_exists($_SESSION['ontology_file']);
        $this->render('home.index', ['hasFile' => $hasFile]);
    }

    public function upload(): void {
        $this->render('home.upload', []);
    }
}
