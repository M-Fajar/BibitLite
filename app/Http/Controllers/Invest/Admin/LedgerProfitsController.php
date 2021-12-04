<?php

namespace App\Http\Controllers\Invest\Admin;

use App\Enums\LedgerTnxType;
use App\Enums\InvestmentStatus;
use App\Models\IvInvest;
use App\Models\IvLedger;
use App\Models\IvProfit;

use App\Filters\LedgerFilter;
use App\Filters\ProfitFilter;
use App\Services\InvestormService;
use App\Services\Investment\IvProfitCalculator;
use App\Traits\WrapInTransaction;


use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class LedgerProfitsController extends Controller
{
    use WrapInTransaction;

    private $investment;

    public function __construct(InvestormService $investment)
    {
        $this->investment = $investment;
    }

    public function transactionList(Request $request, $type = null)
    {
        $input = array_filter($request->only(['type', 'source', 'query']));
        $filterCount = count(array_filter($input, function ($item) {
            return !empty($item) && $item !== 'any';
        }));
        $filter = new LedgerFilter(new Request(array_merge($input, ['type' => $type ?? $request->get('type')])));
        $ledgers = get_enums(LedgerTnxType::class, false);
        $sources = [AccType('main'), AccType('invest')];

        $transactionQuery = IvLedger::orderBy('id', user_meta('iv_tnx_order', 'desc'))
            ->filter($filter);

        $transactions = $transactionQuery->paginate(user_meta('iv_tnx_perpage', 10))->onEachSide(0);


        return view('investment.admin.statement.transactions', [
            'transactions' => $transactions,
            'sources' => $sources,
            'ledgers' => $ledgers,
            'type' => $type ?? 'all',
            'filter_count' => $filterCount,
            'input' => $input,
        ]);
    }

    public function profitList(Request $request, ProfitFilter $filter, $type = null)
    {
        $profitQuery = IvProfit::orderBy('id', user_meta('iv_profit_order', 'desc'))->with(['invest', 'invest_by'])
            ->filter($filter);

        if ($type == 'pending') {
            $profitQuery->whereNull('payout');
        }

        $profits = $profitQuery->paginate(user_meta('iv_profit_perpage', 20))->onEachSide(0);

        return view('investment.admin.statement.profits', [
            'profits' => $profits,
            'type' => $type ?? 'all'
        ]);
    }

    public function processProfits()
    {
        if (is_locked('profit')) {
            throw ValidationException::withMessages(['invalid' => __("Sorry, one of your system administrator is working, please try again after few minutes.")]);
        }

        $payout = IvProfit::whereNull('payout');
        $amount = $payout->sum('amount');
        $total = $payout->count();

        $getProfits = $payout->orderBy('id', 'asc')->get()->groupBy('user_id');

        $userByProfits = $getProfits->map(function ($items, $key) {
            return $items->keyBy('id')->keys()->toArray();
        })->toArray();

        $accounts = [];
        foreach ($userByProfits as $user => $profits) {
            $accounts[] = [$user => $profits];
        }

        if (empty($accounts)) {
            throw ValidationException::withMessages(['invalid' => ['title' => __('No pending profits!'), 'msg' => __('There is no pending profits to approve.')]]);
        }

        return view('investment.admin.misc.modal-process-profits', [ 'accounts' => $accounts, 'amount' => $amount, 'total' => $total ]);
    }

    public function processPayoutProfits(Request $request)
    {
        if (empty($request->get('done')) && is_locked('profit')) {
            throw ValidationException::withMessages(['invalid' => __("Sorry, one of your system administrator is working, please try again after few minutes.")]);
        }

        $request->validate([
            'batchs' => 'required|array',
            'action' => 'nullable',
            'done' => 'nullable',
            'total' => 'nullable',
            'idx' => 'nullable',
        ], [
            'batchs.*' => __("Sorry, unable to proceed for invalid data format.") . ' ' . __("Please reload the page and try again.")
        ]);

        $batchs = $request->get('batchs');
        $done = (int) $request->get('done', 0);
        $total = (int) $request->get('total', 0);
        $idx = (int) $request->get('idx', 0);

        if (!empty($batchs) && is_array($batchs)) {
            foreach ($batchs as $user_id => $profits) {
                $this->wrapInTransaction(function ($profits, $user_id) {
                    $this->investment->proceedPayout($user_id, $profits);
                }, $profits, $user_id);
                $done++;
            }
        }

        $left = ($total - $done);
        $progress = (($done / $total) * 100);
        $next = ($left == 0 || $total <= $done) ? false : true;

        if ($left == 0) {
            $invests = IvInvest::where('status', InvestmentStatus::ACTIVE)->get();
            if (!blank($invests)) {
                foreach ($invests as $invest) {
                    $this->wrapInTransaction(function ($invest) {
                        $this->investment->processCompleteInvestment($invest);
                    }, $invest);
                }
            }

            upss('payout_locked_profit', null);
            $message = __("All profits successfully approved and release the locked amount from user account.");
        } else {
            $message = __("Profits batch processed.");
        }

        return response()->json([
            'status' => 'success', 'message' => $message, 'idx' => ($idx + 1),
            'done' => $done, 'total' => $total, 'progress' => $progress, 'next' => $next
        ]);
    }
}
