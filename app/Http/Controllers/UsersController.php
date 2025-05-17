<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

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
            $startTime = microtime(true);

            $users = Cache::remember('users_list', now()->addMinutes(5), function () {
                $usersCollection = $this->firestore->collection('users')
                    ->select([
                        'username',
                        'email',
                        'image_url',
                        'role',
                        'phone_number',
                        'stripePaymentMethodId',
                        'location'
                    ]);

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
                        } elseif (is_array($location)) {
                            $latitude = $location['latitude'] ?? null;
                            $longitude = $location['longitude'] ?? null;
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

                return $users;
            });

            $endTime = microtime(true);
            Log::info('Firestore user load time: ' . round(($endTime - $startTime) * 1000, 2) . 'ms');

            return view('users', compact('users'));
        } catch (\Throwable $e) {
            Log::error('Firestore Error: ' . $e->getMessage());

            return response()->view('errors.firebase', [
                'message' => 'Failed to load users from Firestore.',
            ], 500);
        }
    }
}
