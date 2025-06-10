<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;

class AuthController extends Controller
{
    protected $auth;
    protected $firestore;

    public function __construct()
    {
        $this->auth = app('firebase.auth');
        $this->firestore = app('firebase.firestore');
    }

    public function index()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'idToken' => 'required|string',
        ]);

        $idToken = $request->input('idToken');

        try {
            $verifiedIdToken = $this->auth->verifyIdToken($idToken);
            $uid = $verifiedIdToken->claims()->get('sub');
            $email = $verifiedIdToken->claims()->get('email');

            $role = $verifiedIdToken->claims()->get('role');

            if (!$role) {
                $role = cache()->remember("user_role_{$uid}", now()->addMinutes(60), function () use ($uid) {
                    return $this->firestore
                        ->collection('users')
                        ->document($uid)
                        ->snapshot()
                        ->get('role') ?? 'customer';
                });
            }

            if ($role !== 'admin') {
                return redirect()->back()->withErrors([
                    'auth' => 'Access denied. Admins only.',
                ]);
            }

            Session::put('user', [
                'uid' => $uid,
                'email' => $email,
                'role' => $role,
            ]);

            return redirect('/');
        } catch (FailedToVerifyToken $e) {
            Log::error('Token verification failed: ' . $e->getMessage());

            return redirect()->back()->withErrors([
                'auth' => 'Your session has expired or the token is invalid.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Login error: ' . $e->getMessage());

            return redirect()->back()->withErrors([
                'auth' => 'An unexpected error occurred. Please try again.',
            ]);
        }
    }

    public function logout()
    {
        Session::forget('user');
        return redirect('/login');
    }
}
