<?php

namespace App\Core;

class Response
{
    public function setStatusCode(int $code): void
    {
        http_response_code($code);
    }

    public function redirect(string $url): void
    {
        Auth::syncCookieSession();
        header("Location: {$url}");
        exit;
    }

    public function json(array $data, int $code = 200): void
    {
        Auth::syncCookieSession();
        $this->setStatusCode($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
}

