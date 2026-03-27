<?php
namespace App\Core;

class Controller {
    protected function render(string $view, array $data = []): void {
        extract($data);
        $viewFile = VIEW_PATH . '/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewFile)) {
            throw new \Exception("View not found: $viewFile");
        }
        require VIEW_PATH . '/layouts/main.php';
    }

    protected function json(mixed $data): void {
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    protected function redirect(string $url): void {
        header("Location: $url");
        exit;
    }
}
