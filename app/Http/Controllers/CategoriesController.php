<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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

    public function index(){
        try{
            $categoriesCollection = $this->firestore->collection('categories');
            $documents = $categoriesCollection->documents();

            $categories = [];

            foreach($documents as $document){
                $categories[] = [
                    'id' => $document->id(),
                    'name' => $document->data()['name'],
                    'image_url' => $document->data()['imageUrl']
                ];
            }

            return view('categories.index', compact('categories'));
        } catch (\Throwable $e) {
            Log::error('Firestore Error: ' . $e->getMessage());
            return response()->view('errors.firebase', [
                'message' => 'Failed to load categories from Firestore.',
            ], 500);
        }
    }

    public function create(){
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        try {
            $storage = app('firebase.storage');
            $bucket = $storage->getBucket();

            $image = $request->file('image');
            $sanitizedName = preg_replace('/[^a-zA-Z0-9\.\-_]/', '_', $image->getClientOriginalName());
            $imageName = time() . '_' . $sanitizedName;
            $storagePath = 'category_images/' . $imageName;

            $stream = fopen($image->getRealPath(), 'r');
            $object = $bucket->upload($stream, [
                'name' => $storagePath,
                'metadata' => ['contentType' => $image->getMimeType()],
            ]);
            fclose($stream);

            $object->update([], ['predefinedAcl' => 'PUBLICREAD']);

            $imageUrl = sprintf(
                'https://firebasestorage.googleapis.com/v0/b/%s/o/%s?alt=media',
                $bucket->name(),
                urlencode($storagePath)
            );

            // Save to Firestore
            $firestore = app('firebase.firestore')->database();
            $firestore->collection('categories')->add([
                'name' => $validated['name'],
                'imageUrl' => $imageUrl,
            ]);

            return redirect()->route('categories.index')->with('success', 'Category created successfully.');

        } catch (\Throwable $e) {
            Log::error('Firebase Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withInput()->with('error', 'Category creation failed: ' . $e->getMessage());
        }
    }


    public function edit($id){
        try{
            $categoryDocument = $this->firestore->collection('categories')->document($id);
            $category = $categoryDocument->snapshot()->data();

            if(!$category){
                return redirect()->route('category')->with('error', 'Category not found.');
            }

            return view('categories.edit', compact('category'));
        } catch (\Throwable $e) {
            Log::error('Firestore Error: ' . $e->getMessage());
            return response()->view('errors.firebase', [
                'message' => 'Failed to load category from Firestore.',
            ], 500);
        }
    }

    public function update(Request $request, $id){
        $request->validate([
            'name' => 'required|string|max:255',
            'image_url' => 'required|url'
        ]);

        try{
            $categoryDocument = $this->firestore->collection('categories')->document($id);
            $categoryDocument->set([
                'name' => $request->input('name'),
                'imageUrl' => $request->input('image_url')
            ]);

            return redirect()->route('category')->with('success', 'Category updated successfully.');
        } catch (\Throwable $e) {
            Log::error('Firestore Error: ' . $e->getMessage());
            return response()->view('errors.firebase', [
                'message' => 'Failed to update category in Firestore.',
            ], 500);
        }
    }

    public function destroy($id){
        try{
            $categoryDocument = $this->firestore->collection('categories')->document($id);
            $categoryDocument->delete();

            return redirect()->route('category')->with('success', 'Category deleted successfully.');
        } catch (\Throwable $e) {
            Log::error('Firestore Error: ' . $e->getMessage());
            return response()->view('errors.firebase', [
                'message' => 'Failed to delete category in Firestore.',
            ], 500);
        }
    }

    public function show($id){
        try{
            $categoryDocument = $this->firestore->collection('categories')->document($id);
            $category = $categoryDocument->snapshot()->data();

            if(!$category){
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
