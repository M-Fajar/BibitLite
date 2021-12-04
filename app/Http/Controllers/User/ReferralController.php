<?php


namespace App\Http\Controllers\User;

use App\Models\Transaction;
use App\Models\Referral;

use App\Enums\TransactionType;
use App\Enums\TransactionStatus;

use App\Http\Controllers\Controller;
class ReferralController extends Controller
{
    public function index()
    {
    	if(!referral_system()) {
	    	return redirect()->route('dashboard')->withErrors(['warning' => __('Sorry, the page you are looking for could not be found.')]);
    	}

        $refers = Referral::where('refer_by', auth()->user()->id);
        $transactions = Transaction::where('type', TransactionType::REFERRAL)->where('user_id', auth()->user()->id)->orderBy('id', 'desc')->get();

        $bonusRecieved = $transactions->where('status', TransactionStatus::COMPLETED)->sum('amount');
        $bonusPending = $transactions->where('status', TransactionStatus::PENDING)->sum('amount');

        $stats = [
            'refer' => $refers->count(),
            'recieved' => $bonusRecieved,
            'pending' => $bonusPending,
        ];

        return view('user.referrals.index', compact('stats', 'transactions'));
    }
}
