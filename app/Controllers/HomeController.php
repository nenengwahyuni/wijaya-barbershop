<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\Item;
use App\Models\Category;
use App\Models\User;

class HomeController extends Controller
{
    public function index(Request $request, Response $response)
    {
        $categories = Category::all();
        $featuredItems = Item::allActiveWithCategory();
        $barbers = User::getBarbersOnDuty();

        return $this->render('customer.home', [
            'title' => 'Wijaya Barbershop - Pangkas Rambut Modern & Berkualitas di Metro',
            'categories' => $categories,
            'items' => array_slice($featuredItems, 0, 6),
            'barbers' => $barbers,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error')
        ], 'main');
    }

    public function services(Request $request, Response $response)
    {
        $categoryId = $request->input('category') ? (int) $request->input('category') : null;
        $categories = Category::all();
        $items = Item::allActiveWithCategory($categoryId);

        return $this->render('customer.services', [
            'title' => 'Katalog Layanan & Produk - Wijaya Barbershop',
            'categories' => $categories,
            'items' => $items,
            'selectedCategory' => $categoryId
        ], 'main');
    }
}

