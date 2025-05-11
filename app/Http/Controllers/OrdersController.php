<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrdersController extends Controller
{
    protected $firestore;

    public function __construct()
    {
        $this->firestore = app('firebase.firestore');
    }

    public function index()
    {
        try {
            $ordersCollection = $this->firestore->collection('orders');
            $documents = $ordersCollection->documents();

            $orders = [];
            foreach ($documents as $document) {
                if ($document->exists()) {
                    $data = $document->data();
                    $orders[] = [
                        'id' => $document->id(),
                        'acceptance_date' => $data['acceptance_date'] ?? null,
                        'buyer_id' => $data['buyer_id'] ?? null,
                        'delivered_date' => $data['delivered_date'] ?? null,
                        'delivery_person_id' => $data['delivery_person_id'] ?? null,
                        'is_received' => $data['is_received'] ?? false,
                        'payment_status' => $data['payment_status'] ?? null,
                        'pick_up_date' => $data['pick_up_date'] ?? null,
                        'product_infos' => $data['product_infos'] ?? [],
                        'seller_id' => $data['seller_id'] ?? null,
                        'seller_location' => $data['seller_location'] ?? null,
                        'seller_phone_number' => $data['seller_phone_number'] ?? null,
                        'status' => $data['status'] ?? null,
                        'timestamp' => $data['timestamp'] ?? null,
                    ];
                }
            }

            return view('orders', compact('orders'));
        } catch (\Throwable $e) {
            Log::error('Firestore Error: ' . $e->getMessage());
            return response()->view('errors.firebase', [
                'message' => 'Failed to load orders from Firestore.',
            ], 500);
        }
    }
}
