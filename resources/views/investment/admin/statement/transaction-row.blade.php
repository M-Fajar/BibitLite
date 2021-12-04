@php

use App\Enums\LedgerTnxType;
use App\Enums\TransactionCalcType;

$type = data_get($transaction, 'type');
$badge_class = 'badge-dark';

if(LedgerTnxType::INVEST==$type){
    $badge_class = 'badge-info';
}
if(LedgerTnxType::CAPITAL==$type){
    $badge_class = 'badge-success';
}
if(LedgerTnxType::PROFIT==$type){
    $badge_class = 'badge-success';
}
if(LedgerTnxType::TRANSFER==$type){
    $badge_class = 'badge-warning';
}

@endphp
<div class="nk-tb-col">
    <span class="text-dark">{{ the_tnx($transaction->ivx, 'ivx') }}</span>
</div>

<div class="nk-tb-col tb-col-sm">
    <span>{{ data_get($transaction, 'desc', '-') }}</span>
</div>

<div class="nk-tb-col tb-col-md">
    <span>{{ the_uid($transaction->user_id) }}</span>
    <em class="icon ni ni-info text-soft fs-13px" data-toggle="tooltip" title="{{ $transaction->user->name }} ({{ str_protect($transaction->user->email) }})"></em>
</div>

<div class="nk-tb-col tb-col-sm">
    <span class="badge badge-dot {{ $badge_class }}">
        {{ ucfirst(__($type)) }}
    </span>
</div>

<div class="nk-tb-col text-right">
    <span class="text-dark">{{ calc_sign($transaction->calc) }} {{ money($transaction->amount, base_currency()) }}</span>
</div>