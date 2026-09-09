<?php
namespace App\Controllers;

class Controller
{
    /**
     * Render a view template with provided data.
     *
     * @param string $view View file name (dot notation, e.g. 'clients.index')
     * @param array $data Associative array of data passed to view
     * @return void
     */
    protected function render(string $view, array $data = []): void
    {
        $viewData = array_merge($data, ['data' => $data]);
        echo view($view, $viewData);
    }

    /**
     * Return JSON response.
     *
     * @param mixed $data
     * @param int $statusCode
     * @return void
     */
    protected function json($data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    /**
     * Redirect to a URL.
     *
     * @param string $url
     * @return void
     */
    protected function redirect(string $url): void
    {
        header("Location: " . $url);
        exit;
    }
}
