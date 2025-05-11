<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoriesController extends Controller
{
    protected $firestore;

    public function __construct()
    {
        $this->firestore = app('firebase.firestore');
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

            return view('categories', compact('categories'));
        } catch (\Throwable $e) {
            Log::error('Firestore Error: ' . $e->getMessage());
            return response()->view('errors.firebase', [
                'message' => 'Failed to load categories from Firestore.',
            ], 500);
        }
    }
}
