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
            $memoryStart = memory_get_usage();

            $categories = Cache::remember('categories_list', now()->addMinutes(5), function () {
                try {
                    $categoriesSnapshot = $this->firestore->collection('categories')
                        ->select(['name'])
                        ->documents();

                    $categories = [];

                    foreach ($categoriesSnapshot as $catDoc) {
                        try {
                            if ($catDoc->exists()) {
                                $categories[$catDoc->id()] = $catDoc->get('name');
                            }
                        } catch (\Throwable $e) {
                            Log::error('Error processing category document: ' . $e->getMessage(), [
                                'document_id' => $catDoc->id(),
                                'trace' => $e->getTraceAsString()
                            ]);
                            continue;
                        }
                    }
                    return $categories;
                } catch (\Throwable $e) {
                    Log::error('Error in categories cache callback: ' . $e->getMessage(), [
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e;
                }
            });

            $products = Cache::remember('products_list', now()->addMinutes(5), function () use ($categories) {
                try {
                    $productsSnapshot = $this->firestore->collection('products')
                        ->select(['name', 'price', 'categoryId', 'status', 'seller_ifos'])
                        ->documents();

                    $products = [];

                    foreach ($productsSnapshot as $doc) {
                        try {
                            if ($doc->exists()) {
                                $data = $doc->data();

                                Log::debug('Processing product document: ' . $doc->id(), [
                                    'data_type' => gettype($data),
                                    'data_size' => strlen(json_encode($data))
                                ]);

                                if (!is_array($data)) {
                                    Log::warning('Invalid product data structure for document: ' . $doc->id());
                                    continue;
                                }

                                $products[] = [
                                    'id' => $doc->id(),
                                    'name' => $data['name'] ?? '',
                                    'price' => $data['price'] ?? 0,
                                    'category' => $categories[$data['categoryId']] ?? 'Unknown',
                                    'category_id' => $data['categoryId'] ?? '',
                                    'status' => $data['status'] ?? '',
                                    'seller' => $data['seller_ifos']['seller_email'] ?? '',
                                ];
                            }
                        } catch (\Throwable $e) {
                            Log::error('Error processing product document: ' . $e->getMessage(), [
                                'document_id' => $doc->id(),
                                'trace' => $e->getTraceAsString()
                            ]);
                            continue;
                        }
                    }
                    return $products;
                } catch (\Throwable $e) {
                    Log::error('Error in products cache callback: ' . $e->getMessage(), [
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e;
                }
            });

            $endTime = microtime(true);
            $memoryEnd = memory_get_usage();

            Log::info('Firestore product load metrics:', [
                'time_ms' => round(($endTime - $startTime) * 1000, 2),
                'memory_usage_mb' => round(($memoryEnd - $memoryStart) / 1024 / 1024, 2),
                'products_count' => count($products)
            ]);

            return view('products', compact('products'));
        } catch (\Throwable $e) {
            Log::error('Firestore Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'memory_usage' => memory_get_usage()
            ]);
            return response()->view('errors.firebase', [
                'message' => 'Failed to load products from Firestore.',
            ], 500);
        }
    }
}
