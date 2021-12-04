@php 
    use \App\Enums\TransactionType as dTType;
    use \App\Enums\TransactionStatus as dTStatus;
    use \App\Enums\TransactionCalcType as dTCType;

    $base_currency = base_currency();

    $amount = $tnx->amount;
    $total = $tnx->total;
    $currency = $tnx->currency;

    $tnx_currency = $tnx->tnx_currency;
    $tnx_amount = $tnx->tnx_amount;
    $tnx_total = $tnx->tnx_amount;
    $exchange = $tnx->exchange;

    $completed_by = data_get($tnx, 'completed_by');
    $confirmed_by = data_get($tnx, 'confirmed_by');

    $pay_to_acc_name = '';

    if ($tnx->tnx_method == 'bank-transfer') {
        $pay_to_acc_name = data_get($tnx, 'meta.pay_meta.account_name');
    }
    if ($tnx->tnx_method == 'crypto-wallet') {
        $pay_to_acc_name = get_currency(data_get($tnx, 'meta.currency'), 'name');
    }
    if ($tnx->tnx_method == 'wd-bank-transfer') {
        $pay_to_acc_name = data_get($tnx, 'meta.pay_meta.payment.acc_name');
    }
    if ($tnx->tnx_method == 'wd-paypal') {
        $pay_to_acc_name = data_get($tnx, 'meta.pay_meta.label');
    }
@endphp

<div class="nk-modal-head mb-3 mb-sm-4">
    <h4 class="nk-modal-title title">{{ __('Transaction') }} <small class="text-primary">#{{ the_tnx(data_get($tnx, 'tnx')) }}</small></h4>
</div>
<div class="nk-block">
    <div class="nk-block-between flex-wrap g-3 pb-1">
        <div class="nk-tnx">
            <div class="nk-tnx-type-badge mr-2">
                {!! tnx_type_icon($transaction, 'tnx-type-icon') !!}
            </div>
            <div class="nk-tnx-text">
                <h5 class="title">{{ money($tnx_amount, $tnx_currency) }}</h5>
                <span class="sub-text mt-n1">{{ show_date(data_get($tnx, 'created_at'), true) }}</span>
            </div>
        </div>
        <ul class="align-center flex-wrap gx-3">
            <li>
                <span class="badge badge-sm{{ css_state_tnx($tnx->status, 'badge') }}">
                    {{ ($tnx->type == dTType::INVESTMENT && in_array($tnx->status, [dTStatus::PENDING])) ? __("Locked") : __(ucfirst($tnx->status)) }}
                </span>
            </li>
        </ul>
    </div>
    <div class="divider md stretched"></div>
    <div class="row gy-1">
        <div class="col-md-6">
            <h6 class="overline-title">{{ __('In Account') }}</h6>
            <div class="row gy-1">
                <div class="col-12">
                    <span class="sub-text">{{ __('Amount') }}</span>
                    <span class="caption-text">{{ money($amount, $base_currency) }}</span>
                </div>
                <div class="col-12">
                    <span class="sub-text">{{ __('Total :Type', ['type' => data_get($tnx, 'type')]) }}</span>
                    <span class="caption-text fw-bold">{{ money($total, $base_currency) }}</span>
                </div>
                <div class="col-12">
                    <span class="sub-text">{{ __('Fees') }}</span>
                    <span class="caption-text">{{ money(data_get($tnx, 'fees', '-'), $base_currency) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <h6 class="overline-title">{{ __('In Transaction') }}</h6>
            <div class="row gy-1">
                <div class="col-12">
                    <span class="sub-text">{{ __('Amount') }}</span>
                    <span class="caption-text">{{ money($tnx_amount, $tnx_currency) }}</span>
                </div>
                <div class="col-12">
                    <span class="sub-text">{{ ($tnx->type == dTType::WITHDRAW) ? __('Total Withdraw') : (($tnx->type == dTType::DEPOSIT) ? __('Total Payment') : __('Total Amount') ) }}</span>
                    <span class="caption-text fw-bold">{{ money(data_get($tnx, 'tnx_total', '-'), $tnx_currency) }}</span>
                </div>
                <div class="col-12">
                    <span class="sub-text">{{ __('Exchage Rate') }}</span>
                    <span class="caption-text">{{ __('1 :from = :rate', ['rate' => money($exchange, $tnx_currency), 'from' => $base_currency]) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <span class="sub-text">{{ __('User Account') }}</span>
            <span class="caption-text">
                {{ the_uid($tnx->customer->id) }} 
                <span class="small text-soft nk-tooltip" title="{{ $tnx->customer->name }}"><em class="icon ni ni-info-fill"></em></span>
            </span>
        </div>
        <div class="col-md-6">
            <span class="sub-text">{{ __('User Email') }}</span>
            <span class="caption-text">
                {{ str_protect($tnx->customer->email) }} 
            </span>
        </div>
        @if($base_currency!=$currency) 
        <div class="col-12">
            <div class="note-text mt-2">
                <p class="text-danger mb-1">{{ __("Attention: Current base currency (:system) does not match with this transaction currency (:tnx).", ['system' => $base_currency, 'tnx' => $currency]) }}</p>
                <p>{{ __('Note: System base currency was changed after transaction made. ') }}</p>
            </div>
        </div>
        @endif
    </div>

    <div class="divider md stretched"></div>
    <h6 class="title">{{ __('Order Details') }}</h6>
    <div class="row gy-1">
        <div class="col-md-6">
            <span class="sub-text">{{ __('Order Date') }}</span>
            <span class="caption-text text-break">{{ show_date(data_get($tnx, 'created_at')) }}</span>
        </div>
        <div class="col-md-6">
            <span class="sub-text">{{ __('Placed By') }}</span>
            <span class="caption-text">
                {{ the_uid($tnx->transaction_by->id) }} 
                <span class="small text-soft nk-tooltip" title="{{ $tnx->transaction_by->name . ' ('.str_protect($tnx->transaction_by->email).')' }}"><em class="icon ni ni-info-fill"></em></span>
            </span>
        </div>
        @if(data_get($tnx, 'confirmed_at'))
        <div class="col-md-6">
            <span class="sub-text">{{ __('Confirmed At') }}</span>
            <span class="caption-text text-break">{{ show_date(data_get($tnx, 'confirmed_at'), true) }}</span>
        </div>
        <div class="col-md-6">
            <span class="sub-text">{{ __('Confirmed By') }}</span>
            <span class="caption-text">{!! (isset($confirmed_by['name']) ? $confirmed_by['name'] : '<em class="text-soft small">'. __('Unknown') .'</em>') !!}</span>
        </div>
        @endif
        @if(data_get($tnx, 'completed_at'))
        <div class="col-md-6">
            <span class="sub-text">{{ __('Completed At') }}</span>
            <span class="caption-text text-break">{{ show_date(data_get($tnx, 'completed_at'), true) }}</span>
        </div>

        <div class="col-md-6">
            <span class="sub-text">{{ __('Completed By') }}</span>
            <span class="caption-text">{!! (isset($completed_by['name']) ? $completed_by['name'] : '<em class="text-soft small">'. __('Unknown') .'</em>') !!}</span>
        </div>
        @endif
    </div>
    
    <div class="divider md stretched"></div>
    <h6 class="title">{{ __('Additional Details') }}</h6>
    <div class="row gy-2">
        <div class="col-lg-6">
            <span class="sub-text">{{ __('Transaction Type') }}</span>
            <span class="caption-text">{{ ucfirst(data_get($tnx, 'type')) }}</span>
        </div>
        <div class="col-lg-6">
            <span class="sub-text">{{ __('Payment Gateway') }}</span>
            <span class="caption-text align-center">{{ data_get($tnx, 'method_name') }}
                @if(data_get($tnx, 'is_online') == 1)
                    <span class="badge badge-primary ml-2 text-white">{{ __('Online Gateway') }}</span>
                @endif
            </span>
        </div>

        @if(data_get($tnx, 'pay_from'))
        <div class="col-lg-6">
            <span class="sub-text">{{ __('Payment From') }}</span>
            <span class="caption-text text-break"><span class="small">{{ from_to_case(data_get($tnx, 'pay_from', '~')) }}</span></span>
        </div>
        @endif

        @if(data_get($tnx, 'reference'))
        <div class="col-lg-6">
            <span class="sub-text">{{ __('Reference / Hash') }}</span>
            <span class="caption-text text-break">{{ data_get($tnx, 'reference', '~') }}</span>
        </div>
        @endif

        @if(data_get($tnx, 'pay_to'))
        <div class="col-lg-6">
            <span class="sub-text">{{ __('Payment To') }}
                @if($pay_to_acc_name)
                <small>({{ $pay_to_acc_name }})</small>
                @endif
            </span>
            <span class="caption-text text-break"><span class="small">{{ data_get($tnx, 'pay_to', '~') }}</span></span>
        </div>
        @endif

        @if(data_get($tnx->ledger,'balance'))
        <div class="col-lg-6">
            <span class="sub-text">{{ __('Updated Balance') }}</span>
            <span class="caption-text">{{ money(data_get($tnx->ledger, 'balance'),base_currency()) }}</span>
        </div>
        @endif

        @if(data_get($tnx, 'description'))
        <div class="col-lg-12">
            <span class="sub-text">{{ __('Transaction Details') }}</span>
            <span class="caption-text">{{ data_get($tnx, 'description') }}</span>
        </div>
        @endif

        @if(data_get($tnx, 'remarks'))
        <div class="col-lg-12">
            <span class="sub-text">{{ __('Note by Admin') }}</span>
            <span class="caption-text">{{ data_get($tnx, 'remarks') }}</span>
        </div>
        @endif
    </div>
</div>