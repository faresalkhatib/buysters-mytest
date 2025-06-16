<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;

class TransactionsController extends Controller
{
    protected $firestore;

    public function __construct()
    {
        $this->firestore = app('firebase.firestore');
    }

    /**
     * Display a listing of transactions.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $transactionsCollection = $this->firestore->collection('transactions');
            $documents = $transactionsCollection->orderBy('date', 'DESC')->documents();

            $transactions = [];
            foreach ($documents as $document) {
                if ($document->exists()) {
                    $data = $document->data();
                    $transactions[] = [
                        'id' => $document->id(),
                        'amount' => $data['amount'] ?? 0,
                        'date' => $this->formatDate($data['date'] ?? null),
                        'order_id' => $data['order_id'] ?? null,
                        'receiver_id' => $data['receiver_id'] ?? null,
                        'sender_id' => $data['sender_id'] ?? null,
                        'status' => $data['status'] ?? null,
                        'type' => $data['type'] ?? null,
                    ];
                }
            }

            return response()->view('transactions', [ // Changed to your blade file name
                'transactions' => $transactions,
                'totalTransactions' => count($transactions),
            ]);

        } catch (\Throwable $e) {
            Log::error('Firestore Transaction Error: ' . $e->getMessage());
            return response()->view('transaction', [ // Changed to your blade file name
                'transactions' => [],
                'totalTransactions' => 0,
                'error' => 'Failed to load transactions data.'
            ]);
        }
    }

    /**
     * Remove the specified transaction from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            $transactionRef = $this->firestore->collection('transactions')->document($id);
            $transactionRef->delete();

            return redirect()->route('transactions.index')
                             ->with('success', 'Transaction deleted successfully');

        } catch (\Throwable $e) {
            Log::error('Firestore Delete Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete transaction.');
        }
    }

    /**
     * Format Firestore timestamp to readable date
     */
    private function formatDate($timestamp)
    {
        if ($timestamp instanceof \Google\Cloud\Core\Timestamp) {
            $dateTime = $timestamp->get();
            if ($dateTime instanceof \DateTimeInterface) {
                return $dateTime->format('M j, Y \a\t g:i A');
            }
        }
        return $timestamp;
    }
}