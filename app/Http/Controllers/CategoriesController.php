<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class CategoriesController extends Controller
{
    protected $firestore;
    protected $storage;
    protected $bucket;

    public function __construct()
    {
        $this->firestore = app('firebase.firestore');
        $this->storage = app('firebase.storage');
        $this->bucket = $this->storage->getBucket('graduation-project-e47af.firebasestorage.app');
    }

    public function index()
    {
        try {
            $startTime = microtime(true);
            $memoryStart = memory_get_usage();

            $categories = Cache::remember('categories_index_list', now()->addMinutes(1), function () {
                try {
                    $categoriesSnapshot = $this->firestore->collection('categories')
                        ->documents();

                    $categories = [];

                    foreach ($categoriesSnapshot as $document) {
                        try {
                            if (!$document->exists()) {
                                continue;
                            }

                            $data = $document->data();

                            Log::debug('Processing document: ' . $document->id(), [
                                'data_type' => gettype($data),
                                'data_size' => strlen(json_encode($data))
                            ]);

                            if (!is_array($data)) {
                                Log::warning('Invalid category data structure for document: ' . $document->id());
                                continue;
                            }

                            if (isset($data['deleted_at'])) {
                                continue;
                            }

                            $categories[] = [
                                'id' => $document->id(),
                                'name' => $data['name'] ?? '',
                                'image_url' => $data['imageUrl'] ?? ''
                            ];
                        } catch (\Throwable $e) {
                            Log::error('Error processing category document: ' . $e->getMessage(), [
                                'document_id' => $document->id(),
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

            $endTime = microtime(true);
            $memoryEnd = memory_get_usage();

            Log::info('Firestore category load metrics:', [
                'time_ms' => round(($endTime - $startTime) * 1000, 2),
                'memory_usage_mb' => round(($memoryEnd - $memoryStart) / 1024 / 1024, 2),
                'categories_count' => count($categories)
            ]);

            return view('categories.index', compact('categories'));
        } catch (\Throwable $e) {
            Log::error('Firestore Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'memory_usage' => memory_get_usage()
            ]);
            return response()->view('errors.firebase', [
                'message' => 'Failed to load categories from Firestore.',
            ], 500);
        }
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        try {
            $image = $request->file('image');
            $sanitizedName = preg_replace('/[^a-zA-Z0-9\.\-_]/', '_', $image->getClientOriginalName());
            $imageName = time() . '_' . $sanitizedName;
            $storagePath = 'category_images/' . $imageName;

            $fileContent = file_get_contents($image->getRealPath());
            if ($fileContent === false) {
                throw new \Exception('Could not read image file.');
            }

            $object = $this->bucket->upload($fileContent, [
                'name' => $storagePath,
                'metadata' => ['contentType' => $image->getMimeType()],
            ]);

            $object->update([], ['predefinedAcl' => 'PUBLICREAD']);

            $imageUrl = sprintf(
                'https://firebasestorage.googleapis.com/v0/b/%s/o/%s?alt=media',
                $this->bucket->name(),
                urlencode($storagePath)
            );

            $this->firestore->collection('categories')->add([
                'name' => $validated['name'],
                'imageUrl' => $imageUrl,
                'deleted_at' => null
            ]);

            Cache::forget('categories_index_list');
            return redirect()->route('category')->with('success', 'Category created successfully.');
        } catch (\Throwable $e) {
            Log::error('Firebase Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withInput()->with('error', 'Category creation failed: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $categoryDocument = $this->firestore->collection('categories')->document($id);
            $category = $categoryDocument->snapshot()->data();

            if (!$category) {
                return redirect()->route('category')->with('error', 'Category not found.');
            }

            $category['id'] = $id;
            return view('categories.edit', compact('category'));
        } catch (\Throwable $e) {
            Log::error('Firebase Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to load category: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        try {
            $categoryDocument = $this->firestore->collection('categories')->document($id);
            $existingData = $categoryDocument->snapshot()->data();

            $categoryData = [
                'name' => $validated['name'],
                'imageUrl' => $existingData['imageUrl']
            ];

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $sanitizedName = preg_replace('/[^a-zA-Z0-9\.\-_]/', '_', $image->getClientOriginalName());
                $imageName = time() . '_' . $sanitizedName;
                $storagePath = 'category_images/' . $imageName;

                $stream = fopen($image->getRealPath(), 'r');
                $object = $this->bucket->upload($stream, [
                    'name' => $storagePath,
                    'metadata' => ['contentType' => $image->getMimeType()],
                ]);
                fclose($stream);

                $object->update([], ['predefinedAcl' => 'PUBLICREAD']);

                $imageUrl = sprintf(
                    'https://firebasestorage.googleapis.com/v0/b/%s/o/%s?alt=media',
                    $this->bucket->name(),
                    urlencode($storagePath)
                );

                $categoryData['imageUrl'] = $imageUrl;
            }

            $categoryDocument->set($categoryData);
            Cache::forget('categories_index_list');

            return redirect('/categories')->with('success', 'Category updated successfully.');
        } catch (\Throwable $e) {
            Log::error('Firebase Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withInput()->with('error', 'Category update failed: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $categoryDocument = $this->firestore->collection('categories')->document($id);
            $existingData = $categoryDocument->snapshot()->data();

            $categoryDocument->set([
                'name' => $existingData['name'],
                'imageUrl' => $existingData['imageUrl'] ?? '',
                'deleted_at' => new \Google\Cloud\Core\Timestamp(new \DateTime())
            ]);

            Cache::forget('categories_index_list');
            return redirect()->route('category')->with('success', 'Category deleted successfully.');
        } catch (\Throwable $e) {
            Log::error('Firebase Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to delete category: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $startTime = microtime(true);
            $memoryStart = memory_get_usage();

            $category = Cache::remember('category_' . $id, now()->addMinutes(5), function () use ($id) {
                try {
                    $categoryDoc = $this->firestore->collection('categories')->document($id);
                    $categoryData = $categoryDoc->snapshot()->data();

                    if (!$categoryData) {
                        throw new \Exception('Category not found');
                    }

                    return [
                        'id' => $id,
                        'name' => $categoryData['name'] ?? '',
                        'image_url' => $categoryData['imageUrl'] ?? ''
                    ];
                } catch (\Throwable $e) {
                    Log::error('Error fetching category: ' . $e->getMessage(), [
                        'category_id' => $id,
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e;
                }
            });

            $products = Cache::remember('products_by_category_' . $id, now()->addMinutes(5), function () use ($id) {
                try {
                    $productsSnapshot = $this->firestore->collection('products')
                        ->where('categoryId', '=', $id)
                        ->select(['name', 'price', 'status', 'seller_ifos'])
                        ->documents();

                    $products = [];
                    $totalPrice = 0;
                    $statusCounts = [];

                    foreach ($productsSnapshot as $doc) {
                        try {
                            if ($doc->exists()) {
                                $data = $doc->data();

                                if (!is_array($data)) {
                                    Log::warning('Invalid product data structure for document: ' . $doc->id());
                                    continue;
                                }

                                $price = $data['price'] ?? 0;
                                $status = $data['status'] ?? 'unknown';

                                $totalPrice += $price;
                                $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;

                                $products[] = [
                                    'id' => $doc->id(),
                                    'name' => $data['name'] ?? '',
                                    'price' => $price,
                                    'status' => $status,
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

                    return [
                        'products' => $products,
                        'statistics' => [
                            'total_products' => count($products),
                            'average_price' => count($products) > 0 ? round($totalPrice / count($products), 2) : 0,
                            'total_value' => $totalPrice,
                            'status_distribution' => $statusCounts
                        ]
                    ];
                } catch (\Throwable $e) {
                    Log::error('Error in products cache callback: ' . $e->getMessage(), [
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e;
                }
            });

            $endTime = microtime(true);
            $memoryEnd = memory_get_usage();

            Log::info('Firestore category products load metrics:', [
                'time_ms' => round(($endTime - $startTime) * 1000, 2),
                'memory_usage_mb' => round(($memoryEnd - $memoryStart) / 1024 / 1024, 2),
                'products_count' => count($products['products'])
            ]);

            return view('categories.show', [
                'category' => $category,
                'products' => $products['products'],
                'statistics' => $products['statistics']
            ]);
        } catch (\Throwable $e) {
            Log::error('Firestore Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'memory_usage' => memory_get_usage()
            ]);
            return response()->view('errors.firebase', [
                'message' => 'Failed to load category products from Firestore.',
            ], 500);
        }
    }
}
