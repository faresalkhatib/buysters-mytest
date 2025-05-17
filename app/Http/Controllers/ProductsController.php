<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ProductsController extends Controller
{
    protected $firestore;

    public function __construct()
    {
        $this->firestore = app('firebase.firestore');
    }

    public function index()
    {
        try {
            $startTime = microtime(true);

            $categories = Cache::remember('categories_list', now()->addMinutes(5), function () {
                $categoriesSnapshot = $this->firestore->collection('categories')
                    ->select(['name'])
                    ->documents();

                $categories = [];
                foreach ($categoriesSnapshot as $catDoc) {
                    if ($catDoc->exists()) {
                        $categories[$catDoc->id()] = $catDoc->get('name');
                    }
                }
                return $categories;
            });

            $products = Cache::remember('products_list', now()->addMinutes(5), function () use ($categories) {
                $productsSnapshot = $this->firestore->collection('products')
                    ->select(['name', 'price', 'categoryId', 'status', 'seller_ifos'])
                    ->documents();

                $products = [];
                foreach ($productsSnapshot as $doc) {
                    if ($doc->exists()) {
                        $data = $doc->data();
                        $categoryName = $categories[$data['categoryId']] ?? 'Unknown';

                        $products[] = [
                            'id' => $doc->id(),
                            'name' => $data['name'] ?? '',
                            'price' => $data['price'] ?? 0,
                            'category' => $categoryName,
                            'status' => $data['status'] ?? '',
                            'seller' => $data['seller_ifos']['seller_email'] ?? '',
                        ];
                    }
                }

                return $products;
            });

            $endTime = microtime(true);
            Log::info('Firestore product load time: ' . round(($endTime - $startTime) * 1000, 2) . 'ms');

            return view('products', compact('products'));
        } catch (\Throwable $e) {
            Log::error('Firestore Error: ' . $e->getMessage());

            return response()->view('errors.firebase', [
                'message' => 'Failed to load products from Firestore.',
            ], 500);
        }
    }
}
