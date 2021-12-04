@php 

use \App\Enums\TransactionType;
use \App\Enums\TransactionStatus;

$amount = $transaction->amount;
$currency = $transaction->currency;
$tnx_amount = $transaction->tnx_amount;
$tnx_currency = $transaction->tnx_currency;
$user_amount = money($transaction->tnx_amount, $transaction->tnx_currency);

if($transaction->tnx_currency!=base_currency()) {
    $user_amount = money($transaction->tnx_amount, $transaction->tnx_currency) . ' ('. money($transaction->amount, $transaction->currency). ')';
}

@endphp

<div class="nk-modal-title">
	<h5 class="title">{!! __('Withdraw ID# :orderid', ['type'=> $type, 'orderid' => '<span class="text-primary">'.the_tnx($transaction->tnx).'</span>' ]) !!}</h5>
</div>

<div class="nk-block">
	<p>{!! __("User (:name) request to withdraw :amount via :Method. Please check out the details and send payment to user account below.", ['method' => '<span class="fw-bold text-dark">'.$transaction->method_name.'</span>', 'amount' => '<span class="fw-bold text-dark text-nowrap">'.$user_amount.'</span>', 'name' => '<span class="fw-bold text-dark">'.the_uid($transaction->customer->id).'</span>' ]) !!}</p>

    <div class="divider md stretched"></div>
	<table class="table table-plain table-borderless table-sm mb-0">
		<tr>
			<td><span class="sub-text">{{ __("Withdraw Amount") }}</span></td>
			<td><span class="lead-text">{{ money($tnx_amount, $tnx_currency) }}</span></td>
		</tr>
		<tr>
			<td><span class="sub-text">{{ __("Withdraw Method") }}</span></td>
			<td><span class="lead-text">{{ $transaction->method_name }}</span></td>
		</tr>
		<tr>
			<td><span class="sub-text">{{ __("Withdraw Account") }}</span></td>
			<td><span class="lead-text">{{ data_get($transaction, 'meta.pay_meta.label') }}</span></td>
		</tr>

		<tr>
			<td><span class="sub-text">{{ __("Payment Information") }}</span></td>
			<td>
				@if(data_get($transaction, 'tnx_method') === 'wd-paypal')
					@if(data_get($transaction,'meta.pay_meta.payment'))
					<div class="label lead-text">{{ __("Email Address / PayPal") }}</div>
					<div class="data mb-1">{{ data_get($transaction,'meta.pay_meta.payment') }}</div>
					@endif
				@endif

				@if (data_get($transaction, 'meta.pay_meta.currency'))
				<div class="label lead-text">{{ data_get($transaction, 'tnx_method') === 'wd-crypto' ? __("Wallet Type") : __('Account Currency') }}</div>
				<div class="data mb-1">{{ get_currency(data_get($transaction, 'meta.pay_meta.currency'), 'name') . ' ('.data_get($transaction, 'meta.pay_meta.currency').')' }}</div>
				@endif

				@if (data_get($transaction, 'meta.pay_meta.wallet'))
				<div class="label lead-text">{{ __("Wallet Address") }}</div>
				<div class="data mb-1">{{ data_get($transaction, 'meta.pay_meta.wallet') }}</div>
				@endif

				@if (data_get($transaction, 'meta.pay_meta.payment.acc_name'))
				<div class="label lead-text">{{ __("Account Name") }}</div>
				<div class="data mb-1">{{ data_get($transaction, 'meta.pay_meta.payment.acc_name') }}</div>
				@endif

				@if (data_get($transaction, 'meta.pay_meta.payment.acc_no'))
				<div class="label lead-text">{{ __("Account Number") }}</div>
				<div class="data mb-1">{{ data_get($transaction, 'meta.pay_meta.payment.acc_no') }}</div>
				@endif
				
				@if (data_get($transaction, 'meta.pay_meta.payment.bank_name'))
				<div class="label lead-text">{{ __("Bank Name") }}</div>
				<div class="data mb-1">{{ data_get($transaction, 'meta.pay_meta.payment.bank_name') }}</div>
				@endif
				
				@if (data_get($transaction, 'meta.pay_meta.payment.bank_branch'))
				<div class="label lead-text">{{ __("Branch") }}</div>
				<div class="data mb-1">{{ data_get($transaction, 'meta.pay_meta.payment.bank_branch') }}</div>
				@endif
			</td>
		</tr>
		<tr>
			<td><span class="sub-text">{{ __('Amount to :Calc', ['calc' => $transaction->calc]) }}</span></td>
			<td><span class="lead-text">{{ money($amount, $currency) }}</span></td>
		</tr>
	</table>
    <div class="divider md stretched"></div>
	<form action="{{ route('admin.transaction.update', ['action' => 'confirm', 'uid' => the_hash($transaction->id)]) }}" data-action="update">
		<p>{!! __("Please confirm that you want to PROCCED this :type request.", ['type' => '<span class="fw-bold text-dark">'.strtoupper($type).'</span>']) !!}</p>

		<ul class="align-center flex-nowrap gx-2 py-2">
            <li>
            	<input type="hidden" value="{{ $transaction->tnx }}" name="orderid">
                <input type="hidden" value="{{ TransactionStatus::CONFIRMED }}" name="status">
                <button type="button" class="btn btn-primary m-tnx-update" data-confirm="yes" data-state="{{ TransactionStatus::CONFIRMED }}">{!! __('Procced Withdraw') !!}</button>
            </li>
            <li>
                <button data-dismiss="modal" type="button" class="btn btn-trans btn-light">{{ __('Cancel') }}</button>
            </li>
        </ul>
	</form>

    <div class="divider md stretched"></div>
    <div class="notes">
        <ul>
            <li class="alert-note is-plain">
                <em class="icon ni ni-info"></em>
                <p>{{ __("You able to complete the withdraw after confirm the withdraw request.") }}</p>
            </li>
            <li class="alert-note is-plain text-danger">
                <em class="icon ni ni-alert"></em>
                <p>{{ __("User unable to cancel the withdraw request once you have confirmed.") }}</p>
            </li>
        </ul>
    </div>
</div>
