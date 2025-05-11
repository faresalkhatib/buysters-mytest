<?php
namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;
use Kreait\Firebase\Messaging;

class FirebaseService
{
    protected $firebase;

    public function __construct()
    {
        $this->firebase = (new Factory)
            ->withServiceAccount(storage_path('app/firebase/firebase_credentials.json'));
    }

    public function getAuth(): Auth
    {
        return $this->firebase->createAuth();
    }

    public function getMessaging(): Messaging
    {
        return $this->firebase->createMessaging();
    }
}