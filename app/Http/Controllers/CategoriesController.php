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

            $categories = Cache::remember('categories_index_list', now()->addMinutes(1), function () {
                $categoriesSnapshot = $this->firestore->collection('categories')
                    ->documents();

                $categories = [];
                foreach ($categoriesSnapshot as $document) {
                    if ($document->exists()) {
                        $data = $document->data();
                        if (!isset($data['deleted_at'])) {
                            $categories[] = [
                                'id' => $document->id(),
                                'name' => $data['name'],
                                'image_url' => $data['imageUrl'] ?? ''
                            ];
                        }
                    }
                }
                return $categories;
            });

            $endTime = microtime(true);
            Log::info('Firestore category load time: ' . round(($endTime - $startTime) * 1000, 2) . 'ms');

            return view('categories.index', compact('categories'));
        } catch (\Throwable $e) {
            Log::error('Firestore Error: ' . $e->getMessage());
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
            $categoryDocument = $this->firestore->collection('categories')->document($id);
            $category = $categoryDocument->snapshot()->data();

            if (!$category) {
                return redirect()->route('category')->with('error', 'Category not found.');
            }

            return view('categories.show', compact('category'));
        } catch (\Throwable $e) {
            Log::error('Firestore Error: ' . $e->getMessage());
            return response()->view('errors.firebase', [
                'message' => 'Failed to load category from Firestore.',
            ], 500);
        }
    }
}
