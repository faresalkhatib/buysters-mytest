<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UsersController extends Controller
{
    protected $firestore;

    public function __construct()
    {
        $this->firestore = app('firebase.firestore');
    }

    public function index()
    {
        try {
            $usersCollection = $this->firestore->collection('users');
            $documents = $usersCollection->documents();

            $users = [];
            foreach ($documents as $document) {
                if ($document->exists()) {
                    $data = $document->data();

                    $location = $data['location'] ?? null;
                    $latitude = null;
                    $longitude = null;

                    if (is_object($location) && method_exists($location, 'latitude') && method_exists($location, 'longitude')) {
                        $latitude = $location->latitude();
                        $longitude = $location->longitude();
                    } elseif (is_array($location) && isset($location['latitude']) && isset($location['longitude'])) {
                        $latitude = $location['latitude'];
                        $longitude = $location['longitude'];
                    }

                    $users[] = [
                        'username' => $data['username'] ?? null,
                        'email' => $data['email'] ?? null,
                        'image_url' => $data['image_url'] ?? null,
                        'role' => $data['role'] ?? null,
                        'phone_number' => $data['phone_number'] ?? null,
                        'stripePaymentMethodId' => $data['stripePaymentMethodId'] ?? null,
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                    ];
                }
            }

            return view('users', compact('users'));
        } catch (\Throwable $e) {
            Log::error('Firestore Error: ' . $e->getMessage());

            return response()->view('errors.firebase', [
                'message' => 'Failed to load users from Firestore.',
            ], 500);
        }
    }
}
