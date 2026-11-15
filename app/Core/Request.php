<?php

namespace App\Core;

class Request
{
    private array $get;
    private array $post;
    private array $files;
    private array $server;

    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->files = $_FILES;
        $this->server = $_SERVER;
    }

    public function getMethod(): string
    {
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
    }

    public function getPath(): string
    {
        $uri = $this->server['REQUEST_URI'] ?? '/';
        $position = strpos($uri, '?');
        if ($position !== false) {
            $uri = substr($uri, 0, $position);
        }
        
        // Sesuaikan jika dijalankan di sub-folder XAMPP (abaikan '/api' milik Vercel Serverless)
        $scriptName = dirname($this->server['SCRIPT_NAME'] ?? '');
        $scriptName = str_replace('\\', '/', $scriptName);
        if ($scriptName !== '/' && $scriptName !== '.' && $scriptName !== '/api' && !empty($scriptName)) {
            if (str_starts_with($uri, $scriptName)) {
                $uri = substr($uri, strlen($scriptName));
            }
        }

        return '/' . trim($uri, '/');
    }

    public function isPost(): bool
    {
        return $this->getMethod() === 'POST';
    }

    public function isGet(): bool
    {
        return $this->getMethod() === 'GET';
    }

    public function isAjax(): bool
    {
        return (!empty($this->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->server['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
            || (isset($this->server['HTTP_ACCEPT']) && str_contains($this->server['HTTP_ACCEPT'], 'application/json'))
            || ($this->input('ajax') === '1');
    }

    public function path(): string
    {
        return $this->getPath();
    }

    public function input(string $key, $default = null)
    {
        return $this->post[$key] ?? $this->get[$key] ?? $default;
    }

    public function all(): array
    {
        $json = file_get_contents('php://input');
        $jsonData = json_decode($json, true) ?: [];
        return array_merge($this->get, $this->post, $jsonData);
    }

    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }
}

