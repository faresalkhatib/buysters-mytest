<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    protected $firestore;

    public function __construct()
    {
        $this->firestore = app('firebase.firestore');
    }

    public function index()
    {
        try {
            // Fetch orders from Firestore (removed limit to get all orders for accurate metrics)
            $ordersCollection = $this->firestore->collection('orders');
            $documents = $ordersCollection->orderBy('timestamp', 'DESC')->documents();

            $orders = [];
            $totalRevenue = 0;
            foreach ($documents as $document) {
                if ($document->exists()) {
                    $data = $document->data();
                    $orders[] = [
                        'id' => $document->id(),
                        'status' => $data['status'] ?? null,
                        'buyer_id' => $data['buyer_id'] ?? null,
                        'product_infos' => $data['product_infos'] ?? [],
                        'seller_location' => $data['seller_location'] ?? null,
                        'total_amount' => $data['total_amount'] ?? 0,
                        'timestamp' => $data['timestamp'] ?? null,
                    ];
                    $totalRevenue += $data['product_infos']['total_amount'] ?? 0;
                }
            }

            $usersCollection = $this->firestore->collection('users');
            $productsCollection = $this->firestore->collection('products');

            $totalOrders = count($orders);
            $totalUsers = $usersCollection->documents()->size();
            $totalProducts = $productsCollection->documents()->size();

            // Calculate orders per day instead of daily revenue
            $ordersPerDay = [];
            $orderStatusCounts = [];

            foreach ($orders as $order) {
                $date = isset($order['timestamp'])
                    ? date('Y-m-d', strtotime($order['timestamp']->__toString()))
                    : null;

                if ($date) {
                    $ordersPerDay[$date] = ($ordersPerDay[$date] ?? 0) + 1;
                }

                $status = $order['status'] ?? 'unknown';
                $orderStatusCounts[$status] = ($orderStatusCounts[$status] ?? 0) + 1;
            }

            // Sort by date (newest first)
            krsort($ordersPerDay);

            // Get only the 10 most recent orders for the table
            $recentOrders = array_slice($orders, 0, 10);

            return view('panel', [
                'orders' => $recentOrders,
                'totalOrders' => $totalOrders,
                'totalUsers' => $totalUsers,
                'totalProducts' => $totalProducts,
                'totalRevenue' => $totalRevenue,
                'ordersPerDay' => $ordersPerDay,
                'orderStatusCounts' => $orderStatusCounts,
            ]);
        } catch (\Throwable $e) {
            Log::error('Firestore Error: ' . $e->getMessage());
            return response()->view('errors.firebase', [
                'message' => 'Failed to load dashboard data from Firestore.',
            ], 500);
        }
    }
}