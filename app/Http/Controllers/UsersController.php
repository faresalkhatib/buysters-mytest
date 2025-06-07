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
            $users = Cache::remember('users_list', now()->addMinutes(5), function () {
                $usersCollection = $this->firestore->collection('users')
                    ->select([
                        'username',
                        'email',
                        'image_url',
                        'role',
                        'phone_number',
                        'stripePaymentMethodId',
                        'location',
                        'status'
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
                            'id' => $document->id(),
                            'username' => $data['username'] ?? null,
                            'email' => $data['email'] ?? null,
                            'image_url' => $data['image_url'] ?? null,
                            'role' => $data['role'] ?? null,
                            'phone_number' => $data['phone_number'] ?? null,
                            'stripePaymentMethodId' => $data['stripePaymentMethodId'] ?? null,
                            'latitude' => $latitude,
                            'longitude' => $longitude,
                            'status' => $data['status'] ?? 'active',
                        ];
                    }
                }

                return $users;
            });

            return view('users', compact('users'));
        } catch (\Throwable $e) {
            Log::error('Firestore Error: ' . $e->getMessage());

            return response()->view('errors.firebase', [
                'message' => 'Failed to load users from Firestore.',
            ], 500);
        }
    }

    public function toggleBlock($userId)
    {
        try {
            Log::info('Attempting to toggle block status', [
                'user_id' => $userId,
                'session_user' => session('user')
            ]);

            $userRef = $this->firestore->collection('users')->document($userId);
            $userDoc = $userRef->snapshot();

            if (!$userDoc->exists()) {
                Log::error('User not found', ['user_id' => $userId]);
                return redirect()->back()->with('error', 'User not found');
            }

            $currentData = $userDoc->data();
            Log::info('Current user data', ['data' => $currentData]);

            if (($currentData['role'] ?? '') === 'admin') {
                Log::warning('Attempt to block admin user', ['user_id' => $userId]);
                return redirect()->back()->with('error', 'Cannot block admin users');
            }

            $currentUserId = session('user.uid');
            if (!$currentUserId) {
                Log::error('No user ID in session');
                return redirect()->back()->with('error', 'Session error: Please log in again');
            }

            if ($currentUserId === $userId) {
                Log::warning('Attempt to block self', ['user_id' => $userId]);
                return redirect()->back()->with('error', 'Cannot block yourself');
            }

            $newStatus = ($currentData['status'] ?? 'active') === 'active' ? 'blocked' : 'active';
            Log::info('Updating user status', [
                'user_id' => $userId,
                'old_status' => $currentData['status'] ?? 'active',
                'new_status' => $newStatus
            ]);

            $userRef->set([
                'status' => $newStatus
            ], ['merge' => true]);

            Cache::forget('users_list');

            $status = $newStatus === 'blocked' ? 'blocked' : 'unblocked';
            return redirect()->back()->with('success', "User successfully {$status}");
        } catch (\Throwable $e) {
            Log::error('Firestore Error in toggleBlock', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $userId
            ]);
            return redirect()->back()->with('error', 'Failed to update user status: ' . $e->getMessage());
        }
    }
}
