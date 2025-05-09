<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FirebaseController extends Controller
{

    protected $auth;
    protected $database;
    protected $firestore;

    public function __construct()
    {
        $this->auth = app('firebase.auth');
        $this->database = app('firebase.database');
        $this->firestore = app('firebase.firestore');
    }

    public function index()
    {
        try {
            $usersCollection = $this->firestore->collection('users');
            $users = $usersCollection->documents();

            return view('users', compact('users'));
        } catch (\Throwable $e) {
            Log::error('Firestore Error: ' . $e->getMessage());

            return response()->view('errors.firebase', [
                'message' => 'Failed to load users from Firestore.',
            ], 500);
        }
    }
}
