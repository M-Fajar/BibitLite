<?php

namespace App\Models;

use App\Models\User;
use App\Filters\Filterable;
use App\Enums\LedgerTnxType;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class IvLedger extends Model
{
    use Filterable;

    protected $fillable = [
        'ivx',
        'user_id',
        'type',
        'calc',
        'amount',
        'fees',
        'total',
        'currency',
        'desc',
        'remarks',
        'note',
        'invest_id',
        'tnx_id',
        'reference',
        'meta',
        'source',
        'created_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invest()
    {
        return $this->belongsTo(IvInvest::class, 'invest_id');
    }

    public function scopeLoggedUser($query)
    {
        return $query->where('user_id', auth()->user()->id);
    }

    public function scopeIsInvestment($query)
    {
        return $query->where('type', LedgerTnxType::INVEST);
    }

    public function scopeIsProfit($query)
    {
        return $query->where('type', LedgerTnxType::PROFIT);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereBetween('created_at', [
            Carbon::now()->startOfMonth(), 
            Carbon::now()->endOfMonth()
        ]);
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [
            Carbon::now()->startOfWeek(), 
            Carbon::now()->endOfWeek()
        ]);
    }

    public function scopeLastWeek($query)
    {
        return $query->whereBetween('created_at', [
            Carbon::now()->subWeek()->startOfWeek(),
            Carbon::now()->subWeek()->endOfWeek()
        ]);
    }

    public function scopeFromLastWeek($query)
    {
        return $query->whereBetween('created_at', [
            Carbon::now()->subWeek()->startOfWeek(),
            Carbon::now()
        ]);
    }

    public function scopeLastMonth($query)
    {
        return $query->whereBetween('created_at', [
            Carbon::now()->subMonth()->startOfMonth(),
            Carbon::now()->subMonth()->endOfMonth()
        ]);
    }

    public function scopeThisYear($query)
    {
        return $query->whereBetween('created_at', [
            Carbon::now()->startOfYear(),
            Carbon::now()->endOfYear()
        ]);
    }

    public function scopeLastYear($query)
    {
        return $query->whereBetween('created_at', [
            Carbon::now()->subYear()->startOfYear(),
            Carbon::now()->subYear()->endOfYear()
        ]);
    }

    public static function statistics() {
        $invest = self::where('type', LedgerTnxType::INVEST)->get();
        $profit = self::where('type', LedgerTnxType::PROFIT)->get();
        $transfer = self::where('type', LedgerTnxType::TRANSFER)->get();

        $this30day  = [ Carbon::now()->subDays(30)->startOfDay(), Carbon::now()->today()->endOfDay() ];
        $last30day  = [ Carbon::now()->subDays(60)->startOfDay(), Carbon::now()->subDays(30)->endOfDay() ];

        $thisWeek   = [ Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek() ];
        $lastWeek   = [ Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek() ];
        $thisMonth  = [ Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth() ];
        $lastMonth  = [ Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth() ];
        $thisYear   = [ Carbon::now()->startOfYear(), Carbon::now()->endOfYear() ];
        $lastYear   = [ Carbon::now()->subYear()->startOfYear(), Carbon::now()->subYear()->endOfYear() ];

        $data = [
            '30day' => [
                'plan' => $invest->whereBetween('created_at', $this30day)->count(),
                'amount' => $invest->whereBetween('created_at', $this30day)->sum('amount'),
                'profit' => $profit->whereBetween('created_at', $this30day)->sum('amount'),
                'transfer' => $transfer->whereBetween('created_at', $this30day)->sum('amount'),
                'diff' => [
                    'plan' => to_dfp($invest->whereBetween('created_at', $this30day)->count(), $invest->whereBetween('created_at', $last30day)->count()),
                    'amount' => to_dfp($invest->whereBetween('created_at', $this30day)->sum('amount'), $invest->whereBetween('created_at', $last30day)->sum('amount')),
                    'profit' => to_dfp($profit->whereBetween('created_at', $this30day)->sum('amount'), $profit->whereBetween('created_at', $last30day)->sum('amount')),
                    'transfer' => to_dfp($transfer->whereBetween('created_at', $this30day)->sum('amount'), $transfer->whereBetween('created_at', $last30day)->sum('amount')),
                ],
                'last' => [
                    'plan' => $invest->whereBetween('created_at', $last30day)->count(),
                    'amount' => $invest->whereBetween('created_at', $last30day)->sum('amount'),
                    'profit' => $profit->whereBetween('created_at', $last30day)->sum('amount'),
                    'transfer' => $transfer->whereBetween('created_at', $last30day)->sum('amount'),
                ]
            ],
            'week' => [
                'plan' => $invest->whereBetween('created_at', $thisWeek)->count(),
                'amount' => $invest->whereBetween('created_at', $thisWeek)->sum('amount'),
                'profit' => $profit->whereBetween('created_at', $thisWeek)->sum('amount'),
                'transfer' => $transfer->whereBetween('created_at', $thisWeek)->sum('amount'),
                'diff' => [
                    'plan' => to_dfp($invest->whereBetween('created_at', $thisWeek)->count(), $invest->whereBetween('created_at', $lastWeek)->count()),
                    'amount' => to_dfp($invest->whereBetween('created_at', $thisWeek)->sum('amount'), $invest->whereBetween('created_at', $lastWeek)->sum('amount')),
                    'profit' => to_dfp($profit->whereBetween('created_at', $thisWeek)->sum('amount'), $profit->whereBetween('created_at', $lastWeek)->sum('amount')),
                    'transfer' => to_dfp($transfer->whereBetween('created_at', $thisWeek)->sum('amount'), $transfer->whereBetween('created_at', $lastWeek)->sum('amount')),
                ],
                'last' => [
                    'plan' => $invest->whereBetween('created_at', $lastWeek)->count(),
                    'amount' => $invest->whereBetween('created_at', $lastWeek)->sum('amount'),
                    'profit' => $profit->whereBetween('created_at', $lastWeek)->sum('amount'),
                    'transfer' => $transfer->whereBetween('created_at', $lastWeek)->sum('amount'),
                ]
            ],
            'month' => [
                'plan' => $invest->whereBetween('created_at', $thisMonth)->count(),
                'amount' => $invest->whereBetween('created_at', $thisMonth)->sum('amount'),
                'profit' => $profit->whereBetween('created_at', $thisMonth)->sum('amount'),
                'transfer' => $transfer->whereBetween('created_at', $thisMonth)->sum('amount'),
                'diff' => [
                    'plan' => to_dfp($invest->whereBetween('created_at', $thisMonth)->count(), $invest->whereBetween('created_at', $lastMonth)->count()),
                    'amount' => to_dfp($invest->whereBetween('created_at', $thisMonth)->sum('amount'), $invest->whereBetween('created_at', $lastMonth)->sum('amount')),
                    'profit' => to_dfp($profit->whereBetween('created_at', $thisMonth)->sum('amount'), $profit->whereBetween('created_at', $lastMonth)->sum('amount')),
                    'transfer' => to_dfp($transfer->whereBetween('created_at', $thisMonth)->sum('amount'), $transfer->whereBetween('created_at', $lastMonth)->sum('amount')),
                ],
                'last' => [
                    'plan' => $invest->whereBetween('created_at', $lastMonth)->count(),
                    'amount' => $invest->whereBetween('created_at', $lastMonth)->sum('amount'),
                    'profit' => $profit->whereBetween('created_at', $lastMonth)->sum('amount'),
                    'transfer' => $transfer->whereBetween('created_at', $lastMonth)->sum('amount'),
                ]
            ],
            'year' => [
                'plan' => $invest->whereBetween('created_at', $thisYear)->count(),
                'amount' => $invest->whereBetween('created_at', $thisYear)->sum('amount'),
                'profit' => $profit->whereBetween('created_at', $thisYear)->sum('amount'),
                'transfer' => $transfer->whereBetween('created_at', $thisYear)->sum('amount'),
                'diff' => [
                    'plan' => to_dfp($invest->whereBetween('created_at', $thisYear)->count(), $invest->whereBetween('created_at', $lastYear)->count()),
                    'amount' => to_dfp($invest->whereBetween('created_at', $thisYear)->sum('amount'), $invest->whereBetween('created_at', $lastYear)->sum('amount')),
                    'profit' => to_dfp($profit->whereBetween('created_at', $thisYear)->sum('amount'), $profit->whereBetween('created_at', $lastYear)->sum('amount')),
                    'transfer' => to_dfp($transfer->whereBetween('created_at', $thisYear)->sum('amount'), $transfer->whereBetween('created_at', $lastYear)->sum('amount')),
                ],
                'last' => [
                    'plan' => $invest->whereBetween('created_at', $lastYear)->count(),
                    'amount' => $invest->whereBetween('created_at', $lastYear)->sum('amount'),
                    'profit' => $profit->whereBetween('created_at', $lastYear)->sum('amount'),
                    'transfer' => $transfer->whereBetween('created_at', $lastYear)->sum('amount'),
                ]
            ],
            'all' => [
                'plan' => $invest->count(),
                'amount' => $invest->sum('amount'),
                'profit' => $profit->sum('amount'),
                'transfer' => $transfer->sum('amount'),
            ]
        ];

        return $data;
    }
}
