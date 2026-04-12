<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Notification;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index($lang = null)
    {
        
        if (auth()->user()) {
            $user_id = auth()->user()->id;
            $user = User::whereId($user_id)->first();
            $transactions = Transaction::whereHas('booking.ride', function ($query) use ($user_id) {
                $query->where('added_by', $user_id);
            })
                ->orderBy('id', 'desc')
                ->get();

            return view('transactions', [
                'user' => $user,
                'transactions' => $transactions,
                'selectedLanguage' => $this->selectedLanguage
            ]);
        } else {
            return redirect()->route('home', ['lang' => $this->selectedLanguage->abbreviation]);
        }
    }
}
