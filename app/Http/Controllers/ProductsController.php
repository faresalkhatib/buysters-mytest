<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductsController extends Controller
{
    protected  $firestore;
    public function __construct()
    {
        $this->firestore = app('firebase.firestore');
    }

    public function index(){

        $categoriesSnapshot = $this->firestore->collection('categories')->documents();
        $categories = [];
        foreach ($categoriesSnapshot as $catDoc) {
            if ($catDoc->exists()) {
                $categories[$catDoc->id()] = $catDoc->get('name');
            }
        }

        $productsSnapshot = $this->firestore->collection('products')->documents();
        $products = [];

        foreach ($productsSnapshot as $doc) {
            if ($doc->exists()) {
                $data = $doc->data();
                $categoryName = $categories[$data['categoryId']] ?? 'Unknown';

                $products[] = [
                    'name' => $data['name'] ?? '',
                    'price' => $data['price'] ?? 0,
                    'category' => $categoryName,
                    'status' => $data['status'] ?? '',
                    'seller' => $data['seller_ifos']['seller_email'] ?? '',
                ];
            }
        }

        return view('products', compact('products'));
    }


}
