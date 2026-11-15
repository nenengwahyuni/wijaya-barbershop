<?php

namespace App\Core;

abstract class Controller
{
    protected function render(string $view, array $data = [], ?string $layout = 'main'): string
    {
        return View::render($view, $data, $layout);
    }

    protected function json(array $data, int $status = 200): void
    {
        $response = new Response();
        $response->json($data, $status);
    }

    protected function redirect(string $url): void
    {
        $response = new Response();
        $response->redirect($url);
    }

    protected function setFlash(string $key, string $message): void
    {
        Auth::initSession();
        $_SESSION['flash'][$key] = $message;
    }

    protected function getFlash(string $key): ?string
    {
        Auth::initSession();
        $msg = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
}

