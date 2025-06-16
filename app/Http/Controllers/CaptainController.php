<?php

namespace App\Http\Controllers;
use Google\Cloud\Firestore\FirestoreClient;
use Illuminate\Http\Request;                                      
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Kreait\Firebase\Contract\Firestore;


class CaptainController extends Controller
{
    protected $firestore;
     protected $storage;
    protected $bucket;

    public function __construct()
    {
        $this->firestore = app('firebase.firestore');
        $this->storage = app('firebase.storage');
        $this->bucket = $this->storage->getBucket('graduation-project-e47af.firebasestorage.app');
    }

    /**
     * Display a listing of the captains.
     */
    public function index()
    {
        $captains = [];
        $documents = $this->firestore
            ->collection('delivery')
            ->documents();

        foreach ($documents as $document) {
            if ($document->exists()) {
                $captain = $document->data();
                $captain['id'] = $document->id();
                $captains[] = (object) $captain;
            }
        }

        $captains = collect($captains);

        return view('captains', compact('captains'));
    }

    /**
     * Display the specified captain.
     */
    public function show($id)
    {
        $docRef = $this->firestore
            ->collection('delivery')
            ->document($id);

        $snapshot = $docRef->snapshot();

        if (!$snapshot->exists()) {
            abort(404);
        }

        $captain = (object) $snapshot->data();
        $captain->id = $snapshot->id();

        return view('captains.show', compact('captain'));
    }

    /**
     * Toggle captain status between 'active' and 'blocked'.
     */
    public function toggleStatus($id)
    {
        $docRef = $this->firestore
            ->collection('delivery')
            ->document($id);

        $snapshot = $docRef->snapshot();

        if (!$snapshot->exists()) {
            abort(404);
        }

        $currentStatus = $snapshot->data()['status'] ?? 'active';
        $newStatus = $currentStatus === 'active' ? 'blocked' : 'active';

        $docRef->set([
            'status' => $newStatus,
            'updated_at' => now()->format('Y-m-d H:i:s'),
        ], ['merge' => true]);

        return back()->with('success', 'Captain status updated!');

    }
public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'national_id' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'location' => 'required|string|max:255',
            'vehicle_model' => 'required|string|max:255',
            'vehicle_number' => 'required|string|max:255',
            'vehicle_type' => 'required|string|in:car,motorcycle,van,truck',
            'vehicle_color' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'stripe_payment_method_id' => 'nullable|string',
        ]);

        try {
            $imageUrl = null;

            // Handle image upload if provided
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $sanitizedName = preg_replace('/[^a-zA-Z0-9\.\-_]/', '_', $image->getClientOriginalName());
                $imageName = time() . '_' . $sanitizedName;
                $storagePath = 'captain_images/' . $imageName;

                $fileContent = file_get_contents($image->getRealPath());
                if ($fileContent === false) {
                    throw new \Exception('Could not read image file.');
                }

                $object = $this->bucket->upload($fileContent, [
                    'name' => $storagePath,
                    'metadata' => ['contentType' => $image->getMimeType()],
                ]);

                $object->update([], ['predefinedAcl' => 'PUBLICREAD']);

                $imageUrl = sprintf(
                    'https://firebasestorage.googleapis.com/v0/b/%s/o/%s?alt=media',
                    $this->bucket->name(),
                    urlencode($storagePath)
                );
            }

            $captainData = [
                'Vehicle_Infos' => [
                    'vehicle_color' => $validated['vehicle_color'],
                    'vehicle_model' => $validated['vehicle_model'],
                    'vehicle_number' => $validated['vehicle_number'],
                    'vehicle_type' => $validated['vehicle_type'],
                ],
                'date_of_birth' => $validated['date_of_birth'],
                'email' => $validated['email'],
                'image_url' => $imageUrl,
                'joining_date' => now()->format('Y-m-d H:i:s'),
                'location' => $validated['location'],
                'name' => $validated['name'],
                'national_id' => $validated['national_id'],
                'phone_number' => $validated['phone'],
                'role' => 'delivery',
                'status' => 'active',
                'stripePaymentMethodId' => $validated['stripe_payment_method_id'] ?? null,
                'updated_at' => now()->format('Y-m-d H:i:s'),
                'deleted_at' => null
            ];

            $this->firestore->collection('delivery')->add($captainData);

            Cache::forget('captains_index_list');
            return redirect()->route('captains.index')->with('success', 'Captain created successfully.');
        } catch (\Throwable $e) {
            Log::error('Firebase Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withInput()->with('error', 'Captain creation failed: ' . $e->getMessage());
        }
    }


}
