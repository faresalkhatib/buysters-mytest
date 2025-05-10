<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    protected $auth;
    protected $firestore;
    public function __construct(){
        $this->auth = app('firebase.auth');
        $this->firestore = app('firebase.firestore');
    }

    public function index()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $idToken = $request->input('idToken');

        try {
            $verifiedIdToken = $this->auth->verifyIdToken($idToken);
            $uid = $verifiedIdToken->claims()->get('sub');
            $user = $this->auth->getUser($uid);

            $role = $this->firestore
                ->collection('users')
                ->document($uid)
                ->snapshot()
                ->get('role') ?? 'customer';

            Session::put('user', [
                'uid' => $user->uid,
                'email' => $user->email,
                'role' => $role,
            ]);

            return redirect('/');
        } catch (\Throwable $e) {
            Log::error('Firebase token verification failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Invalid token.',
            ], 401);
        }
    }

    public function logout()
    {
        Session::forget('user');
        return redirect('/login');
    }
}
