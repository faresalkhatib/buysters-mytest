<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UsersController extends Controller
{

    protected $auth;
    protected $database;
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
                    $users[] = [
                        'username' => $data['username'] ?? null,
                        'email' => $data['email'] ?? null,
                        'image_url' => $data['image_url'] ?? null,
                        'role' => $data['role'] ?? null,
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
